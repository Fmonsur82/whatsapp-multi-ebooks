<?php
require_once "global.php";

class Conexion {
    private static $conexion = null;

    private function __construct() {}

    public static function getConexion() {
        if (self::$conexion === null) {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
            $caller = isset($trace[1]) ? ($trace[1]['file'] . ':' . $trace[1]['line']) : 'Desconocido';

            $logFile = __DIR__ . "/../logs/db_debug.log";
            if (!file_exists(dirname($logFile))) {
                mkdir(dirname($logFile), 0777, true);
            }
            file_put_contents($logFile, "[DB] Conexión creada en $caller - " . date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND);

            self::$conexion = new mysqli('p:' . DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
            self::$conexion->set_charset(DB_ENCODE);

            if (self::$conexion->connect_errno) {
                file_put_contents($logFile, "[DB] ERROR de conexión: " . self::$conexion->connect_error . PHP_EOL, FILE_APPEND);
                printf("Fallo de conexión: %s\n", self::$conexion->connect_error);
                exit();
            }
        }
        return self::$conexion;
    }
}

if (!function_exists('ejecutarConsulta')) {
    function ejecutarConsulta($sql) {
        $conexion = Conexion::getConexion();
        return $conexion->query($sql);
    }
    
    function ejecutarConsultaSimpleFila($sql) {
        $conexion = Conexion::getConexion();
        $query = $conexion->query($sql);
        $row = $query->fetch_assoc();
        return $row;
    }

    function ejecutarConsulta_retornaID($sql) {
        $conexion = Conexion::getConexion();
        $conexion->query($sql);
        return $conexion->insert_id;
    }

    function limpiarCadena($str) {
        $conexion = Conexion::getConexion();
        $str = mysqli_real_escape_string($conexion, trim($str));
        return htmlspecialchars($str);
    }
}
?>
