<?php 
include_once 'config/conexion.php';

class Webhook{
    private $apikey;

    public function __construct() {
        $this->apikey = OPENAI_API_KEY;
    }

    public function logEnRaw($entrada) {
        $dir = __DIR__ . '/../logs';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $path = $dir . '/raw_log.txt';
        $archivoRaw = fopen($path, 'ab');
        if ($archivoRaw === false) {
            error_log("No se pudo abrir log: $path");
            return;
        }
        fwrite($archivoRaw, json_encode($entrada, JSON_UNESCAPED_UNICODE) . PHP_EOL . PHP_EOL);
        fclose($archivoRaw);
    }

    public function LogWebhook($telefono, $mensaje) {
        $dir = __DIR__ . '/../logs';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $path = $dir . '/logs_webhook.txt';
        $fh = fopen($path, 'ab');
        if ($fh === false) {
            error_log("No se pudo abrir log: $path");
            return;
        }
        $entrada = date('Y-m-d H:i:s') . " - " . $telefono . " - " . $mensaje;
        fwrite($fh, $entrada . PHP_EOL);
        fclose($fh);
    }

    public function enviador($plantilla){
        $bearer = 'EAARZAY5FgyuABO7NB9V8EQIp76n4TUTzDIc4OzzoU9vyhBzqgI6JEiRInTTFUWKGaZBDAZC4Snx5r9s4lXK7Hl2mmAGwiZBHlrVgN8F9E1W1qm6LWtcddworNLRckKflpKm6M9gID4ex2IRYQZAETH0LOEZBb8mZBSOzunewJhaBskcJGqhk7JhAGPnOF1pSS0BsgZDZD';
        $options = [
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-type: application/json\r\nAuthorization: Bearer $bearer\r\n",
                'content' => $plantilla,
                'ignore_errors' => true
            ]
        ];
        $context = stream_context_create($options);
        $response = file_get_contents('https://graph.facebook.com/v22.0/706639455864533/messages', false, $context);
        $statusLine = $http_response_header[0] ?? '';
        preg_match('#HTTP/\d+\.\d+\s+(\d+)#', $statusLine, $m);
        $httpCode = isset($m[1]) ? (int)$m[1] : null;
        return [
            'http_code' => $httpCode,
            'raw' => $response,
            'json' => json_decode($response, true),
            'headers' => $http_response_header ?? []
        ];
    }

    private function camposDestinatario($destino) {
        if (is_array($destino)) {
            $valor = $destino['valor'] ?? null;
            if (($destino['tipo'] ?? '') === 'bsuid' && $valor) {
                return ['recipient' => $valor];
            }
            if (($destino['tipo'] ?? '') === 'telefono' && $valor) {
                return ['to' => $valor];
            }
        }

        return $destino ? ['to' => $destino] : [];
    }

    public function plantillaBienvenida($destino){
        $data = json_encode(array_merge([
            "messaging_product" => "whatsapp",
            "type" => "template",
            "template" => [
                "name" => "notificacion_usuario",
                "language" => [
                    "code" => "es_CO"
                ]
            ]
        ], $this->camposDestinatario($destino)));
        return $data;
    }

    public function responderPlantilla($destino,$plantilla){
        $data = json_encode(array_merge([
            "messaging_product" => "whatsapp",
            "type" => "template",
            "template" => [
                "name" => $plantilla,
                "language" => [
                    "code" => "es_CO"
                ]
            ]
        ], $this->camposDestinatario($destino)));
        return $data;
    }

    public function plantillaTexto($destino,$mensaje){
        $data = json_encode(array_merge([
            "messaging_product"=> "whatsapp",    
            "recipient_type"=> "individual",
            "type"=> "text",
            "text"=> [
                "preview_url"=> false,
                "body"=> $mensaje
            ]
        ], $this->camposDestinatario($destino)));
        return $data;
    }

    // public function plantillaRespuestaSaludo($num_telefono){
    //     $data = json_encode([
    //         "messaging_product"=> "whatsapp",    
    //         "recipient_type"=> "individual",
    //         "to"=> $num_telefono,
    //         "type"=> "text",
    //         "text"=> [
    //             "preview_url"=> false,
    //             "body"=> "¡Hola! escribe tu término de búsqueda, te recomiendo que sea una palabra o frase corta 
    //             para que los resultados de la búsqueda sean más acertados"
    //         ]
    //     ]);
    //     return $data;
    // }

    public function plantillaPortada($destino,$url_image,$url_libro){
        $data = json_encode(array_merge([
            "messaging_product"=> "whatsapp",    
            "recipient_type"=> "individual",
            "type"=> "image",
            "image"=> [
                "link"=> $url_image,
                "caption" => $url_libro
            ]
        ], $this->camposDestinatario($destino)));
        return $data;
    }
    

    public function clasificarMensaje($mensajeUsuario) {
        $messages = [
            [
                'role' => 'system',
                'content' =>
                    "Eres un CLASIFICADOR de intención. Debes elegir SOLO un código:\n" .
                    "1 = saludo/cortesía sin solicitud (hola, buenos días, gracias, emoji, etc.)\n" .
                    "2 = solicitud de eliminar/cerrar/desactivar cuenta/perfil/usuario\n" .
                    "3 = solicitud de información sobre términos y condiciones, T&C, TyC, privacidad, tratamiento de datos\n" .
                    "4 = cualquier otro caso o si hay duda.\n\n" .
                    "Reglas:\n" .
                    "- Devuelve SIEMPRE el código usando la herramienta 'clasificar_intencion'.\n" .
                    "- Si hay ambigüedad o mezcla de intenciones, usa 4.\n" .
                    "- No escribas texto fuera del tool call."
            ],
            [
                'role' => 'user',
                'content' => (string)$mensajeUsuario
            ]
        ];
        $data = [
            'model' => 'gpt-4-turbo',
            'messages' => $messages,
            'temperature' => 0,
            'top_p' => 1,
            'max_tokens' => 30,
            'tools' => [
                [
                    'type' => 'function',
                    'function' => [
                        'name' => 'clasificar_intencion',
                        'description' => 'Devuelve el código de intención (1=saludo, 2=eliminar cuenta, 3=TyC, 4=otro).',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'codigo' => [
                                    'type' => 'integer',
                                    'enum' => [1, 2, 3, 4]
                                ]
                            ],
                            'required' => ['codigo'],
                            'additionalProperties' => false
                        ]
                    ]
                ]
            ],
            'tool_choice' => [
                'type' => 'function',
                'function' => ['name' => 'clasificar_intencion']
            ]
        ];
        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apikey
        ]);
        $response = curl_exec($ch);
        if ($response === false) {
            return ['codigo' => 4, 'error' => 'Curl error: ' . curl_error($ch)];
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $responseArr = json_decode($response, true);
        if ($httpCode < 200 || $httpCode >= 300) {
            return ['codigo' => 4, 'error' => $responseArr['error']['message'] ?? ('Error HTTP ' . $httpCode)];
        }
        $args = $responseArr['choices'][0]['message']['tool_calls'][0]['function']['arguments'] ?? null;
        if ($args) {
            $parsed = json_decode($args, true);
            $codigo = $parsed['codigo'] ?? 4;
            if (!in_array($codigo, [1,2,3,4], true)) $codigo = 4;
            return ['codigo' => $codigo];
        }
        return ['codigo' => 4];
    }

    public function consultarLibros($num_telefono,$termino,$idclientemeta) {
        $encoded_search_term = urlencode($termino);
        $url = "https://csdcapi.azurewebsites.net/librosearch/?opensearch=".$encoded_search_term."&page=1&limit=3&sort=rating&portada=0&sindice=0&idcliente=".$idclientemeta;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data_obj = curl_exec($ch);
        curl_close($ch);
        return $data_obj;
    }

    public function nuevoMensajeConversacion($id_usuario,$actor,$mensaje,$fecha_hora){
        $sql = "INSERT INTO conversacion_mensajes(id_usuario,actor,mensaje,fecha_hora)
        VALUES('$id_usuario','$actor','$mensaje','$fecha_hora')";
        return ejecutarConsulta($sql);
    }

    public function validarUsuario($telefono) {
        $cacheFile = __DIR__ . '/../cache/user_' . $telefono . '.json';
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 86400) {
            return json_decode(file_get_contents($cacheFile), true);
        }
        $sql = "SELECT umc.id AS id_usuario, umc.telefono, c.id_cliente, c.nombre AS nombre_cliente, c.url_acceso
        FROM usuarios_multi_cliente umc
        INNER JOIN cliente c ON c.id_cliente = umc.id_cliente
        WHERE telefono = '$telefono'";
        $data = ejecutarConsultaSimpleFila($sql);
        if ($data) {
            file_put_contents($cacheFile, json_encode($data));
        }
        return $data;
    }

    public function usrNoRegistrados($telefono,$fecha_hora){
        $sql = "INSERT INTO usr_no_registrados(no_celular,fecha_hora)VALUES('$telefono','$fecha_hora')";
        return ejecutarConsulta($sql);
    }

    public function busquedaGeneral($url_acceso,$termino){
        $terminoEncode = rawurlencode($termino);
        $mensaje = '🔎 📚 Puedes encontrar todo lo relacionado a tu búsqueda en el siguente enlace: ';
        return $mensaje.$url_acceso.'?sos='.$terminoEncode;
    }

}

?>
