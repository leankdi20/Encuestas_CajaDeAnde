<?php
include_once "header.php";

$encuesta_id = $_GET['id'];
$enc = Query::dataEncuesta($conn, $encuesta_id);  
$encuesta = $enc["encuesta"];

if (isset($_GET["accion"])) {
  if ($_GET["accion"] == "crear") {
		//if ($_POST["form_token"] == $_SESSION["form_token"]) {
			$mens_crea = Procesos::crear_respuesta($conn, $encuesta_id, $_POST)["mensaje"];
			unset($_SESSION["form_token"]);
		// } else {
			// $mens_crea = "Solo se puede realizar un envío simultáneo de este formulario.";
		// }
    
    if ($mens_crea == "") {
      $clase = "text-success";
      $titulo = "¡Muchas gracias!";
      $texto = $encuesta["mensaje_exito"];
    } else {
      $clase = "text-danger";
      $titulo = "¡Atención!";
      $texto = "Ocurrió un error al enviar su evaluación. Error: " . $mens_crea . ".";
    }
  }
}

echo "<main role='main' class='container text-gris-oscuro'>
        <div class='row px-2 py-5'>
          <div class='col-12 text-center'>";
if (isset($_GET['app']) && $_GET['app'] != "1") {
  echo "
            <img src='assets/img/logo.png' class='img-logo'/>
            <h3 class='text-gris my-4'>".$encuesta["nombre"]."</h3>";
}
echo "
            <h4 class='text-gris my-4 " . $clase . "'>" . $titulo . "</h3>
            <p>" . $texto . "</p>
          </div>
        </div>
      </main>";

include_once "footer.php";
?>