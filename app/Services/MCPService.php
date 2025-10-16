<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MCPService
{
    protected $tools = [];
    protected $context = [];

    public function __construct()
    {
        $this->tools = [
            [
                'type' => 'function',
                'function' => [
                    'name' => 'query_database',
                    'description' => 'Ejecuta una consulta SQL SELECT en la base de datos para obtener información',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'query' => [
                                'type' => 'string',
                                'description' => 'La consulta SQL SELECT a ejecutar (solo lectura). Ejemplo: SELECT * FROM posts ORDER BY created_at DESC'
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
                    'description' => 'Inserta un nuevo registro en la tabla posts. NO necesitas incluir user_id, session_id, created_at o updated_at',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'table' => [
                                'type' => 'string',
                                'description' => 'Nombre de la tabla (ejemplo: "posts")',
                                'enum' => ['posts', 'users']
                            ],
                            'data' => [
                                'type' => 'object',
                                'description' => 'Datos a insertar. Para posts usa: {"title": "tu titulo", "content": "tu contenido"}',
                                'properties' => [
                                    'title' => ['type' => 'string'],
                                    'content' => ['type' => 'string']
                                ]
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
                    'description' => 'Actualiza registros existentes en una tabla',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'table' => [
                                'type' => 'string',
                                'description' => 'Nombre de la tabla',
                                'enum' => ['posts', 'users']
                            ],
                            'data' => [
                                'type' => 'object',
                                'description' => 'Datos a actualizar (clave: valor)'
                            ],
                            'where' => [
                                'type' => 'object',
                                'description' => 'Condiciones WHERE (clave: valor). Ejemplo: {"id": 1}'
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
                                'description' => 'Nombre de la tabla',
                                'enum' => ['posts', 'users']
                            ],
                            'where' => [
                                'type' => 'object',
                                'description' => 'Condiciones WHERE para identificar registros. Ejemplo: {"id": 1}'
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
                    'description' => 'Obtiene la estructura/esquema de una tabla',
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

    public function chat(array $messages, array $context = [])
    {
        $this->context = $context;
        $maxIterations = 10; // Aumentar para permitir más interacciones
        $iteration = 0;

        while ($iteration < $maxIterations) {
            $iteration++;
            
            Log::info("Iteración {$iteration} del chat MCP", [
                'mensaje_count' => count($messages)
            ]);

            try {
                $response = Http::timeout(30)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
                        'Content-Type' => 'application/json',
                    ])
                    ->post('https://router.huggingface.co/nebius/v1/chat/completions', [
                        'model' => 'meta-llama/Meta-Llama-3.1-8B-Instruct-fast',
                        'messages' => $messages,
                        'tools' => $this->tools,
                        'tool_choice' => 'auto',
                        'temperature' => 0.7,
                    ]);

                if (!$response->successful()) {
                    Log::error('Error en llamada a API', [
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);

                    return [
                        'success' => false,
                        'error' => 'Error de API: ' . $response->status(),
                    ];
                }

                $data = $response->json();
                $message = $data['choices'][0]['message'] ?? null;

                if (!$message) {
                    Log::error('No hay mensaje en la respuesta', ['data' => $data]);
                    return [
                        'success' => false,
                        'error' => 'Respuesta inválida del modelo',
                    ];
                }

                // Agregar mensaje del asistente al historial
                $messages[] = $message;

                // Si hay llamadas a herramientas, ejecutarlas
                if (isset($message['tool_calls']) && count($message['tool_calls']) > 0) {
                    Log::info('Herramientas llamadas', [
                        'count' => count($message['tool_calls']),
                        'tools' => array_map(fn($t) => $t['function']['name'], $message['tool_calls'])
                    ]);

                    foreach ($message['tool_calls'] as $toolCall) {
                        $functionName = $toolCall['function']['name'];
                        $functionArgs = json_decode($toolCall['function']['arguments'], true);

                        Log::info('Ejecutando función', [
                            'function' => $functionName,
                            'args' => $functionArgs
                        ]);

                        $result = $this->executeFunction($functionName, $functionArgs);

                        Log::info('Resultado de función', [
                            'function' => $functionName,
                            'result' => $result
                        ]);

                        // Agregar resultado al historial
                        $messages[] = [
                            'role' => 'tool',
                            'tool_call_id' => $toolCall['id'],
                            'content' => json_encode($result, JSON_UNESCAPED_UNICODE),
                        ];
                    }

                    // Continuar el bucle para obtener la respuesta final
                    continue;
                }

                // Si no hay tool_calls, tenemos la respuesta final
                $finalContent = $message['content'] ?? '';
                
                Log::info('Respuesta final del asistente', [
                    'content' => $finalContent,
                    'iterations' => $iteration
                ]);

                return [
                    'success' => true,
                    'reply' => $finalContent ?: 'He procesado tu solicitud.',
                ];

            } catch (\Exception $e) {
                Log::error('Excepción en chat MCP', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return [
                    'success' => false,
                    'error' => 'Error interno: ' . $e->getMessage(),
                ];
            }
        }

        Log::warning('Se alcanzó el máximo de iteraciones');
        
        return [
            'success' => false,
            'error' => 'Se alcanzó el límite de iteraciones',
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
                    return ['error' => 'Función no encontrada: ' . $name];
            }
        } catch (\Exception $e) {
            Log::error('Error ejecutando función', [
                'function' => $name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return ['error' => $e->getMessage()];
        }
    }

    protected function queryDatabase(string $query)
    {
        $query = trim($query);
        
        if (empty($query)) {
            return ['error' => 'La consulta no puede estar vacía'];
        }

        if (!preg_match('/^\s*SELECT/i', $query)) {
            return ['error' => 'Solo se permiten consultas SELECT por seguridad'];
        }

        // Agregar LIMIT si no existe
        if (!preg_match('/LIMIT\s+\d+/i', $query)) {
            $query = rtrim($query, ';') . ' LIMIT 100';
        }

        try {
            $results = DB::select($query);
            
            return [
                'success' => true,
                'data' => $results,
                'count' => count($results),
                'message' => 'Consulta ejecutada correctamente'
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Error en consulta SQL: ' . $e->getMessage()
            ];
        }
    }

    protected function insertRecord(string $table, array $data)
    {
        if (empty($table)) {
            return ['error' => 'El nombre de la tabla es requerido'];
        }

        if (empty($data)) {
            return ['error' => 'Los datos a insertar son requeridos'];
        }

        $allowedTables = ['users', 'posts'];

        if (!in_array($table, $allowedTables)) {
            return ['error' => "No tienes permiso para insertar en la tabla '{$table}'"];
        }

        try {
            // Agregar contexto automáticamente para posts
            if ($table === 'posts') {
                if (!empty($this->context['user_id'])) {
                    $data['user_id'] = $this->context['user_id'];
                } elseif (!empty($this->context['session_id'])) {
                    $data['session_id'] = $this->context['session_id'];
                }
            }

            // Agregar timestamps
            $data['created_at'] = $data['created_at'] ?? now();
            $data['updated_at'] = $data['updated_at'] ?? now();

            $id = DB::table($table)->insertGetId($data);

            return [
                'success' => true,
                'message' => "Registro insertado correctamente en '{$table}'",
                'id' => $id,
                'data' => $data
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Error al insertar: ' . $e->getMessage()
            ];
        }
    }

    protected function updateRecord(string $table, array $data, array $where)
    {
        if (empty($table) || empty($data) || empty($where)) {
            return ['error' => 'Tabla, datos y condiciones WHERE son requeridos'];
        }

        $allowedTables = ['users', 'posts'];

        if (!in_array($table, $allowedTables)) {
            return ['error' => "No tienes permiso para actualizar la tabla '{$table}'"];
        }

        try {
            $data['updated_at'] = now();

            $query = DB::table($table);

            foreach ($where as $column => $value) {
                $query->where($column, $value);
            }

            $affected = $query->update($data);

            return [
                'success' => true,
                'message' => "Registro(s) actualizado(s) en '{$table}'",
                'affected_rows' => $affected
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Error al actualizar: ' . $e->getMessage()
            ];
        }
    }

    protected function deleteRecord(string $table, array $where)
    {
        if (empty($table) || empty($where)) {
            return ['error' => 'Tabla y condiciones WHERE son requeridos'];
        }

        $allowedTables = ['users', 'posts'];

        if (!in_array($table, $allowedTables)) {
            return ['error' => "No tienes permiso para eliminar de la tabla '{$table}'"];
        }

        try {
            $query = DB::table($table);

            foreach ($where as $column => $value) {
                $query->where($column, $value);
            }

            $affected = $query->delete();

            return [
                'success' => true,
                'message' => "Registro(s) eliminado(s) de '{$table}'",
                'affected_rows' => $affected
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Error al eliminar: ' . $e->getMessage()
            ];
        }
    }

    protected function getTableSchema(string $tableName)
    {
        if (empty($tableName)) {
            return ['error' => 'El nombre de la tabla es requerido'];
        }

        try {
            $columns = DB::select("DESCRIBE {$tableName}");

            return [
                'success' => true,
                'table' => $tableName,
                'columns' => $columns
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Error al obtener esquema: ' . $e->getMessage()
            ];
        }
    }

    protected function listTables()
    {
        try {
            $tables = DB::select('SHOW TABLES');

            return [
                'success' => true,
                'tables' => $tables,
                'count' => count($tables)
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Error al listar tablas: ' . $e->getMessage()
            ];
        }
    }
}