<?php
require_once '../models/mdl_VerificacionBsuid.php';

$modelo = new VerificacionBsuid();
header('Content-Type: application/json; charset=utf-8');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || ($_GET['op'] ?? '') !== 'verificar') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'mensaje' => 'Método no permitido']);
    exit();
}

$token = trim((string) ($_POST['token'] ?? ''));
$telefono = preg_replace('/\D+/', '', (string) ($_POST['telefono'] ?? ''));
$politica = ($_POST['acepta_pol_priv'] ?? '') === '1';
$tyc = ($_POST['acepta_tyc'] ?? '') === '1';

if ($token === '' || !preg_match('/^\d{8,15}$/', $telefono) || !$politica || !$tyc) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'mensaje' => 'Confirma un número válido y acepta ambos documentos para continuar.']);
    exit();
}

$dispositivo = substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? 'No informado'), 0, 500);
$ip = filter_var($_SERVER['REMOTE_ADDR'] ?? '', FILTER_VALIDATE_IP) ?: '';
$respuesta = $modelo->verificarYAsociar($token, $telefono, $dispositivo, $ip);

if (($respuesta['codigo'] ?? '') === 'no_registrado') {
    $respuesta['url_registro'] = 'https://wsp-multi.dcsing.com/view/instituciones/';
}
echo json_encode($respuesta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
