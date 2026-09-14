$(document).ready(function() {
	let segundos = 30;
	$("#contador_texto").text("El botón se habilitará en " + segundos + " Seg");
	let intervalo = setInterval(function() {
		segundos--;
		if (segundos > 0) {
			$("#contador_texto").text("El botón se habilitará en " + segundos + " Seg");
		} else {
			clearInterval(intervalo);
			$("#contador_texto").text("¡El botón está habilitado!");
			$("#btn_wa").prop("disabled", false);
		}
	}, 1000);
});
