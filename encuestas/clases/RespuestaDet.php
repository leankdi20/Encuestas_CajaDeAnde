<?php

class RespuestaDet {
  
  public static function insertar($conn, $respuesta_id, $encuesta_det_id, $tipo_id, $valor, $texto_otro, 
                                  $texto_exp) {
    $ret = array("mensaje" => "", 
                 "datos" => null);
    $id = null;

    try {
      $cmd = " INSERT INTO respuesta_det (respuesta_id, encuesta_det_id, tipo_id, valor, texto_otro, 
                                          texto_exp) 
               VALUES (?, ?, ?, ?, ?, 
                       ?); 
               SELECT SCOPE_IDENTITY() as id; ";
      $params = array($respuesta_id, $encuesta_det_id, $tipo_id, $valor, $texto_otro, 
                      $texto_exp);
      $datos = sqlsrv_query($conn, $cmd, $params);
      
      if (!$datos) 
        throw new Exception(sqlsrv_errors()[0]['message']);
      if (sqlsrv_rows_affected($datos) <= 0) 
        throw new Exception("Error creando la respuesta (detalle) en la base de datos.");
      
      $next_result = sqlsrv_next_result($datos);
      if ($next_result) {
        while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
          $id = $row["id"];
        }
      } 
      
      sqlsrv_free_stmt($datos);
      
      if (isset($id)) 
        $ret["datos"] = array("respuesta_det_id" => $id);
      else 
        throw new Exception("Error creando la respuesta (detalle) en la base de datos.");
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["datos"] = null;
    }
      
    return $ret;
  }
  
  public static function actualizarTextoOtro($conn, $respuesta_id, $encuesta_det_id, $texto_otro)
  {
    $ret = array("mensaje" => "", 
                 "respuesta_det_id" => 0);
    
    try {
      $cmd = " UPDATE respuesta_det 
               SET texto_otro = ? 
               WHERE rd.respuesta_id = ? 
                 AND rd.encuesta_det_id = ? ";
      $params = array($texto_otro, $respuesta_id, $encuesta_det_id);
      $datos = sqlsrv_query($conn, $cmd, $params);
      
      if (!$datos) 
        throw new Exception(sqlsrv_errors()[0]['message']);
      if (sqlsrv_rows_affected($datos) <= 0) 
        throw new Exception("Error actualizando el texto de otro de la respuesta (detalle) en la base de datos.");
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["respuesta_det_id"] = 0;
    }
    
    return $ret;
  }
  
  public static function getRespuestaDets($conn, $respuesta_id)
  {
    $ret = array("mensaje" => "", 
                 "respuesta_dets" => null);
    $respuesta_dets = array();
    
    try {
      $cmd = " select rd.respuesta_det_id, rd.respuesta_id, rd.encuesta_det_id, 
                      rd.tipo_id, rd.valor, rd.texto_otro 
               from respuesta_det rd 
               left join encuesta_det ed on (ed.encuesta_det_id = rd.encuesta_det_id) 
               where rd.respuesta_id = ? 
               order by ed.orden ";
      $datos = sqlsrv_query($conn, $cmd, array($respuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($respuesta_dets, $row);
      }
      $ret["respuesta_dets"] = $respuesta_dets;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["respuesta_dets"] = null;
    }
    
    return $ret;
  }
  
}

?>