<?php
include_once "header.php";

$pixel_evento = "";
$clase = "text-gris-oscuro";
$titulo = "Formulario";
$texto = "No fue posible procesar la solicitud.";

$encuesta_id = $_GET['id'];
$enc = Query::dataEncuesta($conn, $encuesta_id);
$encuesta = $enc["encuesta"];

if (isset($_GET["accion"]) && $_GET["accion"] == "crear") {
  if (empty($_POST) && ($_SERVER["CONTENT_LENGTH"] ?? 0) > 0) {
    $mens_crea = "El tama&ntilde;o total de los archivos supera el l&iacute;mite permitido por el servidor (" . ini_get("post_max_size") . "). Seleccione im&aacute;genes o documentos m&aacute;s livianos.";
  } else {
    $mens_crea = Procesos::crear_respuesta($conn, $encuesta_id, $_POST)["mensaje"];
  }
  unset($_SESSION["form_token"]);

  if ($mens_crea == "") {
    $clase = "text-success";
    $titulo = "&iexcl;Muchas gracias!";
    $mensaje_exito = MensajeExitoEncuestaFactory::crear($encuesta_id);
    $texto = $mensaje_exito->obtenerMensaje($encuesta);

    $pixel_evento = "
      <script>
        fbq('track', 'Lead');
      </script>
    ";
  } else {
    $clase = "text-danger";
    $titulo = "&iexcl;Atenci&oacute;n!";
    $texto = "Ocurri&oacute; un error al enviar su evaluaci&oacute;n. Error: " . $mens_crea . ".";
  }
}

echo $pixel_evento;
echo "
  <main role='main' class='survey-page'>
    <div class='survey-shell container py-4 py-md-5'>
      <section class='survey-card survey-card--message text-center'>
        <div class='survey-brand survey-brand--centered'>
          <img src='https://storageproyectosmercadeo.blob.core.windows.net/masaccesosbeneficios/banners/Logocajadeande.png' class='img-logo' alt='Caja de ANDE'/>
          <span class='survey-badge'>Encuestas Caja de ANDE</span>
        </div>
        <h1 class='survey-title my-4 " . $clase . "'>" . $titulo . "</h1>
        <p class='survey-description survey-description--message'>" . $texto . "</p>
      </section>
    </div>
  </main>";

include_once "footer.php";
?>
