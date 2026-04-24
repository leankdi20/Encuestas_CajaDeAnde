<?php
include_once "header.php";

$respuestas = Respuesta::getRespuestas($conn)["respuestas"];

echo "<main role='main' class='container text-gris-oscuro'>
        <div class='row p-5'>
          <div class='col-12 text-center'>
            <img src='assets/img/logo.png' class='img-logo'/>
            <h3 class='text-gris my-4'>Revisión de datos</h3>
          </div>
          <div class='col-12'>
            <table>
              <thead>
                <tr>
                  <th>Accionista</th>
                  <th>Cédula</th>
                  <th>Sucursal</th>
                  <th>Unidad</th>
                  <th class='text-right'>Fecha</th>
                  <th class='text-right'>Pregunta 1</th>
                  <th class='text-right'>Pregunta 2</th>
                  <th class='text-right'>Pregunta 3</th>
                  <th class='text-right'>Pregunta 4</th>
                  <th class='text-right'>Pregunta 5</th>
                  <th class='text-right'>¿Recomendaría?</th>
                  <th class='text-right'>Comentarios</th>
                </tr>
              </thead>
              <tbody>";

if (isset($respuestas)) {
  foreach ($respuestas as $respuesta) {
    $respuesta_id = $respuesta["respuesta_id"];
    $respuesta_dets = RespuestaDet::getRespuestaDets($conn, $respuesta_id)["respuesta_dets"];
    $resp9 = "";
    
    foreach ($respuesta_dets as $respuesta_det) {
      switch ($respuesta_det["encuesta_det_id"]) {
        case 3: 
          $resp3 = "<td class='text-right'>".$respuesta_det["valor"]."</td>";
          break;
        case 4: 
          $resp4 = "<td class='text-right'>".$respuesta_det["valor"]."</td>";
          break;
        case 5: 
          $resp5 = "<td class='text-right'>".$respuesta_det["valor"]."</td>";
          break;
        case 6: 
          $resp6 = "<td class='text-right'>".$respuesta_det["valor"]."</td>";
          break;
        case 7: 
          $resp7 = "<td class='text-right'>".$respuesta_det["valor"]."</td>";
          break;
        case 8: 
          $resp8 = "<td class='text-right'>".$respuesta_det["valor"]."</td>";
          break;
        case 9: 
          $resp9 = "<td class='text-right'>".$respuesta_det["valor"]."</td>";
          break;
      }
    }
    
    if ($resp9 == "") $resp9 = "<td></td>";
    
    echo "
                <tr>
                  <td>".$respuesta["nombre"]."</td>
                  <td>".$respuesta["cedula"]."</td>
                  <td>".$respuesta["sucursal"]."</td>
                  <td>".$respuesta["unidad"]."</td>
                  <td class='text-right'>".$respuesta["fecha_format"]."</td>".$resp3.$resp4.$resp5.$resp6.$resp7.$resp8.$resp9.
                "</tr>";
  }
}

echo "
              </tbody>
            </table>
          </div>
        </div>
      </main>";

include_once "footer.php";
?>