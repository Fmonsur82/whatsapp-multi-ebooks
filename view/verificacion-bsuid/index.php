<?php
$token = trim((string) ($_GET['token'] ?? ''));
if ($token === '' || !preg_match('/^[a-f0-9]{64}$/', $token)) {
    http_response_code(400);
    echo 'Enlace de verificación no válido.';
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica tu cuenta</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="../../public/css/main.css">
</head>
<body>
    <div class="loader" style="display: none;"></div>
    <main class="container mt-4 mb-5">
        <div class="row justify-content-center mt-3">
            <div class="col-lg-4 col-md-6 col-8 text-center">
                <img src="../../public/img/banner.png" alt="Ebooks7-24" width="100%">
            </div>
        </div>
        <div class="row justify-content-center mt-4">
            <div class="col-lg-6 col-md-8">
                <h4 class="text-center">Verifica tu cuenta</h4>
                <p>Para continuar usando la herramienta, confirma el número con el que te registraste y acepta los documentos actualizados.</p>
                <form id="frm_verificacion_bsuid">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="form-group">
                        <label for="indicativo">Número registrado</label>
                        <div class="form-row">
                            <div class="col-md-5 mb-2">
                                <select id="indicativo" name="indicativo" class="selectpicker" title="País" data-live-search="true" data-width="100%" data-size="5" required></select>
                            </div>
                            <div class="col-md-7 mb-2">
                                <input type="text" class="form-control" id="telefono" name="telefono" inputmode="numeric" pattern="\d{6,13}" maxlength="13" required placeholder="Número celular">
                            </div>
                        </div>
                        <small class="form-text text-muted">Selecciona tu país e ingresa el número celular registrado.</small>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="1" id="acepta_pol_priv" name="acepta_pol_priv" required>
                        <label class="form-check-label" for="acepta_pol_priv">Acepto la <a href="https://digital-content.co/politica-de-datos/" target="_blank" rel="noopener">política de tratamiento de datos</a> (v3-202607).</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="1" id="acepta_tyc" name="acepta_tyc" required>
                        <label class="form-check-label" for="acepta_tyc">Acepto los <a href="https://wsp-multi.dcsing.com/view/terminos-y-condiciones/" target="_blank" rel="noopener">términos y condiciones</a> (v2-202609).</label>
                    </div>
                    <button type="submit" id="btn_verificar" class="btn btn-block btn-outline-primary">Verificar mi cuenta</button>
                </form>
                <div id="resultado_verificacion" class="mt-3" role="status" aria-live="polite"></div>
            </div>
        </div>
    </main>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="../../public/js/verificacion-bsuid.js"></script>
</body>
</html>
