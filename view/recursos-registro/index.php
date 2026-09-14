<?php  
if (isset($_GET['token']) && !empty($_GET['token'])) {
	$cliente = json_decode(base64_decode($_GET['token']));
}else{
	echo "ERROR: Token no encontrado";
	exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<link rel="icon" type="image/ico" href="https://dcsing.com/recursos/corporativa/favicon.ico">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Registra tu número</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
	<style type="text/css">
		.img-banner {
			width: 100%;
			max-height: 120px;
			object-fit: contain;
		}
	</style>
</head>
<body>
	<div class="container mt-4">
		<input type="hidden" id="id_cliente" name="id_cliente" value="<?php echo $cliente->id_cliente ?>">

		<div class="row justify-content-md-center">
			<div class="col-lg-3 col-md-5 col-sm-12 text-center">
				<img id="logo_cliente" alt="Logo cliente" class="img-banner">
			</div>
		</div>

		<div class="row justify-content-md-center mt-3">
			<div class="col-lg-4 col-md-8 col-sm-12 text-center">
				<img src="../../public/img/banner.png" alt="Ebooks7-24" class="img-banner">
			</div>
		</div>
		<div class="row justify-content-md-center">

			<div class="col-lg-12 col-md-12 col-sm-12 mt-4 text-center">
				<h5>Registra tu número de celular</h5>
				<div class="text-center w-100">
					<canvas id="qrcode" class="img-fluid"></canvas>
				</div>
			</div>
		</div>

		<div class="row justify-content-md-center mb-5">
			<div class="col-lg-3 col-md-12 col-sm-12 text-center">
				<input type="hidden" id="url">			
				<button type="button" class="btn btn-outline-success" onclick="irAlRegistro()">Ir al registro</button>
			</div>
		</div>
		<div class="row justify-content-md-center mt-4">
			<div class="col-lg-6 col-md-12 col-sm-12 text-center">
				<small><a href="https://wsp-multi.dcsing.com/view/terminos-y-condiciones/" target="_blanc">Términos y condiciones</a></small>
			</div>
		</div>
		<div class="row justify-content-md-center">
			<div class="col-lg-6 col-md-12 col-sm-12 text-center">
				<small><span class="text-danger">¿Deseas eliminar tu cuenta?</span> <span> Pulsa </span>  
					<a href="../eliminar-cuenta/">aquí </a> 
				para continuar.</small>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
	<script type="text/javascript" src="../../public/js/recursos-registro.js"></script>
</body>
</html>