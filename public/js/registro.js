var id_cliente = $('#id_cliente').val()
console.log(id_cliente);

$("#frm_registrar_numero").on("submit",function(data){
	registrar_numero(data);
});

async function obtenerData(id_cuenta) {
	let response = await fetch('../../controllers/registro.php?op=obtenerData', {
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
	console.log(data);
	$('#logo_cliente').attr('src', '../../'+data.cliente.logo_ruta);
	$('#nombre_cliente').html(data.cliente.nombre);

	data.listaPais.forEach( status =>{
		$("#indicativo").append(`<option value="${status.indicativo_wsp}">${status.indicativo_lbl+" - "+status.nombre}</option>`);
	}); 
	$("#indicativo").selectpicker('refresh');
	$(".loader").fadeOut("slow");
})();

function registrar_numero(data) {
	$(".loader").show();
	data.preventDefault();
	var formData = new FormData($("#frm_registrar_numero")[0]);
	$.ajax({
		url: "../../controllers/registro.php?op=nuevoNumero",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,
		success: function(data){
			data = JSON.parse(data);
			$(".loader").fadeOut("slow");
			Swal.fire({
				icon: data.icon,
				title: data.title ,
				text:data.text
			}).then((result) => {
				window.location.href = '../chatear';
			});
		}
	});
}