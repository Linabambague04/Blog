<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MCPService
{
    protected $tools = [];

    public function __construct()
    {
        // Definir herramientas MCP disponibles
        $this->tools = [
            [
                'type' => 'function',
                'function' => [
                    'name' => 'query_database',
                    'description' => 'Ejecuta una consulta SQL SELECT en la base de datos',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'query' => [
                                'type' => 'string',
                                'description' => 'La consulta SQL SELECT a ejecutar (solo lectura)'
                            ],
                        ],
                        'required' => ['query']
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'insert_record',
                    'description' => 'Inserta un nuevo registro en una tabla',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'table' => [
                                'type' => 'string',
                                'description' => 'Nombre de la tabla'
                            ],
                            'data' => [
                                'type' => 'object',
                                'description' => 'Objeto con los datos a insertar (clave: valor)'
                            ],
                        ],
                        'required' => ['table', 'data']
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'update_record',
                    'description' => 'Actualiza registros en una tabla',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'table' => [
                                'type' => 'string',
                                'description' => 'Nombre de la tabla'
                            ],
                            'data' => [
                                'type' => 'object',
                                'description' => 'Datos a actualizar (clave: valor)'
                            ],
                            'where' => [
                                'type' => 'object',
                                'description' => 'Condiciones WHERE (clave: valor)'
                            ],
                        ],
                        'required' => ['table', 'data', 'where']
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'delete_record',
                    'description' => 'Elimina registros de una tabla',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'table' => [
                                'type' => 'string',
                                'description' => 'Nombre de la tabla'
                            ],
                            'where' => [
                                'type' => 'object',
                                'description' => 'Condiciones WHERE para identificar registros a eliminar'
                            ],
                        ],
                        'required' => ['table', 'where']
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_table_schema',
                    'description' => 'Obtiene el esquema de una tabla de la base de datos',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'table_name' => [
                                'type' => 'string',
                                'description' => 'Nombre de la tabla'
                            ],
                        ],
                        'required' => ['table_name']
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'list_tables',
                    'description' => 'Lista todas las tablas disponibles en la base de datos',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => []
                    ]
                ]
            ]
        ];
    }

    public function chat(array $messages)
    {
        $maxIterations = 5;
        $iteration = 0;

        while ($iteration < $maxIterations) {
            $iteration++;

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => 'deepseek/deepseek-chat-v3.1:free',
                'messages' => $messages,
                'tools' => $this->tools,
                'tool_choice' => 'auto',
            ]);

            if (!$response->successful()) {
                Log::error('Error MCP', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [
                    'success' => false,
                    'error' => $response->body(),
                ];
            }

            $data = $response->json();
            Log::info('Respuesta completa del modelo', ['response' => $data]);
            $message = $data['choices'][0]['message'] ?? null;


            if (!$message) {
                return [
                    'success' => false,
                    'error' => 'No message in response',
                ];
            }

            $messages[] = $message;

            if (isset($message['tool_calls']) && count($message['tool_calls']) > 0) {
                foreach ($message['tool_calls'] as $toolCall) {
                    $functionName = $toolCall['function']['name'];
                    $functionArgs = json_decode($toolCall['function']['arguments'], true);

                    $result = $this->executeFunction($functionName, $functionArgs);

                    $messages[] = [
                        'role' => 'tool',
                        'tool_call_id' => $toolCall['id'],
                        'content' => json_encode($result),
                    ];
                }

                continue;
            }

            return [
                'success' => true,
                'reply' => $message['content'] ?? 'Sin respuesta.',
            ];
        }

        return [
            'success' => false,
            'error' => 'Max iterations reached',
        ];
    }

    protected function executeFunction(string $name, array $args)
    {
        try {
            switch ($name) {
                case 'query_database':
                    return $this->queryDatabase($args['query'] ?? '');

                case 'insert_record':
                    return $this->insertRecord($args['table'] ?? '', $args['data'] ?? []);

                case 'update_record':
                    return $this->updateRecord(
                        $args['table'] ?? '',
                        $args['data'] ?? [],
                        $args['where'] ?? []
                    );

                case 'delete_record':
                    return $this->deleteRecord($args['table'] ?? '', $args['where'] ?? []);

                case 'get_table_schema':
                    return $this->getTableSchema($args['table_name'] ?? '');

                case 'list_tables':
                    return $this->listTables();

                default:
                    return ['error' => 'Function not found'];
            }
        } catch (\Exception $e) {
            Log::error('Error executing function', [
                'function' => $name,
                'error' => $e->getMessage()
            ]);

            return ['error' => $e->getMessage()];
        }
    }

    protected function queryDatabase(string $query)
    {
        if (!preg_match('/^\s*SELECT/i', $query)) {
            return ['error' => 'Solo se permiten consultas SELECT'];
        }

        $query = rtrim($query, ';') . ' LIMIT 100';

        try {
            $results = DB::select($query);
            return [
                'success' => true,
                'data' => $results,
                'count' => count($results)
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    protected function insertRecord(string $table, array $data)
    {
        if (empty($table) || empty($data)) {
            return ['error' => 'Tabla y datos son requeridos'];
        }

        // Lista blanca de tablas permitidas (IMPORTANTE: ajusta según tu app)
        $allowedTables = ['users', 'posts'];

        if (!in_array($table, $allowedTables)) {
            return ['error' => 'No tienes permiso para insertar en esta tabla'];
        }

        try {
            // Agregar timestamps si la tabla los usa
            if (!isset($data['created_at'])) {
                $data['created_at'] = now();
            }
            if (!isset($data['updated_at'])) {
                $data['updated_at'] = now();
            }

            $id = DB::table($table)->insertGetId($data);

            return [
                'success' => true,
                'message' => 'Registro insertado correctamente',
                'id' => $id
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    protected function updateRecord(string $table, array $data, array $where)
    {
        if (empty($table) || empty($data) || empty($where)) {
            return ['error' => 'Tabla, datos y condiciones son requeridos'];
        }

        $allowedTables = ['users', 'posts'];

        if (!in_array($table, $allowedTables)) {
            return ['error' => 'No tienes permiso para actualizar esta tabla'];
        }

        try {
            // Agregar updated_at
            $data['updated_at'] = now();

            $query = DB::table($table);

            foreach ($where as $column => $value) {
                $query->where($column, $value);
            }

            $affected = $query->update($data);

            return [
                'success' => true,
                'message' => 'Registro(s) actualizado(s) correctamente',
                'affected_rows' => $affected
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    protected function deleteRecord(string $table, array $where)
    {
        if (empty($table) || empty($where)) {
            return ['error' => 'Tabla y condiciones son requeridos'];
        }

        $allowedTables = ['users', 'posts'];

        if (!in_array($table, $allowedTables)) {
            return ['error' => 'No tienes permiso para eliminar de esta tabla'];
        }

        try {
            $query = DB::table($table);

            foreach ($where as $column => $value) {
                $query->where($column, $value);
            }

            $affected = $query->delete();

            return [
                'success' => true,
                'message' => 'Registro(s) eliminado(s) correctamente',
                'affected_rows' => $affected
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    protected function getTableSchema(string $tableName)
    {
        try {
            $columns = DB::select("DESCRIBE {$tableName}");

            return [
                'success' => true,
                'table' => $tableName,
                'columns' => $columns
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    protected function listTables()
    {
        try {
            $tables = DB::select('SHOW TABLES');

            return [
                'success' => true,
                'tables' => $tables
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
