$('#cont_id_cliente').hide();

$.post('../../controllers/instituciones.php?op=listarPaises',function(data) {
	$("#id_pais").html(data);
	$("#id_pais").selectpicker('refresh');
});

$("#id_pais").change(function(){
	id_pais = $( this ).val();
	if (id_pais != null) {
		console.log(id_pais);
		selectInstitucion(id_pais);
	}
}).change();


function selectInstitucion(id_pais) {
	$("#id_cliente").hide();
	$("#spinner_institucion").show();
	$.post('../../controllers/instituciones.php?op=selectInstitucion', {
		id_pais: id_pais
	}, function(data) {
		$("#spinner_institucion").hide(500);
		$("#id_cliente")
			.html(data)
			.prop('disabled', false)
			.selectpicker('refresh');

		$('#cont_id_cliente').show(500);
	});
}


$("#frm_institucion").on("submit",function(data){
	vistaRegistro(data);
});

function vistaRegistro(data) {
	data.preventDefault();
	var formData = new FormData($("#frm_institucion")[0]);
	$.ajax({
		url: "../../controllers/instituciones.php?op=vistaRegistro",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,
		success: function(data){
			data = JSON.parse(data);
			window.location.href = data;			
		}
	});
}