<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<link rel="icon" type="image/ico" href="https://dcsing.com/recursos/corporativa/favicon.ico">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Eliminación de cuenta</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
	<link rel="stylesheet" href="../../public/css/main.css">
</head>
<body>
	<div class="loader"></div>
	<div class="container mt-3">
		<div class="row justify-content-center mt-3">
			<div class="col-lg-6 col-md-5 col-sm-6 col-6 d-flex justify-content-center">
				<h4>Eliminación de cuenta</h4>
			</div>
		</div>
		<div class="row justify-content-center mt-3">
			<div class="col-lg-4 col-md-6 col-7 d-flex justify-content-center">
				<img src="../../public/img/banner.png" alt="Ebooks7-24" width="100%">
			</div>
		</div>

		<form id="frm_eliminar_numero">
			<div class="row justify-content-md-center mt-4	">
				<!-- <input type="hidden" id="id_cliente" name="id_cliente" value="<?php echo $cliente->id_cliente ?>"> -->
				<div class="col-lg-3 col-md-6 col-sm-12 mt-3">
					<select id="indicativo" name="indicativo" class="selectpicker" title="País" data-live-search="true" data-width="100%" data-size="5" required>
					</select>
				</div>

				<div class="col-lg-3 col-md-6 col-sm-12 mt-3">
					<div class="form-group">
						<input
						type="text"
						class="form-control"
						id="telefono"
						name="telefono"
						pattern="\d{8,13}"
						maxlength="13"
						minlength="8"
						oninput="this.value = this.value.replace(/\D/g, '').slice(0,13);"
						required
						placeholder="Celular (8 a 13 dígitos)"
						>
					</div>
				</div>
			</div>
			<div class="row justify-content-md-center mt-2">
				<div class="col-lg-6 col-md-12 col-sm-12">
					<button type="submit" id="btn_registrar" class="btn btn-block btn-outline-danger">Eliminar Cuenta</button>
				</div>
			</div>
		</form>

		<div class="row justify-content-md-center mt-4 mb-5">
			<div class="col-lg-6 col-md-12 col-sm-12">
				<small>
					
					<p>Al solicitar la eliminación de su número:</p>
					<ul>
						<li>Su solicitud se procesará de inmediato</li>
						<li>Dejará de recibir mensajes informativos, recordatorios, novedades, notificaciones de acceso y demás comunicaciones relacionadas con el servicio de WhatsApp para la plataforma Ebooks7-24 Plus.</li>
						<li>La eliminación de su número de WhatsApp no afectará su acceso a la plataforma Ebooks7-24 Plus ni la vigencia de su cuenta de usuario de Spiral Reader.</li>
						<li>Para fines estadísticos y de mejora del servicio, los registros de uso e interacción previos a la eliminación podrán conservarse de forma anonimizada o con el número renombrado.</li>
					</ul>
				</small>
			</div>
		</div>
	</div>
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
	<script type="text/javascript" src="../../public/js/eliminar-cuenta.js"></script>
</body>
</html>