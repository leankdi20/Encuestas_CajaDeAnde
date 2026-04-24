<?php

class Respuesta {
  
  public static function insertar($conn, 
								  $encuesta_id, $nombre, $cedula, $sucursal_id, $unidad_id, 
								  $agente_id, $gestion_id, $fecha, $fecha_nac, $estado_emp_id, 
								  $estado_otro, $genero_id, $genero_otro, $puesto_id, $puesto_otro, 
								  $provincia_id, $canton_id, $distrito_id, $correo, $telefono, 
								  $est_civil_id) {
    $ret = array("mensaje" => "", 
                 "datos" => null);
    $id = null;
    
    try {
      $cmd = " INSERT INTO respuestas (encuesta_id, nombre, cedula, sucursal_id, unidad_id, 
                                       agente_id, gestion_id, fecha, fecha_nac, estado_emp_id, 
                                       estado_otro, genero_id, genero_otro, puesto_id, puesto_otro, 
                                       provincia_id, canton_id, distrito_id, correo, telefono, 
									   est_civil_id) 
               VALUES (?, ?, ?, ?, ?, 
                       ?, ?, ?, ?, ?, 
                       ?, ?, ?, ?, ?, 
                       ?, ?, ?, ?, ?, 
					   ?); 
               SELECT SCOPE_IDENTITY() as id; ";
      $params = array($encuesta_id, $nombre, $cedula, $sucursal_id, $unidad_id, 
                      $agente_id, $gestion_id, $fecha, $fecha_nac, $estado_emp_id, 
                      $estado_otro, $genero_id, $genero_otro, $puesto_id, $puesto_otro, 
                      $provincia_id, $canton_id, $distrito_id, $correo, $telefono, 
					  $est_civil_id);
      $datos = sqlsrv_query($conn, $cmd, $params);
      
      if (!$datos) 
        throw new Exception(sqlsrv_errors()[0]['message']);
      if (sqlsrv_rows_affected($datos) <= 0) 
        throw new Exception("Error creando la respuesta en la base de datos.");
      
      $next_result = sqlsrv_next_result($datos);
      if ($next_result) {
        while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
          $id = $row["id"];
        }
      } 
      
      sqlsrv_free_stmt($datos);
      
      if (isset($id)) 
        $ret["datos"] = array("respuesta_id" => $id);
      else 
        throw new Exception("Error creando la respuesta en la base de datos.");
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["datos"] = null;
    }
      
    return $ret;
  }
  
  public static function getRespuestas($conn)
  {
    $ret = array("mensaje" => "", 
                 "respuestas" => null);
    $respuestas = array();
    
    try {
      $cmd = " select r.respuesta_id, r.encuesta_id, r.nombre, r.cedula, r.correo, r.telefono, r.fecha, dbo.fecha_texto(r.fecha) as fecha_format, 
                      r.fecha_nac, dbo.fecha_texto(r.fecha_nac) as fecha_nac_format, r.sucursal_id, s.nombre as sucursal, r.unidad_id, u.nombre as unidad, 
                      coalesce(r.estado_otro, ee.nombre) as estado_emp, coalesce(r.genero_otro, g.nombre) as genero, coalesce(r.puesto_otro, p.nombre) as puesto, 
                      pr.nombre as provincia, ca.nombre as canton, d.nombre as distrito, a.nombre as agente, ge.nombre as gestion, 
                      ec.nombre as estado_civil 
               from respuestas r 
               left join sucursales s on (s.sucursal_id = r.sucursal_id) 
               left join unidades u on (u.unidad_id = r.unidad_id) 
               left join estados_emp ee on (ee.estado_emp_id = r.estado_emp_id) 
               left join generos g on (g.genero_id = r.genero_id) 
               left join puestos p on (p.puesto_id = r.puesto_id) 
               left join provincias pr on (pr.provincia_id = r.provincia_id) 
               left join cantones ca on (ca.canton_id = r.canton_id) 
               left join distritos d on (d.distrito_id = r.distrito_id) 
               left join agentes a on (a.agente_id = r.agente_id) 
               left join gestiones ge on (ge.gestion_id = r.gestion_id) 
               left join est_civiles ec on (ec.est_civil_id = r.est_civil_id) 
               order by r.respuesta_id ";
      $datos = sqlsrv_query($conn, $cmd);
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($respuestas, $row);
      }
      $ret["respuestas"] = $respuestas;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["respuestas"] = null;
    }
    
    return $ret;
  }
  
  public static function getRespuesta($conn, $respuesta_id)
  {
    $ret = array("mensaje" => "", 
                 "respuesta" => null);
    $respuesta = null;
    
    try {
      // se obtiene el registro completo del encabezado
      $cmd = " select r.respuesta_id, r.encuesta_id, r.nombre, r.cedula, r.correo, r.telefono, r.fecha, dbo.fecha_texto(r.fecha) as fecha_format, 
                      r.fecha_nac, dbo.fecha_texto(r.fecha_nac) as fecha_nac_format, r.sucursal_id, s.nombre as sucursal, r.unidad_id, u.nombre as unidad, 
                      coalesce(r.estado_otro, ee.nombre) as estado_emp, coalesce(r.genero_otro, g.nombre) as genero, coalesce(r.puesto_otro, p.nombre) as puesto, 
                      pr.nombre as provincia, ca.nombre as canton, d.nombre as distrito, a.nombre as agente, ge.nombre as gestion, 
                      ec.nombre as estado_civil 
               from respuestas r 
               left join sucursales s on (s.sucursal_id = r.sucursal_id) 
               left join unidades u on (u.unidad_id = r.unidad_id) 
               left join estados_emp ee on (ee.estado_emp_id = r.estado_emp_id) 
               left join generos g on (g.genero_id = r.genero_id) 
               left join puestos p on (p.puesto_id = r.puesto_id) 
               left join provincias pr on (pr.provincia_id = r.provincia_id) 
               left join cantones ca on (ca.canton_id = r.canton_id) 
               left join distritos d on (d.distrito_id = r.distrito_id) 
               left join agentes a on (a.agente_id = r.agente_id) 
               left join gestiones ge on (ge.gestion_id = r.gestion_id) 
               left join est_civiles ec on (ec.est_civil_id = r.est_civil_id) 
               where respuesta_id = ? ";
      $datos = sqlsrv_query($conn, $cmd, array($respuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        $respuesta = $row;
      }

      sqlsrv_free_stmt($datos);
      // se obtiene el registro completo del encabezado


      // se obtienen todos los registros de las lineas de detalle
      $det = RespuestaDet::getRespuestaDetPorRespuesta($conn, $respuesta_id);

      if ($det["mensaje"] != "") throw new Exception($det["mensaje"]);

      $respuesta["detalles"] = $det["respuesta_dets"];
      // se obtienen todos los registros de las lineas de detalle


      $ret["respuesta"] = $respuesta;
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["respuesta"] = null;
    }
    
    return $ret;
  }

  public static function actualizarCorreos($conn, $respuesta_id, $correos) {
    $ret = array("mensaje" => "");
    
    try {
        $cmd = " update respuestas set correos_envio = ? where respuesta_id = ? ";
        $params = array($correos, $respuesta_id);
        $stmt = sqlsrv_prepare($conn, $cmd, $params);
        
        if (!sqlsrv_execute($stmt)) throw new Exception(sqlsrv_errors()[0]['message']);

        $rows_affected = sqlsrv_rows_affected($stmt);
        if ($rows_affected <= 0) throw new Exception("Error agregando el cintillo en la base de datos.");

        sqlsrv_free_stmt($stmt);
    } catch (Exception $e) {
        $ret["mensaje"] = $e->getMessage();
    }
      
    return $ret;
  }
  
  public static function insertarRespuestaEnvioCorreo($conn, $respuesta_id, $correo, $respuesta) {
    $ret = array("mensaje" => "");
    
    try {
      $cmd = " INSERT INTO respuesta_envio_correo (respuesta_id, correo, respuesta) 
               VALUES (?, ?, ?); ";
      $params = array($respuesta_id, $correo, $respuesta);
      $datos = sqlsrv_query($conn, $cmd, $params);
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      if (sqlsrv_rows_affected($datos) <= 0) throw new Exception("Error creando la respuesta en la base de datos.");
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
    }
      
    return $ret;
  }
  
}

?>