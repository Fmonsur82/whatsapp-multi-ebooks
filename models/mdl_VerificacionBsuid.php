<?php
include_once __DIR__ . '/../config/conexion.php';

class VerificacionBsuid {
    public const POLITICA_URL = 'https://digital-content.co/politica-de-datos/';
    public const POLITICA_VERSION = 'v3-202607';
    public const TYC_URL = 'https://wsp-multi.dcsing.com/view/terminos-y-condiciones/';
    public const TYC_VERSION = 'v2-202609';

    public function crearToken(string $bsuid): string {
        $conexion = Conexion::getConexion();
        $stmt = $conexion->prepare('UPDATE agentes_whatsapp_bsuid_verificaciones SET revocado_en = NOW() WHERE bsuid = ? AND usado_en IS NULL AND revocado_en IS NULL');
        if (!$stmt) {
            throw new RuntimeException('No fue posible preparar la revocación del token');
        }
        $stmt->bind_param('s', $bsuid);
        $stmt->execute();
        $stmt->close();

        $token = bin2hex(random_bytes(32));
        $hash = hash('sha256', $token);
        $stmt = $conexion->prepare('INSERT INTO agentes_whatsapp_bsuid_verificaciones (bsuid, token_hash) VALUES (?, ?)');
        if (!$stmt) {
            throw new RuntimeException('No fue posible preparar el token');
        }
        $stmt->bind_param('ss', $bsuid, $hash);
        if (!$stmt->execute()) {
            $stmt->close();
            throw new RuntimeException('No fue posible guardar el token');
        }
        $stmt->close();
        return $token;
    }

    public function obtenerToken(string $token): ?array {
        $hash = hash('sha256', $token);
        $conexion = Conexion::getConexion();
        $stmt = $conexion->prepare('SELECT id, bsuid FROM agentes_whatsapp_bsuid_verificaciones WHERE token_hash = ? AND usado_en IS NULL AND revocado_en IS NULL LIMIT 1');
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param('s', $hash);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado ? $resultado->fetch_assoc() : null;
        $stmt->close();
        return $fila ?: null;
    }

    public function verificarYAsociar(string $token, string $telefono, string $dispositivo, string $ip): array {
        $tokenData = $this->obtenerToken($token);
        if (!$tokenData) {
            return ['ok' => false, 'codigo' => 'token_invalido', 'mensaje' => 'El enlace ya fue utilizado, revocado o no es válido.'];
        }

        $conexion = Conexion::getConexion();
        $stmt = $conexion->prepare('SELECT id, bsuid FROM usuarios_multi_cliente WHERE telefono = ? LIMIT 1');
        if (!$stmt) {
            return ['ok' => false, 'codigo' => 'error', 'mensaje' => 'No fue posible consultar el número.'];
        }
        $stmt->bind_param('s', $telefono);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuario = $resultado ? $resultado->fetch_assoc() : null;
        $stmt->close();

        if (!$usuario) {
            return ['ok' => false, 'codigo' => 'no_registrado', 'mensaje' => 'No encontramos ese número. Puedes iniciar el registro normal.'];
        }

        if (!empty($usuario['bsuid']) && !hash_equals((string) $usuario['bsuid'], (string) $tokenData['bsuid'])) {
            return ['ok' => false, 'codigo' => 'conflicto', 'mensaje' => 'El número ya está asociado a otra identidad de WhatsApp. Contacta con soporte.'];
        }

        $conexion->begin_transaction();
        try {
            $stmt = $conexion->prepare('UPDATE usuarios_multi_cliente SET bsuid = ?, acepta_pol_priv = 1, acepta_tyc = 1, acepta_fecha_hora = NOW(), acepta_dispositivo = ?, acepta_ip = ?, acepta_pol_priv_version = ?, acepta_tyc_version = ? WHERE id = ? AND (bsuid IS NULL OR bsuid = ? OR bsuid = \'\')');
            if (!$stmt) {
                throw new RuntimeException('No fue posible preparar la actualización');
            }
            $bsuid = $tokenData['bsuid'];
            $versionPolitica = self::POLITICA_VERSION;
            $versionTyc = self::TYC_VERSION;
            $idUsuario = (int) $usuario['id'];
            $vacio = '';
            $stmt->bind_param('sssssis', $bsuid, $dispositivo, $ip, $versionPolitica, $versionTyc, $idUsuario, $vacio);
            if (!$stmt->execute()) {
                throw new RuntimeException('No fue posible asociar el BSUID');
            }
            $stmt->close();

            $stmt = $conexion->prepare('UPDATE agentes_whatsapp_bsuid_verificaciones SET usado_en = NOW() WHERE id = ? AND usado_en IS NULL AND revocado_en IS NULL');
            if (!$stmt) {
                throw new RuntimeException('No fue posible consumir el token');
            }
            $idToken = (int) $tokenData['id'];
            $stmt->bind_param('i', $idToken);
            if (!$stmt->execute() || $stmt->affected_rows !== 1) {
                throw new RuntimeException('No fue posible consumir el token');
            }
            $stmt->close();
            $conexion->commit();
            return ['ok' => true, 'codigo' => 'verificado', 'mensaje' => 'Tu cuenta fue verificada. Regresa a WhatsApp y envía nuevamente tu consulta.'];
        } catch (Throwable $e) {
            $conexion->rollback();
            error_log('No fue posible completar la verificación BSUID: ' . $e->getMessage());
            return ['ok' => false, 'codigo' => 'error', 'mensaje' => 'No fue posible completar la verificación. Intenta nuevamente.'];
        }
    }
}
