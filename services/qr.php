<?php
require_once __DIR__ . '/../components/phpqrcode/phpqrcode.php';

if (!isset($_GET['data'])) {
    http_response_code(400);
    exit('Falta el parámetro data');
}

$data = $_GET['data'];

$level = QR_ECLEVEL_H;  
$size = 10;             
$margin = 2;   
	

header('Content-Type: image/png');
header('Cache-Control: no-cache, no-store, must-revalidate');

QRcode::png($data, null, $level, $size, $margin);
?>