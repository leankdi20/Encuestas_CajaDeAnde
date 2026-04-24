<?php

class Encuesta {
  
  public static function getEncuesta($conn, $encuesta_id)
  {
    $ret = array("mensaje" => "", 
                 "encuesta" => null);
    $encuesta = null;
    
    try {
      // se obtiene el registro completo del encabezado
      $cmd = " select encuesta_id, nombre, descripcion, categoria_id, fecha_inicio, 
                      dbo.fecha_texto(fecha_inicio) as fecha_inicio_format, fecha_fin, 
                      dbo.fecha_texto(fecha_fin) as fecha_fin_format, incluye_nombre, 
                      nombre_oblig, incluye_cedula, cedula_oblig, incluye_sucursal, 
                      sucursal_oblig, incluye_unidad, unidad_oblig, incluye_agente, 
                      agente_oblig, incluye_gestion, gestion_oblig, incluye_fecha_nac, 
                      fecha_nac_oblig, incluye_estado_emp, estado_emp_oblig, 
                      incluye_genero, genero_oblig, incluye_ubicacion, 
                      ubicacion_oblig, incluye_puesto, puesto_oblig, 
                      incluye_correo, correo_oblig, incluye_telefono, telefono_oblig, 
                      incluye_est_civil, est_civil_oblig, correos, mensaje_exito, 
											mensaje_bloqueo, mostrar_num_pregunta, folder_id, nombre_csv, activo, 
                      case when fecha_inicio > getdate() then 1 else 0 end mostrar_mensaje_fechas 
               from encuestas 
               where encuesta_id = ? ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        $encuesta = $row;
      }
      
      sqlsrv_free_stmt($datos);
      // se obtiene el registro completo del encabezado
      
      
      // se obtienen todos los registros de las lineas de detalle
      $det = EncuestaDet::getEncuestaDetPorEncuesta($conn, $encuesta_id);
      
      if ($det["mensaje"] != "") throw new Exception($det["mensaje"]);
      
      $encuesta["detalles"] = $det["encuesta_dets"];
      // se obtienen todos los registros de las lineas de detalle
      
      
      $ret["encuesta"] = $encuesta;
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["encuesta"] = null;
    }
    
    return $ret;
  }
  
  public static function getEncuestas($conn)
  {
    $ret = array("mensaje" => "", 
                 "encuestas" => null);
    $encuestas = array();
    
    try {
      $cmd = " select encuesta_id, nombre, descripcion, categoria_id, fecha_inicio, 
                      dbo.fecha_texto(fecha_inicio) as fecha_inicio_format, fecha_fin, 
                      dbo.fecha_texto(fecha_fin) as fecha_fin_format, incluye_nombre, 
                      nombre_oblig, incluye_cedula, cedula_oblig, incluye_sucursal, 
                      sucursal_oblig, incluye_unidad, unidad_oblig, incluye_agente, 
                      agente_oblig, incluye_gestion, gestion_oblig, incluye_fecha_nac, 
                      fecha_nac_oblig, incluye_estado_emp, estado_emp_oblig, 
                      incluye_genero, genero_oblig, incluye_ubicacion, 
                      ubicacion_oblig, incluye_puesto, puesto_oblig, 
                      incluye_correo, correo_oblig, incluye_telefono, telefono_oblig, 
                      incluye_est_civil, est_civil_oblig, correos, mensaje_exito, 
                      mensaje_bloqueo, mostrar_num_pregunta, folder_id, activo, 
                      case when fecha_inicio > getdate() then 1 else 0 end mostrar_mensaje_fechas 
               from encuestas 
               order by encuesta_id ";
      $datos = sqlsrv_query($conn, $cmd);
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($encuestas, $row);
      }
      $ret["encuestas"] = $encuestas;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["encuestas"] = null;
    }
    
    return $ret;
  }
//  
//  public static function insertar($mysqli, $correo, $contrasena, $nombre, $apellido1, $apellido2, $telefono1, $usuario_id) {
//    $ret = array("mensaje" => "", 
//                 "datos" => null);
//
//    try {
//      $cmd = " INSERT INTO usuarios (correo, contrasena, nombre, apellido1, apellido2, telefono1, 
//                                     creado_por, actualizado_por) 
//               VALUES (lower(?), ?, ?, ?, ?, ?, ?, ?) 
//               ON DUPLICATE KEY UPDATE nombre = ?, apellido1 = ?, apellido2 = ?, telefono1 = ?, actualizado_por = ?, 
//                                       actualizado = NOW(), activo = 1 ";
//      $stmt = $mysqli->prepare($cmd);
//      $stmt->bind_param('ssssssiissssi', $correo, $contrasena, $nombre, $apellido1, $apellido2, $telefono1, $usuario_id, $usuario_id, 
//                        $nombre, $apellido1, $apellido2, $telefono1, $usuario_id);
//      if ($stmt->execute()) 
//        $ret["datos"] = array("usuario_id" => $mysqli->insert_id);
//      else 
//        throw new Exception("Error creando el usuario en la base de datos.");
//      $stmt->close();
//    } catch (Exception $e) {
//      $ret["mensaje"] = $e->getMessage();
//      $ret["datos"] = null;
//    }
//      
//    return $ret;
//  }
//  
//  public static function actualizar_contrasena($mysqli, $usuario_id, $contrasena) {
//    $ret = array("mensaje" => "");
//
//    try {
//      $cmd = " update usuarios 
//               set contrasena = ? 
//               where usuario_id = ? ";
//      $stmt = $mysqli->prepare($cmd);
//      $stmt->bind_param('si', $contrasena, $usuario_id);
//      $stmt->execute();
//      if (trim($stmt->error) != "") throw new Exception($stmt->error);
//      $stmt->close();
//    } catch (Exception $e) {
//      $ret["mensaje"] = $e->getMessage();
//    }
//      
//    return $ret;
//  }
//  
}

?>