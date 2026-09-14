function init() {
	$.post('../../controllers/eliminar-cuenta.php?op=listarPaises', function(data) {
		$("#indicativo").html(data);
		$("#indicativo").selectpicker('refresh');
	});

	$("#frm_eliminar_numero").on("submit",function(data){
		eliminarNumero(data);
	});
	$(".loader").fadeOut("slow");
}

function eliminarNumero(data) {
	data.preventDefault();
	var formData = new FormData($("#frm_eliminar_numero")[0]);
	Swal.fire({
		title: "Estas seguro?",
		text: "Si eliminas la cuenta no podras usar el servicio!",
		icon: "warning",
		showCancelButton: true,
		confirmButtonColor: "#3085d6",
		cancelButtonColor: "#d33",
		confirmButtonText: "Si, eliminar"
	}).then((result) => {
		if (result.isConfirmed) {
			$(".loader").show();
			$.ajax({
				url: "../../controllers/eliminar-cuenta.php?op=eliminarNumero",
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
						location.reload();
					});
				}
			});
		}
	});	
}

init();