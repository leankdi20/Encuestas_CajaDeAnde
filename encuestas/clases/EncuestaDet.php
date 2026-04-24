<?php

class EncuestaDet {
  
  public static function getEncuestaDet($conn, $encuesta_det_id)
  {
    $ret = array("mensaje" => "", 
                 "encuesta_det" => null);
    $encuesta_det = array();
    
    try {
      // se obtienen todos los registros de las lineas de detalle
      $cmd = " select ed.encuesta_det_id, ed.encuesta_id, ed.tipo_id, ed.enunciado, 
                      coalesce(ed.texto_descripcion, '') as texto_descripcion, 
											coalesce(ed.placeholder, '') as placeholder, ed.opcional, 
											ed.dep_encuesta_det_id, ed.dep_encuesta_det_opcion_id, 
                      coalesce(edo.encuesta_det_opcion_id, 0) as otro_id 
               from encuesta_det ed 
               left join encuesta_det_opciones edo on (edo.encuesta_det_id = ed.encuesta_det_id 
                                                   and edo.otro = 1)
               where ed.encuesta_det_id = ? ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_det_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        $enc_det = $row;
        
        
        // se obtienen todos los del meta
        $det = EncuestaDetMeta::getEncuestaDetMeta($conn, $encuesta_det_id);

        if ($det["mensaje"] != "") throw new Exception($det["mensaje"]);

        $enc_det["meta"] = $det["encuesta_det_meta"];
        // se obtienen todos los del meta
        
        
        // se obtienen todas las opciones de la pregunta
        $det = EncuestaDetOpciones::getEncuestaDetOpciones($conn, $encuesta_det_id);

        if ($det["mensaje"] != "") throw new Exception($det["mensaje"]);

        $enc_det["opciones"] = $det["encuesta_det_opciones"];
        // se obtienen todas las opciones de la pregunta
        
        
        /* // se obtienen todas las dependencias de la pregunta
        $det = EncuestaDet::getDependencias($conn, $encuesta_det_id);

        if ($det["mensaje"] != "") throw new Exception($det["mensaje"]);

        $enc_det["dependencias"] = $det["dependencias"];
        // se obtienen todas las dependencias de la pregunta */
		
		
        // si es un archivo, traigo los tipos de archivo asociados
        if ($enc_det["tipo_id"] == EDT_ARCHIVO) {
          $det = EncuestaDetTipoArchivo::getEncuestaDetTiposArchivos($conn, $encuesta_det_id);

          if ($det["mensaje"] != "") throw new Exception($det["mensaje"]);

          $enc_det["tipos_archivo"] = $det["encuesta_det_tipos_archivo"];
        } else {
          $enc_det["tipos_archivo"] = array();
        }
        // si es un archivo, traigo los tipos de archivo asociados
        
        
        $ret["encuesta_det"] = $enc_det;
      }
      sqlsrv_free_stmt($datos);
      // se obtienen todos los registros de las lineas de detalle
      
      
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["encuesta_det"] = null;
    }
    
    return $ret;
  }
  
  public static function getEncuestaDetPorEncuesta($conn, $encuesta_id)
  {
    $ret = array("mensaje" => "", 
                 "encuesta_dets" => null);
    $encuesta_det = array();
    
    try {
      // se obtienen todos los registros de las lineas de detalle
      $cmd = " select ed.encuesta_det_id, ed.encuesta_id, ed.tipo_id, ed.enunciado, 
                      coalesce(ed.texto_descripcion, '') as texto_descripcion, 
											coalesce(ed.placeholder, '') as placeholder, ed.opcional, 
											ed.dep_encuesta_det_id, ed.dep_encuesta_det_opcion_id, 
                      coalesce(edo.encuesta_det_opcion_id, 0) as otro_id 
               from encuesta_det ed 
               left join encuesta_det_opciones edo on (edo.encuesta_det_id = ed.encuesta_det_id 
                                                   and edo.otro = 1)
               where ed.encuesta_id = ? 
               order by ed.orden ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        $enc_det = $row;
        $encuesta_det_id = $enc_det["encuesta_det_id"];
        
        
        // se obtienen todos los del meta
        $det = EncuestaDetMeta::getEncuestaDetMeta($conn, $encuesta_det_id);

        if ($det["mensaje"] != "") throw new Exception($det["mensaje"]);

        $enc_det["meta"] = $det["encuesta_det_meta"];
        // se obtienen todos los del meta
        
        
        // se obtienen todas las opciones de la pregunta
        $det = EncuestaDetOpciones::getEncuestaDetOpciones($conn, $encuesta_det_id);

        if ($det["mensaje"] != "") throw new Exception($det["mensaje"]);

        $enc_det["opciones"] = $det["encuesta_det_opciones"];
        // se obtienen todas las opciones de la pregunta
        
        
        /* // se obtienen todas las dependencias de la pregunta
        $det = EncuestaDet::getDependencias($conn, $encuesta_det_id);

        if ($det["mensaje"] != "") throw new Exception($det["mensaje"]);

        $enc_det["dependencias"] = $det["dependencias"];
        // se obtienen todas las dependencias de la pregunta */
		
		
        // si es un archivo, traigo los tipos de archivo asociados
        if ($enc_det["tipo_id"] == EDT_ARCHIVO) {
          $det = EncuestaDetTipoArchivo::getEncuestaDetTiposArchivos($conn, $encuesta_det_id);

          if ($det["mensaje"] != "") throw new Exception($det["mensaje"]);

          $enc_det["tipos_archivo"] = $det["encuesta_det_tipos_archivo"];
        } else {
          $enc_det["tipos_archivo"] = array();
        }
        // si es un archivo, traigo los tipos de archivo asociados
        
        
        array_push($encuesta_det, $enc_det);
      }
      $ret["encuesta_dets"] = $encuesta_det;
      
      sqlsrv_free_stmt($datos);
      // se obtienen todos los registros de las lineas de detalle
      
      
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["encuesta_dets"] = null;
    }
    
    return $ret;
  }
  
  public static function getDependencias($conn, $encuesta_det_id)
  {
    $ret = array("mensaje" => "", 
                 "dependencias" => null);
    $dependencias = array();
    
    try {
      // se obtienen todos los registros de las lineas de detalle
      $cmd = " select ed.encuesta_det_id, ed.dep_encuesta_det_id, ed.dep_encuesta_det_opcion_id 
               from encuesta_det ed 
               where ed.dep_encuesta_det_id = ? or ed.dep_encuesta_det_opcion_id = ? 
               order by ed.orden ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_det_id, $encuesta_det_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($dependencias, $row);
      }
      $ret["dependencias"] = $dependencias;
      
      sqlsrv_free_stmt($datos);
      // se obtienen todos los registros de las lineas de detalle
      
      
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["dependencias"] = null;
    }
    
    return $ret;
  }
  
}

?>