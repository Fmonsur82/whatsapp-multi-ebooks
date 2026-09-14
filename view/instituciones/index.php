<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<link rel="icon" type="image/ico" href="https://dcsing.com/recursos/corporativa/favicon.ico">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Instituciones</title>
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
		<div class="row justify-content-center" style="margin-top: 100px;">
			<div class="col-lg-4 col-md-6 col-7 d-flex justify-content-center">
				<img src="../../public/img/banner.png" alt="Ebooks7-24" width="100%">
			</div>
		</div>

		<form id="frm_institucion">

			<div class="row justify-content-center mt-4">
				<div class="col-lg-3 col-md-6 col-sm-12 mt-3">
					<select id="id_pais" name="id_pais" class="selectpicker" data-style="btn-outline-secondary" title="País" data-live-search="true" data-width="100%" data-size="5" required>
					</select>
				</div>
			</div>

			<div class="row justify-content-center mt-3">
				<div class="col-lg-3 col-md-6 col-sm-12 mt-3">
					<div id="spinner_institucion" style="display:none; text-align:center;">
						<span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span> Cargando instituciones...
					</div>
					<div id="cont_id_cliente">
						
						<select id="id_cliente" name="id_cliente" class="selectpicker" title="Institución" data-live-search="true" data-style="btn-outline-secondary"data-width="100%" data-size="5" style="display:none;" required>
						</select>
					</div>
				</div>
			</div>

			<div class="row justify-content-center mt-3">
				<div class="col-lg-3 col-md-6 col-sm-12 mt-3">
					<button type="submit" class="btn btn-block btn-outline-primary">Buscar Institución</button>
				</div>
			</div>
		</form>	
	</div>

	<script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
	<script type="text/javascript" src="../../public/js/instituciones.js"></script>
</body>
</html>