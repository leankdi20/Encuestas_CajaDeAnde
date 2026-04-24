<?php

class Gestion {
  
  public static function getGestionesUnidad($conn, $unidad_id)
  {
    $ret = array("mensaje" => "", 
                 "gestiones" => null);
    $gestiones = array();
    
    try {
      $cmd = " select g.gestion_id, g.nombre, g.otro 
               from gestiones g 
               inner join unidad_gestiones ug on (ug.unidad_id = ? and ug.gestion_id = g.gestion_id) 
               inner join unidades u on (u.unidad_id = ug.unidad_id) 
               order by g.otro, g.nombre ";
      $datos = sqlsrv_query($conn, $cmd, array($unidad_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($gestiones, $row);
      }
      $ret["gestiones"] = $gestiones;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["gestiones"] = null;
    }
    
    return $ret;
  }
  
}

?>