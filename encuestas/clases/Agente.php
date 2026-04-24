<?php

class Agente {
  
  public static function getAgente($conn, $agente_id)
  {
    $ret = array("mensaje" => "", 
                 "agente" => null);
    $agentes = array();
    
    try {
      $cmd = " select agente_id, nombre, sucursal_id, unidad_id 
               from agentes 
               where agente_id = ? ";
      $datos = sqlsrv_query($conn, $cmd, array($agente_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        $ret["agente"] = $row;
      }
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["agente"] = null;
    }
    
    return $ret;
  }
  
  public static function getAgentes($conn)
  {
    $ret = array("mensaje" => "", 
                 "agentes" => null);
    $agentes = array();
    
    try {
      $cmd = " select agente_id, nombre, sucursal_id, unidad_id 
               from agentes 
               order by nombre ";
      $datos = sqlsrv_query($conn, $cmd);
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($agentes, $row);
      }
      $ret["agentes"] = $agentes;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["agentes"] = null;
    }
    
    return $ret;
  }
  
  public static function getAgentesSucUni($conn, $sucursal_id, $unidad_id)
  {
    $ret = array("mensaje" => "", 
                 "agentes" => null);
    $agentes = array();
    
    try {
      $cmd = " select agente_id, nombre, sucursal_id, unidad_id 
               from agentes 
               where sucursal_id = ? and unidad_id = ? 
               order by nombre ";
      $datos = sqlsrv_query($conn, $cmd, array($sucursal_id, $unidad_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($agentes, $row);
      }
      $ret["agentes"] = $agentes;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["agentes"] = null;
    }
    
    return $ret;
  }
  
  public static function getAgentesDash($conn, $nombre, $sucursal_id, $unidad_id)
  {
    $ret = array("mensaje" => "", 
                 "agentes" => null);
    $agentes = array();
		$params = array();
    
    try {
			$cond = "";
			if ($nombre != "") {
				$cond .= ($cond == "" ? " where " : " and ") . " a.nombre_lower like ? ";
				array_push($params, "%" . $nombre . "%");
			}
			if ($sucursal_id > 0) {
				$cond .= ($cond == "" ? " where " : " and ") . " a.sucursal_id = ? ";
				array_push($params, $sucursal_id);
			}
			if ($unidad_id > 0) {
				$cond .= ($cond == "" ? " where " : " and ") . " a.unidad_id = ? ";
				array_push($params, $unidad_id);
			}
			
      $cmd = " select a.agente_id, a.nombre, a.sucursal_id, s.nombre as sucursal, a.unidad_id, u.nombre as unidad, u.es_whatsapp, 
											'https://mercadeo.cajadeande.fi.cr/encuestas/index.php?id=1&age_id=' + CAST(a.agente_id as varchar) as url, 
											'https://mercadeo.cajadeande.fi.cr/encuestas/index.php?id=17&age_id=' + CAST(a.agente_id as varchar) as url_whatsapp
               from agentes a 
							 inner join sucursales s on (s.sucursal_id = a.sucursal_id)
							 inner join unidades u on (u.unidad_id = a.unidad_id) " . $cond . "
               order by a.nombre ";
      $datos = sqlsrv_query($conn, $cmd, $params);
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($agentes, $row);
      }
      $ret["agentes"] = $agentes;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["agentes"] = null;
    }
    
    return $ret;
  }
  
  public static function insertar($conn, $nombre, $sucursal_id, $unidad_id) {
    $ret = array("mensaje" => "", 
                 "datos" => null);
    $id = null;
    
    try {
      $cmd = " INSERT INTO agentes (nombre, sucursal_id, unidad_id) 
               VALUES (?, ?, ?); 
               SELECT SCOPE_IDENTITY() as id; ";
      $params = array($nombre, $sucursal_id, $unidad_id);
      $datos = sqlsrv_query($conn, $cmd, $params);
      
      if (!$datos) 
        throw new Exception(sqlsrv_errors()[0]['message']);
      if (sqlsrv_rows_affected($datos) <= 0) 
        throw new Exception("Error creando el agente en la base de datos 1.");
      
      $next_result = sqlsrv_next_result($datos);
      if ($next_result) {
				while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
				  $id = $row["id"];
        }
      } 
      
      sqlsrv_free_stmt($datos);
      
      /* if (isset($id)) 
        $ret["datos"] = array("agente_id" => $id);
      else 
        throw new Exception("Error creando el agente en la base de datos 2."); */
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["datos"] = null;
    }
      
    return $ret;
  }
  
  public static function modificar($conn, $agente_id, $sucursal_id, $unidad_id) {
    $ret = array("mensaje" => "");
    
    try {
      $cmd = " update agentes 
							 set sucursal_id = ?, unidad_id = ? 
							 where agente_id = ? ";
      $params = array($sucursal_id, $unidad_id, $agente_id);
      $datos = sqlsrv_query($conn, $cmd, $params);
      
      if (!$datos) 
        throw new Exception(sqlsrv_errors()[0]['message']);
      if (sqlsrv_rows_affected($datos) <= 0) 
        throw new Exception("Error modificando el agente en la base de datos.");
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
    }
      
    return $ret;
  }
  
}

?>