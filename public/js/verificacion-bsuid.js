(function () {
    const form = document.getElementById('frm_verificacion_bsuid');
    const button = document.getElementById('btn_verificar');
    const result = document.getElementById('resultado_verificacion');
    const telefono = document.getElementById('telefono');
    const indicativo = document.getElementById('indicativo');
    const loader = document.querySelector('.loader');

    async function cargarPaises() {
        try {
            const response = await fetch('../../controllers/verificacion-bsuid.php?op=listarPaises');
            const paises = await response.json();
            paises.forEach(function (pais) {
                const option = document.createElement('option');
                option.value = pais.indicativo_wsp;
                option.textContent = pais.indicativo_lbl + ' - ' + pais.nombre;
                indicativo.appendChild(option);
            });
            $(indicativo).selectpicker('refresh');
        } catch (error) {
            result.className = 'alert alert-danger mt-3';
            result.textContent = 'No fue posible cargar la lista de países. Recarga la página.';
        } finally {
            loader.style.display = 'none';
        }
    }

    loader.style.display = 'block';
    cargarPaises();

    telefono.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 15);
    });

    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        button.disabled = true;
        button.textContent = 'Verificando...';
        loader.style.display = 'block';
        result.className = 'mt-3';
        result.textContent = '';

        try {
            const response = await fetch('../../controllers/verificacion-bsuid.php?op=verificar', {
                method: 'POST',
                body: new FormData(form)
            });
            const data = await response.json();
            result.className = 'alert mt-3 ' + (data.ok ? 'alert-success' : 'alert-warning');
            result.textContent = data.mensaje || 'No fue posible completar la verificación.';
            if (data.url_registro) {
                const link = document.createElement('a');
                link.href = data.url_registro;
                link.textContent = ' Ir al registro';
                link.className = 'alert-link';
                result.appendChild(link);
            }
            if (data.ok) {
                form.reset();
            }
        } catch (error) {
            result.className = 'alert alert-danger mt-3';
            result.textContent = 'No fue posible comunicarse con el servicio. Intenta nuevamente.';
        } finally {
            loader.style.display = 'none';
            button.disabled = false;
            button.textContent = 'Verificar mi cuenta';
        }
    });
})();
