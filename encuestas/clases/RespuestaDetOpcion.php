<?php

class RespuestaDetOpcion {
  
  public static function insertar($conn, $respuesta_det_id, $encuesta_det_opcion_id, $texto_otro) {
    $ret = array("mensaje" => "", 
                 "datos" => null);
    $id = null;

    try {
      $cmd = " INSERT INTO respuesta_det_opciones (respuesta_det_id, encuesta_det_opcion_id, texto_otro) 
               VALUES (?, ?, ?); 
               SELECT SCOPE_IDENTITY() as id; ";
      $params = array($respuesta_det_id, $encuesta_det_opcion_id, $texto_otro);
      $datos = sqlsrv_query($conn, $cmd, $params);
      
      if (!$datos) 
        throw new Exception(sqlsrv_errors()[0]['message']);
      if (sqlsrv_rows_affected($datos) <= 0) 
        throw new Exception("Error creando la opción de la respuesta (detalle) en la base de datos.");
      
      $next_result = sqlsrv_next_result($datos);
      if ($next_result) {
        while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
          $id = $row["id"];
        }
      } 
      
      sqlsrv_free_stmt($datos);
      
      if (isset($id)) 
        $ret["datos"] = array("respuesta_det_opcion_id" => $id);
      else 
        throw new Exception("Error creando la respuesta (detalle) en la base de datos.");
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["datos"] = null;
    }
      
    return $ret;
  }
  
}

?>