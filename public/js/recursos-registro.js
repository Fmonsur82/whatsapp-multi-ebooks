var id_cliente = $('#id_cliente').val()

$("#frm_registrar_numero").on("submit",function(data){
	registrar_numero(data);
});


async function obtenerData(id_cuenta) {
	let response = await fetch('../../controllers/recursos-registro.php?op=obtenerData', {
		method: 'POST',
		headers: {'Content-Type': 'application/x-www-form-urlencoded'},
		body: new URLSearchParams({
			id_cliente:id_cliente
		})
	});
	let data = await response.json();
	return data;
}

(async () => {
	data = await obtenerData(id_cliente);
	$('#logo_cliente').attr('src', '../../'+data.cliente.logo_ruta);
	$("#indicativo").selectpicker('refresh');
	$("#url").val(data.url_registro);
	generarQRRegistro(data.url_registro);

})();


function generarQRRegistro(url) {
	QRCode.toCanvas(document.getElementById('qrcode'), url, function (error) {
		if (error) console.error(error);
	});
}

function irAlRegistro() {
	window.location.href = $("#url").val();
}

