<?php

class Dashboard {
  
  public static function getEncuestasRes($conn, $sucursal_id, $unidad_id)
  {
    $ret = array("mensaje" => "", 
                 "encuestas" => null);
    $encuestas = array();
    
    try {
			$cond = ""; $params = [];
			if (isset($sucursal_id) && $sucursal_id != "") { $cond = " and r.sucursal_id = ? "; array_push($params, $sucursal_id); }
      if (isset($unidad_id) && $unidad_id != "")     { $cond = " and r.unidad_id = ? "; array_push($params, $unidad_id); }
			
      $cmd = " select e.encuesta_id, e.nombre, e.correos, e.visible_app, count(distinct r.respuesta_id) as cant_respuestas 
               from encuestas e 
							 left join respuestas r on (r.encuesta_id = e.encuesta_id " . $cond . ") 
							 where e.activo = 1 
							 group by e.encuesta_id, e.nombre, e.correos, e.visible_app 
               order by e.nombre ";
      $datos = sqlsrv_query($conn, $cmd, $params);
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
				$row["opciones_con_correo"] = Dashboard::getOpcionesCorreos($conn, $row["encuesta_id"])["opciones"];
				
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
	
  public static function getEncuestasApp($conn)
  {
    $ret = array("mensaje" => "", 
                 "encuestas" => null);
    $encuestas = array();
    
    try {
			$cmd = " select e.encuesta_id, e.nombre, e.nombre COLLATE SQL_Latin1_General_Cp1251_CS_AS as nombre_busqueda, e.imagen_app 
               from encuestas e 
							 where e.activo = 1 and e.visible_app = 1 
               order by e.prioridad, e.nombre ";
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
  
  public static function getOpcionesCorreos($conn, $encuesta_id)
  {
    $ret = array("mensaje" => "", 
                 "opciones" => null);
    $opciones = array();
    
    try {
			$cond = ""; $params = [$encuesta_id];
			
      $cmd = " select ed.enunciado, edo.texto, edo.correos 
               from encuestas e 
							 inner join encuesta_det ed on (ed.encuesta_id = e.encuesta_id) 
							 inner join encuesta_det_opciones edo on (edo.encuesta_det_id = ed.encuesta_det_id and edo.correos is not null)
							 where e.encuesta_id = ? 
               order by ed.orden ";
      $datos = sqlsrv_query($conn, $cmd, $params);
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($opciones, $row);
      }
      $ret["opciones"] = $opciones;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["opciones"] = null;
    }
    
    return $ret;
  }
	
	public static function consultarReporte($conn, $encuesta_id, $sucursal_id, $unidad_id, $respuesta_id = 0) {
		$ret = array("mensaje" => "Reporte aún no implementado", 
                 "registros" => null);
		
		switch ($encuesta_id) {
			case  1: $ret = self::getEncuesta_001($conn, $sucursal_id, $unidad_id); break;
			case  2: $ret = self::getEncuesta_002($conn); break;
			case  4: $ret = self::getEncuesta_004($conn); break;
			case  6: $ret = self::getEncuesta_006($conn, $respuesta_id); break;
			case  7: $ret = self::getEncuesta_007($conn, $respuesta_id); break;
			case  8: $ret = self::getEncuesta_008($conn, $respuesta_id); break;
			case  9: $ret = self::getEncuesta_009($conn, $respuesta_id); break;
			case 10: $ret = self::getEncuesta_010($conn); break;
			case 11: $ret = self::getEncuesta_011($conn); break;
			case 12: $ret = self::getEncuesta_012($conn); break;
			case 13: $ret = self::getEncuesta_013($conn, $respuesta_id); break;
			case 15: $ret = self::getEncuesta_015($conn, $respuesta_id); break;
			case 16: $ret = self::getEncuesta_016($conn, $respuesta_id); break;
			case 17: $ret = self::getEncuesta_017($conn, $respuesta_id); break;
			case 19: $ret = self::getEncuesta_019($conn, $respuesta_id); break;
			case 20: $ret = self::getEncuesta_020($conn, $respuesta_id); break;
			case 21: $ret = self::getEncuesta_021($conn, $respuesta_id); break;
			case 22: $ret = self::getEncuesta_022($conn, $respuesta_id); break;
			case 23: $ret = self::getEncuesta_023($conn, $respuesta_id); break;
			case 24: $ret = self::getEncuesta_024($conn, $respuesta_id); break;
			case 25: $ret = self::getEncuesta_025($conn, $respuesta_id); break;
			case 26: $ret = self::getEncuesta_026($conn, $respuesta_id); break;
			case 27: $ret = self::getEncuesta_027($conn, $respuesta_id); break;
			case 28: $ret = self::getEncuesta_028($conn, $respuesta_id); break;
			case 29: $ret = self::getEncuesta_029($conn, $respuesta_id); break;
			case 30: $ret = self::getEncuesta_030($conn, $respuesta_id); break;
			case 31: $ret = self::getEncuesta_031($conn, $respuesta_id); break;
			case 32: $ret = self::getEncuesta_032($conn, $respuesta_id); break;
			case 33: $ret = self::getEncuesta_033($conn, $respuesta_id); break;
			case 34: $ret = self::getEncuesta_034($conn, $respuesta_id); break;
			case 36: $ret = self::getEncuesta_036($conn, $respuesta_id); break;
			case 37: $ret = self::getEncuesta_037($conn, $respuesta_id); break;
			case 38: $ret = self::getEncuesta_038($conn, $respuesta_id); break;
			case 39: $ret = self::getEncuesta_039($conn, $respuesta_id); break;
			case 41: $ret = self::getEncuesta_041($conn, $respuesta_id); break;
      case 42: $ret = self::getEncuesta_042($conn, $respuesta_id); break;
			case 43: case 44: case 45: case 46: $ret = self::getEncuesta_Cursos_2025($conn, $encuesta_id); break;
      case 47: $ret = self::getEncuesta_047($conn, $respuesta_id); break;
			case 48: $ret = self::getEncuesta_048($conn, $respuesta_id); break;
			case 49: $ret = self::getEncuesta_049($conn, $respuesta_id); break;
			case 50: $ret = self::getEncuesta_050($conn, $respuesta_id); break;
			case 51: $ret = self::getEncuesta_051($conn, $respuesta_id); break;
			case 52: $ret = self::getEncuesta_052($conn, $respuesta_id); break;
			case 53: $ret = self::getEncuesta_053($conn, $respuesta_id); break;
			case 54: $ret = self::getEncuesta_054($conn, $respuesta_id); break;
			case 55: $ret = self::getEncuesta_055($conn, $respuesta_id); break;
			case 56: $ret = self::getEncuesta_056($conn, $respuesta_id); break;
			default: $ret = self::getEncuesta_Def($conn, $encuesta_id, $respuesta_id); break;
		}
		
		$encuesta = Encuesta::getEncuesta($conn, $encuesta_id)["encuesta"];
		$nombre_csv = $encuesta["nombre_csv"];
		if (isset($respuesta_id) && $respuesta_id <> 0) $nombre_csv .= "_" . $respuesta_id;
		$ret["nombre_csv"] = $nombre_csv;
		
		return $ret;
	}
	
	private static function getCondicionDescargaAnnoActual($conn, $encuesta_id)
  {
		$ret = "";
    
    try {
			$params = array($encuesta_id);
			$cmd = " select descargas_anno_actual from encuestas where encuesta_id = ? ";
      $datos = sqlsrv_query($conn, $cmd, $params);
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        if ($row["descargas_anno_actual"] == 1) $ret = " and year(r.fecha) >= year(getdate()) - 1 ";
      }
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) { }
    
    return $ret;
	}

  public static function getEncuesta_Def($conn, $encuesta_id, $respuesta_id)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$cond = ""; $params = array($encuesta_id);
			if (isset($respuesta_id) && $respuesta_id > 0) { $cond = " and r.respuesta_id = ? "; array_push($params, $respuesta_id); }
			
			$cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, rtrim(r.correo) as correo, r.telefono 
from respuestas r 
where r.encuesta_id = ? " . $cond . "
order by r.respuesta_id desc ";
      $datos = sqlsrv_query($conn, $cmd, $params);
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }
  
  public static function getEncuesta_001($conn, $sucursal_id = "", $unidad_id = "")
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 1;
			
			$cond = ""; $params = array($encuesta_id);
			if (isset($sucursal_id) && $sucursal_id != "" && $sucursal_id > 0) { $cond = " and r.sucursal_id = ? "; array_push($params, $sucursal_id); }
      if (isset($unidad_id) && $unidad_id != "" && $unidad_id > 0)     { $cond = " and r.unidad_id = ? "; array_push($params, $unidad_id); }
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, a.nombre as agente, 
			 s.nombre as sucursal, u.nombre as unidad, g.nombre as gestion, 
			 e494.texto as v1, e495.texto as v2, dbo.valor_booleano(r496.valor) as v3, e497.texto as v4, coalesce(r9.valor, '') as v5 
from respuestas r 
inner join agentes a on (a.agente_id = r.agente_id)
inner join sucursales s on (s.sucursal_id = r.sucursal_id)
inner join unidades u on (u.unidad_id = r.unidad_id)
inner join gestiones g on (g.gestion_id = r.gestion_id)
inner join respuesta_det r494 on (r494.respuesta_id = r.respuesta_id and r494.encuesta_det_id = 494)
inner join encuesta_det_opciones e494 on (e494.encuesta_det_opcion_id = r494.valor)
inner join respuesta_det r495 on (r495.respuesta_id = r.respuesta_id and r495.encuesta_det_id = 495)
inner join encuesta_det_opciones e495 on (e495.encuesta_det_opcion_id = r495.valor)
inner join respuesta_det r496 on (r496.respuesta_id = r.respuesta_id and r496.encuesta_det_id = 496)
inner join respuesta_det r497 on (r497.respuesta_id = r.respuesta_id and r497.encuesta_det_id = 497)
inner join encuesta_det_opciones e497 on (e497.encuesta_det_opcion_id = r497.valor)
left join respuesta_det r9 on (r9.respuesta_id = r.respuesta_id and r9.encuesta_det_id = 9)
where r.encuesta_id = ? " . $cond . self::getCondicionDescargaAnnoActual($conn, $encuesta_id) . "
order by r.fecha  ";
      $datos = sqlsrv_query($conn, $cmd, $params);
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
    
    return $ret;
  }
  
  public static function getEncuesta_002($conn)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 2;
			$codigo = "";
			
			// cargo el codigo para el reporte
			$cmd = " select NEWID() as codigo ";
      $datos = sqlsrv_query($conn, $cmd);
      if (!$datos) throw new Exception("Error cargando el código para el reporte (1). " . sqlsrv_errors()[0]['message']);
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        $codigo = $row["codigo"];
      }
      sqlsrv_free_stmt($datos);
			// cargo el codigo para el reporte
			
			
			if ($codigo == "") throw new Exception("Error cargando el código para el reporte (2).");
			
			
			// con base en el codigo, ejecuto el procedimiento almacenado de la carga
			$cmd = " exec stp_rpt_encuesta_002 @codigo = ? ";
      $stmt = sqlsrv_prepare($conn, $cmd, array($codigo));
			if (!sqlsrv_execute($stmt)) throw new Exception("Error ejecutando el procedimiento almacenado.");
      sqlsrv_free_stmt($stmt);
			// con base en el codigo, ejecuto el procedimiento almacenado de la carga
			
			
			// con base en el codigo, ejecuto el procedimiento almacenado de la carga
			$cmd = " select fecha, hora, cedula, estado, genero, puesto, provincia, canton, distrito, 
			                v11, v13, v14, v15, v16, v17, v18, v20, v22, v23, v24, v25, 
											v27, v28, v29, v31, v32, v33, v34, v35, v36, v38, v39, v40, 
											v41, v42, v43, v44, v45, v46, v47, v48, v49, v50, v52, v54, 
											v55, v56, v57 
							 from rpt_reporte_002 r 
							 where codigo = ? " . self::getCondicionDescargaAnnoActual($conn, $encuesta_id) . "
							 order by fecha ";
      $stmt = sqlsrv_prepare($conn, $cmd, array($codigo));
			
			if (!sqlsrv_execute($stmt)) throw new Exception("Error ejecutando la consulta.");

      while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($stmt);
			// con base en el codigo, ejecuto el procedimiento almacenado de la carga
			
			
			// borro los registros de la tabla de reportes
			$cmd = " delete from rpt_reporte_002 where codigo = ? ";
      $stmt = sqlsrv_prepare($conn, $cmd, array($codigo));
			if (!sqlsrv_execute($stmt)) throw new Exception("Error ejecutando la consulta.");
      sqlsrv_free_stmt($stmt);
			// borro los registros de la tabla de reportes
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
    
    return $ret;
  }
  
  public static function getEncuesta_004($conn)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 4;
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, a.nombre as agente, 
       e61.texto + ' ' + coalesce(r61.texto_otro, '') + ' ' + coalesce(r61.texto_exp, '') as v61, 
       e62.texto + ' ' + coalesce(r62.texto_otro, '') + ' ' + coalesce(r62.texto_exp, '') as v62, 
       e63.texto + ' ' + coalesce(r63.texto_otro, '') + ' ' + coalesce(r63.texto_exp, '') as v63, 
       e64.texto + ' ' + coalesce(r64.texto_otro, '') + ' ' + coalesce(r64.texto_exp, '') as v64
from respuestas r 
left join agentes a on (a.agente_id = r.agente_id)
inner join respuesta_det r61 on (r61.respuesta_id = r.respuesta_id and r61.encuesta_det_id = 61)
left join encuesta_det_opciones e61 on (e61.encuesta_det_opcion_id = r61.valor)
inner join respuesta_det r62 on (r62.respuesta_id = r.respuesta_id and r62.encuesta_det_id = 62)
left join encuesta_det_opciones e62 on (e62.encuesta_det_opcion_id = r62.valor)
inner join respuesta_det r63 on (r63.respuesta_id = r.respuesta_id and r63.encuesta_det_id = 63)
left join encuesta_det_opciones e63 on (e63.encuesta_det_opcion_id = r63.valor)
inner join respuesta_det r64 on (r64.respuesta_id = r.respuesta_id and r64.encuesta_det_id = 64)
left join encuesta_det_opciones e64 on (e64.encuesta_det_opcion_id = r64.valor)
where r.encuesta_id = ? " . self::getCondicionDescargaAnnoActual($conn, $encuesta_id) . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
    
    return $ret;
  }
  
  public static function getEncuesta_006($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 6;
			$codigo = "";
			$cond = "";
			
			// cargo el codigo para el reporte
			$cmd = " select NEWID() as codigo ";
      $datos = sqlsrv_query($conn, $cmd);
      if (!$datos) throw new Exception("Error cargando el código para el reporte (1). " . sqlsrv_errors()[0]['message']);
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        $codigo = $row["codigo"];
      }
      sqlsrv_free_stmt($datos);
			// cargo el codigo para el reporte
			
			
			if ($codigo == "") throw new Exception("Error cargando el código para el reporte (2).");
			
			
			// con base en el codigo, ejecuto el procedimiento almacenado de la carga
			if ($respuesta_id == 0) {
				$cmd = " exec stp_rpt_encuesta_006 @codigo = ? ";
				$stmt = sqlsrv_prepare($conn, $cmd, array($codigo));
				$cond = self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			} else {
				$cmd = " exec stp_rpt_encuesta_006_res @codigo = ?, @respuesta_id = ? ";
				$stmt = sqlsrv_prepare($conn, $cmd, array($codigo, $respuesta_id));
			}
			if (!sqlsrv_execute($stmt)) throw new Exception("Error ejecutando el procedimiento almacenado.");
      sqlsrv_free_stmt($stmt);
			// con base en el codigo, ejecuto el procedimiento almacenado de la carga
			
			
			// con base en el codigo, ejecuto el procedimiento almacenado de la carga
			$cmd = " select fecha, hora, cedula, nombre, correo, telefono,  
                      v82, v83, v84, v86, v87, v90, v96, v97, v98, v207, v100
							 from rpt_reporte_006 r 
							 where codigo = ? " . $cond . "
							 order by fecha ";
      $stmt = sqlsrv_prepare($conn, $cmd, array($codigo));
			
			if (!sqlsrv_execute($stmt)) throw new Exception("Error ejecutando la consulta.");

      while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($stmt);
			// con base en el codigo, ejecuto el procedimiento almacenado de la carga
			
			
			// borro los registros de la tabla de reportes
			$cmd = " delete from rpt_reporte_006 where codigo = ? ";
      $stmt = sqlsrv_prepare($conn, $cmd, array($codigo));
			if (!sqlsrv_execute($stmt)) throw new Exception("Error ejecutando la consulta.");
      sqlsrv_free_stmt($stmt);
			// borro los registros de la tabla de reportes
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
    
    return $ret;
  }
  
  public static function getEncuesta_007($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 7;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, rtrim(r.correo) as correo, r.telefono, 
			 e101.texto as v1, rtrim(e102.texto + ' ' + coalesce(r102.texto_otro, '')) as v2, r388.valor as v3, r390.valor as v4, r391.valor as v5 
from respuestas r 
inner join respuesta_det r101 on (r101.respuesta_id = r.respuesta_id and r101.encuesta_det_id = 101)
inner join encuesta_det_opciones e101 on (e101.encuesta_det_opcion_id = r101.valor)
inner join respuesta_det r102 on (r102.respuesta_id = r.respuesta_id and r102.encuesta_det_id = 102)
left join encuesta_det_opciones e102 on (e102.encuesta_det_opcion_id = r102.valor)
left join respuesta_det r388 on (r388.respuesta_id = r.respuesta_id and r388.encuesta_det_id = 388)
left join respuesta_det r390 on (r390.respuesta_id = r.respuesta_id and r390.encuesta_det_id = 390)
left join respuesta_det r391 on (r391.respuesta_id = r.respuesta_id and r391.encuesta_det_id = 391)
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
    
    return $ret;
  }
  
  public static function getEncuesta_008($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 8;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, rtrim(r.correo) as correo, 
			 e103.texto as v1, e105.texto as v2 
from respuestas r 
inner join respuesta_det r103 on (r103.respuesta_id = r.respuesta_id and r103.encuesta_det_id = 103)
inner join encuesta_det_opciones e103 on (e103.encuesta_det_opcion_id = r103.valor)
inner join respuesta_det r105 on (r105.respuesta_id = r.respuesta_id and r105.encuesta_det_id = 105)
inner join encuesta_det_opciones e105 on (e105.encuesta_det_opcion_id = r105.valor)
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
    
    return $ret;
  }
  
  public static function getEncuesta_009($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 9;
			$codigo = "";
			$cond = "";
			
			// cargo el codigo para el reporte
			$cmd = " select NEWID() as codigo ";
      $datos = sqlsrv_query($conn, $cmd);
      if (!$datos) throw new Exception("Error cargando el código para el reporte (1). " . sqlsrv_errors()[0]['message']);
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        $codigo = $row["codigo"];
      }
      sqlsrv_free_stmt($datos);
			// cargo el codigo para el reporte
			
			
			if ($codigo == "") throw new Exception("Error cargando el código para el reporte (2).");
			
			
			// con base en el codigo, ejecuto el procedimiento almacenado de la carga
			if ($respuesta_id == 0) {
				$cmd = " exec stp_rpt_encuesta_009 @codigo = ? ";
				$stmt = sqlsrv_prepare($conn, $cmd, array($codigo));
				$cond = self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			} else {
				$cmd = " exec stp_rpt_encuesta_009_res @codigo = ?, @respuesta_id = ? ";
				$stmt = sqlsrv_prepare($conn, $cmd, array($codigo, $respuesta_id));
			}
			if (!sqlsrv_execute($stmt)) throw new Exception("Error ejecutando el procedimiento almacenado.");
      sqlsrv_free_stmt($stmt);
			// con base en el codigo, ejecuto el procedimiento almacenado de la carga
			
			
			// con base en el codigo, ejecuto el procedimiento almacenado de la carga
			$cmd = " select fecha, hora, cedula, nombre, est_civil, correo, fecha_nac, 
                      v01, v02, v03, v04, v05, v06, v07, v08, v09, v10, v11, v12, 
                      v13, v14, v15, v16, v17, v18
							 from rpt_reporte_009 r 
							 where codigo = ? " . $cond . "
							 order by fecha ";
      $stmt = sqlsrv_prepare($conn, $cmd, array($codigo));
			
			if (!sqlsrv_execute($stmt)) throw new Exception("Error ejecutando la consulta.");

      while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($stmt);
			// con base en el codigo, ejecuto el procedimiento almacenado de la carga
			
			
			// borro los registros de la tabla de reportes
			$cmd = " delete from rpt_reporte_009 where codigo = ? ";
      $stmt = sqlsrv_prepare($conn, $cmd, array($codigo));
			if (!sqlsrv_execute($stmt)) throw new Exception("Error ejecutando la consulta.");
      sqlsrv_free_stmt($stmt);
			// borro los registros de la tabla de reportes
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
    
    return $ret;
  }
  
  public static function getEncuesta_010($conn)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 10;
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, rtrim(r.correo) as correo, 
			 e1.texto + ' ' + coalesce(r1.texto_otro, '') + ' ' + coalesce(r1.texto_exp, '') as v1, 
			 dbo.valor_booleano(r2.valor) as v2 
from respuestas r 
inner join respuesta_det r1 on (r1.respuesta_id = r.respuesta_id and r1.encuesta_det_id = 127)
inner join encuesta_det_opciones e1 on (e1.encuesta_det_opcion_id = r1.valor)
inner join respuesta_det r2 on (r2.respuesta_id = r.respuesta_id and r2.encuesta_det_id = 128)
where r.encuesta_id = ? " . self::getCondicionDescargaAnnoActual($conn, $encuesta_id) . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
    
    return $ret;
  }
  
  public static function getEncuesta_011($conn)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 11;
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, rtrim(r.correo) as correo, 
			 e1.texto + ' ' + coalesce(r1.texto_otro, '') + ' ' + coalesce(r1.texto_exp, '') as v1, 
			 dbo.valor_booleano(r2.valor) as v2 
from respuestas r 
inner join respuesta_det r1 on (r1.respuesta_id = r.respuesta_id and r1.encuesta_det_id = 129)
inner join encuesta_det_opciones e1 on (e1.encuesta_det_opcion_id = r1.valor)
inner join respuesta_det r2 on (r2.respuesta_id = r.respuesta_id and r2.encuesta_det_id = 130)
where r.encuesta_id = ? " . self::getCondicionDescargaAnnoActual($conn, $encuesta_id) . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
    
    return $ret;
  }
  
  public static function getEncuesta_012($conn)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 12;
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, r.nombre, 
       e365.texto as v1, r133.valor as v2, dbo.valor_booleano(r134.valor) as v3, r135.valor as v4 
from respuestas r 
inner join respuesta_det r365 on (r365.respuesta_id = r.respuesta_id and r365.encuesta_det_id = 365)
inner join encuesta_det_opciones e365 on (e365.encuesta_det_opcion_id = r365.valor)
inner join respuesta_det r133 on (r133.respuesta_id = r.respuesta_id and r133.encuesta_det_id = 133)
inner join respuesta_det r134 on (r134.respuesta_id = r.respuesta_id and r134.encuesta_det_id = 134)
inner join respuesta_det r135 on (r135.respuesta_id = r.respuesta_id and r135.encuesta_det_id = 135)
where r.encuesta_id = ? " . self::getCondicionDescargaAnnoActual($conn, $encuesta_id) . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
    
    return $ret;
  }
  
  public static function getEncuesta_013($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 13;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, rtrim(r.correo) as correo, r.telefono, 
       e136.texto as v1 
from respuestas r 
inner join respuesta_det r136 on (r136.respuesta_id = r.respuesta_id and r136.encuesta_det_id = 136)
inner join encuesta_det_opciones e136 on (e136.encuesta_det_opcion_id = r136.valor)
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
    
    return $ret;
  }
  
  public static function getEncuesta_015($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 15;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, r200.valor as v01, r202.valor as v02, 
			 r203.valor as v03, r204.valor as v04, r206.valor as v05 
from respuestas r 
inner join respuesta_det r200 on (r200.respuesta_id = r.respuesta_id and r200.encuesta_det_id = 200)
inner join respuesta_det r202 on (r202.respuesta_id = r.respuesta_id and r202.encuesta_det_id = 202)
inner join respuesta_det r203 on (r203.respuesta_id = r.respuesta_id and r203.encuesta_det_id = 203)
inner join respuesta_det r204 on (r204.respuesta_id = r.respuesta_id and r204.encuesta_det_id = 204)
inner join respuesta_det r206 on (r206.respuesta_id = r.respuesta_id and r206.encuesta_det_id = 206)
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
    
    return $ret;
  }
  
  public static function getEncuesta_016($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 16;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, r209.valor as nombre, r210.valor as cedula 
from respuestas r 
inner join respuesta_det r209 on (r209.respuesta_id = r.respuesta_id and r209.encuesta_det_id = 209)
inner join respuesta_det r210 on (r210.respuesta_id = r.respuesta_id and r210.encuesta_det_id = 210)
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
    
    return $ret;
  }
  
  public static function getEncuesta_017($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 17;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, a.nombre as agente, r214.valor as v01, 
			 r215.valor as v02, r216.valor as v03, r217.valor as v04, r218.valor as v05 
from respuestas r 
inner join agentes a on (a.agente_id = r.agente_id)
inner join respuesta_det r214 on (r214.respuesta_id = r.respuesta_id and r214.encuesta_det_id = 214)
inner join respuesta_det r215 on (r215.respuesta_id = r.respuesta_id and r215.encuesta_det_id = 215)
inner join respuesta_det r216 on (r216.respuesta_id = r.respuesta_id and r216.encuesta_det_id = 216)
inner join respuesta_det r217 on (r217.respuesta_id = r.respuesta_id and r217.encuesta_det_id = 217)
inner join respuesta_det r218 on (r218.respuesta_id = r.respuesta_id and r218.encuesta_det_id = 218)
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }
  
  public static function getEncuesta_019($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 19;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, rtrim(r.correo) as correo, r.telefono, 
			 r222.valor as v01, r223.valor as v02, r224.valor as v03, r225.valor as v04, r226.valor as v05, 
			 r227.valor as v06, r228.valor as v07, r229.valor as v08, r230.valor as v09, r231.valor as v10, 
			 r232.valor as v11 
from respuestas r 
left join respuesta_det r222 on (r222.respuesta_id = r.respuesta_id and r222.encuesta_det_id = 222)
inner join respuesta_det r223 on (r223.respuesta_id = r.respuesta_id and r223.encuesta_det_id = 223)
left join respuesta_det r224 on (r224.respuesta_id = r.respuesta_id and r224.encuesta_det_id = 224)
left join respuesta_det r225 on (r225.respuesta_id = r.respuesta_id and r225.encuesta_det_id = 225)
left join respuesta_det r226 on (r226.respuesta_id = r.respuesta_id and r226.encuesta_det_id = 226)
left join respuesta_det r227 on (r227.respuesta_id = r.respuesta_id and r227.encuesta_det_id = 227)
left join respuesta_det r228 on (r228.respuesta_id = r.respuesta_id and r228.encuesta_det_id = 228)
left join respuesta_det r229 on (r229.respuesta_id = r.respuesta_id and r229.encuesta_det_id = 229)
left join respuesta_det r230 on (r230.respuesta_id = r.respuesta_id and r230.encuesta_det_id = 230)
left join respuesta_det r231 on (r231.respuesta_id = r.respuesta_id and r231.encuesta_det_id = 231)
left join respuesta_det r232 on (r232.respuesta_id = r.respuesta_id and r232.encuesta_det_id = 232)
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }
  
  public static function getEncuesta_020($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 20;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, rtrim(r.correo) as correo, r.telefono, 
			 e233.texto as v01  
from respuestas r 
inner join respuesta_det r233 on (r233.respuesta_id = r.respuesta_id and r233.encuesta_det_id = 233)
inner join encuesta_det_opciones e233 on (e233.encuesta_det_opcion_id = r233.valor)
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }
  
  public static function getEncuesta_021($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 21;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, 
			 r238.valor as v01, r240.valor as v02, r241.valor as v03 
from respuestas r 
inner join respuesta_det r238 on (r238.respuesta_id = r.respuesta_id and r238.encuesta_det_id = 238)
inner join respuesta_det r240 on (r240.respuesta_id = r.respuesta_id and r240.encuesta_det_id = 240)
inner join respuesta_det r241 on (r241.respuesta_id = r.respuesta_id and r241.encuesta_det_id = 241)
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }
  
  public static function getEncuesta_022($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 22;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, rtrim(r.correo) as correo, r.telefono, 
			 e242.texto as v01, d243.nombre as v02, r244.valor as v03, r267.valor as v04 
from respuestas r 
inner join respuesta_det r242 on (r242.respuesta_id = r.respuesta_id and r242.encuesta_det_id = 242)
inner join encuesta_det_opciones e242 on (e242.encuesta_det_opcion_id = r242.valor)
left join respuesta_det r243 on (r243.respuesta_id = r.respuesta_id and r243.encuesta_det_id = 243)
left join direcciones_envio d243 on (d243.direccion_id = r243.valor)
left join respuesta_det r244 on (r244.respuesta_id = r.respuesta_id and r244.encuesta_det_id = 244)
left join respuesta_det r267 on (r267.respuesta_id = r.respuesta_id and r267.encuesta_det_id = 267)
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }
  
  public static function getEncuesta_023($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 23;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, rtrim(r.correo) as correo, r.telefono, 
			 e245.texto as v01  
from respuestas r 
inner join respuesta_det r245 on (r245.respuesta_id = r.respuesta_id and r245.encuesta_det_id = 245)
inner join encuesta_det_opciones e245 on (e245.encuesta_det_opcion_id = r245.valor)
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }
  
  public static function getEncuesta_024($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 24;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, rtrim(r.correo) as correo, r.telefono, 
			 e246.texto as v01, d247.nombre as v02 
from respuestas r 
inner join respuesta_det r246 on (r246.respuesta_id = r.respuesta_id and r246.encuesta_det_id = 246)
inner join encuesta_det_opciones e246 on (e246.encuesta_det_opcion_id = r246.valor)
left join respuesta_det r247 on (r247.respuesta_id = r.respuesta_id and r247.encuesta_det_id = 247)
left join direcciones_envio d247 on (d247.direccion_id = r247.valor) 
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }
  
  public static function getEncuesta_025($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 25;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, rtrim(r.correo) as correo, r.telefono, 
			 r249.valor as v01, r250.valor as v02, r251.valor as v03, 
			 r253.valor as v04, r254.valor as v05, r255.valor as v06 
from respuestas r 
inner join respuesta_det r249 on (r249.respuesta_id = r.respuesta_id and r249.encuesta_det_id = 249)
inner join respuesta_det r250 on (r250.respuesta_id = r.respuesta_id and r250.encuesta_det_id = 250)
inner join respuesta_det r251 on (r251.respuesta_id = r.respuesta_id and r251.encuesta_det_id = 251)
inner join respuesta_det r253 on (r253.respuesta_id = r.respuesta_id and r253.encuesta_det_id = 253)
inner join respuesta_det r254 on (r254.respuesta_id = r.respuesta_id and r254.encuesta_det_id = 254)
inner join respuesta_det r255 on (r255.respuesta_id = r.respuesta_id and r255.encuesta_det_id = 255)
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
    
    return $ret;
  }
  
  public static function getEncuesta_026($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 26;
			$codigo = "";
			$cond = "";
			
			// cargo el codigo para el reporte
			$cmd = " select NEWID() as codigo ";
      $datos = sqlsrv_query($conn, $cmd);
      if (!$datos) throw new Exception("Error cargando el código para el reporte (1). " . sqlsrv_errors()[0]['message']);
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        $codigo = $row["codigo"];
      }
      sqlsrv_free_stmt($datos);
			// cargo el codigo para el reporte
			
			
			if ($codigo == "") throw new Exception("Error cargando el código para el reporte (2).");
			
			
			// con base en el codigo, ejecuto el procedimiento almacenado de la carga
			if ($respuesta_id == 0) {
				$cmd = " exec stp_rpt_encuesta_026 @codigo = ? ";
				$stmt = sqlsrv_prepare($conn, $cmd, array($codigo));
				$cond = self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			} else {
				$cmd = " exec stp_rpt_encuesta_026_res @codigo = ?, @respuesta_id = ? ";
				$stmt = sqlsrv_prepare($conn, $cmd, array($codigo, $respuesta_id));
			}
			if (!sqlsrv_execute($stmt)) throw new Exception("Error ejecutando el procedimiento almacenado.");
      sqlsrv_free_stmt($stmt);
			// con base en el codigo, ejecuto el procedimiento almacenado de la carga
			
			
			// con base en el codigo, ejecuto el procedimiento almacenado de la carga
			$cmd = " select fecha, hora, cedula, nombre, 
                      v01, v02, v03, v04, v05, v06, v07
							 from rpt_reporte_026 r 
							 where codigo = ? " . $cond . "
							 order by fecha ";
      $stmt = sqlsrv_prepare($conn, $cmd, array($codigo));
			
			if (!sqlsrv_execute($stmt)) throw new Exception("Error ejecutando la consulta.");

      while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($stmt);
			// con base en el codigo, ejecuto el procedimiento almacenado de la carga
			
			
			// borro los registros de la tabla de reportes
			$cmd = " delete from rpt_reporte_026 where codigo = ? ";
      $stmt = sqlsrv_prepare($conn, $cmd, array($codigo));
			if (!sqlsrv_execute($stmt)) throw new Exception("Error ejecutando la consulta.");
      sqlsrv_free_stmt($stmt);
			// borro los registros de la tabla de reportes
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
    
    return $ret;
  }
  
  public static function getEncuesta_027($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 27;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, 
			 r263.valor as v01  
from respuestas r 
inner join respuesta_det r263 on (r263.respuesta_id = r.respuesta_id and r263.encuesta_det_id = 263)
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }
  
  public static function getEncuesta_028($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 28;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, r.telefono, 
			 r264.valor as v01  
from respuestas r 
inner join respuesta_det r264 on (r264.respuesta_id = r.respuesta_id and r264.encuesta_det_id = 264)
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }
  
  public static function getEncuesta_029($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 29;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r265.valor as v01, 
       case when o589.respuesta_det_id is null then 0 else 1 end as v02, 
       case when o587.respuesta_det_id is null then 0 else 1 end as v03, 
       case when o586.respuesta_det_id is null then 0 else 1 end as v04, 
       case when o588.respuesta_det_id is null then 0 else 1 end as v05, 
       case when o594.respuesta_det_id is null then 0 else 1 end as v06, 
       case when o591.respuesta_det_id is null then 0 else 1 end as v07, 
       case when o595.respuesta_det_id is null then 0 else 1 end as v08, 
       case when o593.respuesta_det_id is null then 0 else 1 end as v09, 
       case when o592.respuesta_det_id is null then 0 else 1 end as v10, 
       case when o596.respuesta_det_id is null then 0 else 1 end as v11, 
       case when o590.respuesta_det_id is null then 0 else 1 end as v12,
       coalesce(o590.texto_otro, '') as v13 
from respuestas r 
inner join respuesta_det r265 on (r265.respuesta_id = r.respuesta_id and r265.encuesta_det_id = 265)
inner join respuesta_det r266 on (r266.respuesta_id = r.respuesta_id and r266.encuesta_det_id = 266)
left join respuesta_det_opciones o589 on (o589.respuesta_det_id = r266.respuesta_det_id and o589.encuesta_det_opcion_id = 589)
left join respuesta_det_opciones o587 on (o587.respuesta_det_id = r266.respuesta_det_id and o587.encuesta_det_opcion_id = 587)
left join respuesta_det_opciones o586 on (o586.respuesta_det_id = r266.respuesta_det_id and o586.encuesta_det_opcion_id = 586)
left join respuesta_det_opciones o588 on (o588.respuesta_det_id = r266.respuesta_det_id and o588.encuesta_det_opcion_id = 588)
left join respuesta_det_opciones o594 on (o594.respuesta_det_id = r266.respuesta_det_id and o594.encuesta_det_opcion_id = 594)
left join respuesta_det_opciones o591 on (o591.respuesta_det_id = r266.respuesta_det_id and o591.encuesta_det_opcion_id = 591)
left join respuesta_det_opciones o595 on (o595.respuesta_det_id = r266.respuesta_det_id and o595.encuesta_det_opcion_id = 595)
left join respuesta_det_opciones o593 on (o593.respuesta_det_id = r266.respuesta_det_id and o593.encuesta_det_opcion_id = 593)
left join respuesta_det_opciones o592 on (o592.respuesta_det_id = r266.respuesta_det_id and o592.encuesta_det_opcion_id = 592)
left join respuesta_det_opciones o596 on (o596.respuesta_det_id = r266.respuesta_det_id and o596.encuesta_det_opcion_id = 596)
left join respuesta_det_opciones o590 on (o590.respuesta_det_id = r266.respuesta_det_id and o590.encuesta_det_opcion_id = 590)
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
    
    return $ret;
  }
  
  public static function getEncuesta_030($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 30;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, rtrim(r.correo) as correo, r.telefono, 
			 e269.texto as v01, e270.texto as v02, cast(r271.valor as decimal(18,2)) as v03, r275.valor as v04, r276.valor as v05, 
			 r278.valor as v06, r272.valor as v07, r273.valor as v08 
from respuestas r 
inner join respuesta_det r269 on (r269.respuesta_id = r.respuesta_id and r269.encuesta_det_id = 269)
inner join encuesta_det_opciones e269 on (e269.encuesta_det_opcion_id = r269.valor)
inner join respuesta_det r270 on (r270.respuesta_id = r.respuesta_id and r270.encuesta_det_id = 270)
inner join encuesta_det_opciones e270 on (e270.encuesta_det_opcion_id = r270.valor)
inner join respuesta_det r271 on (r271.respuesta_id = r.respuesta_id and r271.encuesta_det_id = 271)
inner join respuesta_det r275 on (r275.respuesta_id = r.respuesta_id and r275.encuesta_det_id = 275)
inner join respuesta_det r276 on (r276.respuesta_id = r.respuesta_id and r276.encuesta_det_id = 276)
inner join respuesta_det r278 on (r278.respuesta_id = r.respuesta_id and r278.encuesta_det_id = 278)
left join respuesta_det r272 on (r272.respuesta_id = r.respuesta_id and r272.encuesta_det_id = 272)
left join respuesta_det r273 on (r273.respuesta_id = r.respuesta_id and r273.encuesta_det_id = 273)
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }
  
  public static function getEncuesta_031($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 31;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, rtrim(r.correo) as correo, r.telefono, 
			 d281.nombre as v01
from respuestas r 
inner join respuesta_det r281 on (r281.respuesta_id = r.respuesta_id and r281.encuesta_det_id = 281)
left join direcciones_envio d281 on (d281.direccion_id = r281.valor)
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }
  
  public static function getEncuesta_032($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 32;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, r.nombre, 
			 e282.texto as v01, r283.valor as v02 
from respuestas r 
inner join respuesta_det r282 on (r282.respuesta_id = r.respuesta_id and r282.encuesta_det_id = 282)
inner join encuesta_det_opciones e282 on (e282.encuesta_det_opcion_id = r282.valor)
inner join respuesta_det r283 on (r283.respuesta_id = r.respuesta_id and r283.encuesta_det_id = 283)
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }
  
  public static function getEncuesta_033($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 33;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, rtrim(r.correo) as correo, r.telefono, 
			 e285.texto as v01 
from respuestas r 
inner join respuesta_det r285 on (r285.respuesta_id = r.respuesta_id and r285.encuesta_det_id = 285)
inner join encuesta_det_opciones e285 on (e285.encuesta_det_opcion_id = r285.valor)
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }
  
  public static function getEncuesta_034($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 34;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, 
			 e286.texto v01, r311.valor as v02, coalesce(e287.texto, e312.texto) as v03 
from respuestas r 
inner join respuesta_det r286 on (r286.respuesta_id = r.respuesta_id and r286.encuesta_det_id = 286)
inner join encuesta_det_opciones e286 on (e286.encuesta_det_opcion_id = r286.valor) 
left join respuesta_det r311 on (r311.respuesta_id = r.respuesta_id and r311.encuesta_det_id = 311)
left join respuesta_det r287 on (r287.respuesta_id = r.respuesta_id and r287.encuesta_det_id = 287)
left join encuesta_det_opciones e287 on (e287.encuesta_det_opcion_id = r287.valor)
left join respuesta_det r312 on (r312.respuesta_id = r.respuesta_id and r312.encuesta_det_id = 312)
left join encuesta_det_opciones e312 on (e312.encuesta_det_opcion_id = r312.valor)
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }
  
  public static function getEncuesta_036($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 36;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, 
			 e313.texto v01, r314.valor as v02, e315.texto v03, r316.valor as v04 
from respuestas r 
inner join respuesta_det r313 on (r313.respuesta_id = r.respuesta_id and r313.encuesta_det_id = 313)
inner join encuesta_det_opciones e313 on (e313.encuesta_det_opcion_id = r313.valor) 
left join respuesta_det r314 on (r314.respuesta_id = r.respuesta_id and r314.encuesta_det_id = 314)
inner join respuesta_det r315 on (r315.respuesta_id = r.respuesta_id and r315.encuesta_det_id = 315)
inner join encuesta_det_opciones e315 on (e315.encuesta_det_opcion_id = r315.valor) 
inner join respuesta_det r316 on (r316.respuesta_id = r.respuesta_id and r316.encuesta_det_id = 316)
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }
  
  public static function getEncuesta_037($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 37;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, 
			 e317.texto v01, e318.texto v02, r322.valor as v03, r323.valor as v04, 
			 coalesce(e319.texto, e320.texto) as v05, coalesce(e321.texto, 'Vencimiento') as v06 
from respuestas r 
inner join respuesta_det r317 on (r317.respuesta_id = r.respuesta_id and r317.encuesta_det_id = 317)
inner join encuesta_det_opciones e317 on (e317.encuesta_det_opcion_id = r317.valor) 
inner join respuesta_det r318 on (r318.respuesta_id = r.respuesta_id and r318.encuesta_det_id = 318)
inner join encuesta_det_opciones e318 on (e318.encuesta_det_opcion_id = r318.valor) 
left join respuesta_det r322 on (r322.respuesta_id = r.respuesta_id and r322.encuesta_det_id = 322)
left join respuesta_det r323 on (r323.respuesta_id = r.respuesta_id and r323.encuesta_det_id = 323)
left join respuesta_det r319 on (r319.respuesta_id = r.respuesta_id and r319.encuesta_det_id = 319)
left join encuesta_det_opciones e319 on (e319.encuesta_det_opcion_id = r319.valor)
left join respuesta_det r320 on (r320.respuesta_id = r.respuesta_id and r320.encuesta_det_id = 320)
left join encuesta_det_opciones e320 on (e320.encuesta_det_opcion_id = r320.valor)
left join respuesta_det r321 on (r321.respuesta_id = r.respuesta_id and r321.encuesta_det_id = 321)
left join encuesta_det_opciones e321 on (e321.encuesta_det_opcion_id = r321.valor)
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }
  
  public static function getEncuesta_038($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 38;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, rtrim(r.correo) as correo, r.telefono, 
			 r325.valor as v01, r326.valor as v02, r327.valor as v03, r328.valor as v04, r329.valor as v05, 
			 r331.valor as v06, r332.valor as v07, r333.valor as v08, r334.valor as v09, r335.valor as v10, 
			 r336.valor as v11, r337.valor as v12, r338.valor as v13, r339.valor as v14, r340.valor as v15 
from respuestas r 
inner join respuesta_det r325 on (r325.respuesta_id = r.respuesta_id and r325.encuesta_det_id = 325)
inner join respuesta_det r326 on (r326.respuesta_id = r.respuesta_id and r326.encuesta_det_id = 326)
inner join respuesta_det r327 on (r327.respuesta_id = r.respuesta_id and r327.encuesta_det_id = 327)
inner join respuesta_det r328 on (r328.respuesta_id = r.respuesta_id and r328.encuesta_det_id = 328)
inner join respuesta_det r329 on (r329.respuesta_id = r.respuesta_id and r329.encuesta_det_id = 329)
inner join respuesta_det r331 on (r331.respuesta_id = r.respuesta_id and r331.encuesta_det_id = 331)
left join respuesta_det r332 on (r332.respuesta_id = r.respuesta_id and r332.encuesta_det_id = 332)
left join respuesta_det r333 on (r333.respuesta_id = r.respuesta_id and r333.encuesta_det_id = 333)
left join respuesta_det r334 on (r334.respuesta_id = r.respuesta_id and r334.encuesta_det_id = 334)
left join respuesta_det r335 on (r335.respuesta_id = r.respuesta_id and r335.encuesta_det_id = 335)
left join respuesta_det r336 on (r336.respuesta_id = r.respuesta_id and r336.encuesta_det_id = 336)
left join respuesta_det r337 on (r337.respuesta_id = r.respuesta_id and r337.encuesta_det_id = 337)
left join respuesta_det r338 on (r338.respuesta_id = r.respuesta_id and r338.encuesta_det_id = 338)
left join respuesta_det r339 on (r339.respuesta_id = r.respuesta_id and r339.encuesta_det_id = 339)
left join respuesta_det r340 on (r340.respuesta_id = r.respuesta_id and r340.encuesta_det_id = 340)
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }
  
  public static function getEncuesta_039($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 39;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, rtrim(r.correo) as correo, r.telefono, 
			 r341.valor as v01, r343.valor as v02, r344.valor as v03, r345.valor as v04, r346.valor as v05, 
			 r347.valor as v06, r348.valor as v07, r349.valor as v08, r350.valor as v09, r351.valor as v10, 
			 r352.valor as v11 
from respuestas r 
inner join respuesta_det r341 on (r341.respuesta_id = r.respuesta_id and r341.encuesta_det_id = 341)
inner join respuesta_det r343 on (r343.respuesta_id = r.respuesta_id and r343.encuesta_det_id = 343)
left join respuesta_det r344 on (r344.respuesta_id = r.respuesta_id and r344.encuesta_det_id = 344)
left join respuesta_det r345 on (r345.respuesta_id = r.respuesta_id and r345.encuesta_det_id = 345)
left join respuesta_det r346 on (r346.respuesta_id = r.respuesta_id and r346.encuesta_det_id = 346)
left join respuesta_det r347 on (r347.respuesta_id = r.respuesta_id and r347.encuesta_det_id = 347)
left join respuesta_det r348 on (r348.respuesta_id = r.respuesta_id and r348.encuesta_det_id = 348)
left join respuesta_det r349 on (r349.respuesta_id = r.respuesta_id and r349.encuesta_det_id = 349)
left join respuesta_det r350 on (r350.respuesta_id = r.respuesta_id and r350.encuesta_det_id = 350)
left join respuesta_det r351 on (r351.respuesta_id = r.respuesta_id and r351.encuesta_det_id = 351)
left join respuesta_det r352 on (r352.respuesta_id = r.respuesta_id and r352.encuesta_det_id = 352)
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }
  
  public static function getEncuesta_041($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 41;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, 
			 r359.valor as v01, r360.valor as v02, e361.texto as v03 
from respuestas r 
inner join respuesta_det r359 on (r359.respuesta_id = r.respuesta_id and r359.encuesta_det_id = 359)
inner join respuesta_det r360 on (r360.respuesta_id = r.respuesta_id and r360.encuesta_det_id = 360)
inner join respuesta_det r361 on (r361.respuesta_id = r.respuesta_id and r361.encuesta_det_id = 361)
inner join encuesta_det_opciones e361 on (e361.encuesta_det_opcion_id = r361.valor)
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }
  
  public static function getEncuesta_042($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 42;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, rtrim(r.correo) as correo, r.telefono, 
			 r363.valor as v01, r364.valor as v02 
from respuestas r 
inner join respuesta_det r363 on (r363.respuesta_id = r.respuesta_id and r363.encuesta_det_id = 363)
inner join respuesta_det r364 on (r364.respuesta_id = r.respuesta_id and r364.encuesta_det_id = 364)
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }

  public static function getEncuesta_Cursos_2025($conn, $encuesta_id)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, g.nombre as genero, 
			 rd1.valor as v01, rd2.valor as v02, rd3.valor as v03, rd4.valor as v04 
from respuestas r 
inner join generos g on (g.genero_id = r.genero_id)
inner join encuesta_det ed1 on (ed1.encuesta_id = r.encuesta_id and ed1.orden = 1)
inner join encuesta_det ed2 on (ed2.encuesta_id = r.encuesta_id and ed2.orden = 2)
inner join encuesta_det ed3 on (ed3.encuesta_id = r.encuesta_id and ed3.orden = 3)
inner join encuesta_det ed4 on (ed4.encuesta_id = r.encuesta_id and ed4.orden = 4)
inner join respuesta_det rd1 on (rd1.respuesta_id = r.respuesta_id and rd1.encuesta_det_id = ed1.encuesta_det_id)
inner join respuesta_det rd2 on (rd2.respuesta_id = r.respuesta_id and rd2.encuesta_det_id = ed2.encuesta_det_id)
inner join respuesta_det rd3 on (rd3.respuesta_id = r.respuesta_id and rd3.encuesta_det_id = ed3.encuesta_det_id)
inner join respuesta_det rd4 on (rd4.respuesta_id = r.respuesta_id and rd4.encuesta_det_id = ed4.encuesta_det_id)
where r.encuesta_id = ? 
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }

  public static function getEncuesta_047($conn, $encuesta_id)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
      $encuesta_id = 47;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
			$cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, rtrim(r.correo) as correo, 
			 r386.valor as v01 
from respuestas r 
inner join respuesta_det r386 on (r386.respuesta_id = r.respuesta_id and r386.encuesta_det_id = 386)
where r.encuesta_id = ? 
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }
  
  public static function getEncuesta_048($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 48;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, r392.valor as v01, e393.texto v02, r394.valor as v03, 
       r395.valor as v04, r396.valor as v05, r397.valor as v06, r398.valor as v07, r399.valor as v08 
from respuestas r 
inner join respuesta_det r392 on (r392.respuesta_id = r.respuesta_id and r392.encuesta_det_id = 392)
inner join respuesta_det r393 on (r393.respuesta_id = r.respuesta_id and r393.encuesta_det_id = 393)
inner join encuesta_det_opciones e393 on (e393.encuesta_det_opcion_id = r393.valor) 
inner join respuesta_det r394 on (r394.respuesta_id = r.respuesta_id and r394.encuesta_det_id = 394)
inner join respuesta_det r395 on (r395.respuesta_id = r.respuesta_id and r395.encuesta_det_id = 395)
inner join respuesta_det r396 on (r396.respuesta_id = r.respuesta_id and r396.encuesta_det_id = 396)
inner join respuesta_det r397 on (r397.respuesta_id = r.respuesta_id and r397.encuesta_det_id = 397)
inner join respuesta_det r398 on (r398.respuesta_id = r.respuesta_id and r398.encuesta_det_id = 398)
left join respuesta_det r399 on (r399.respuesta_id = r.respuesta_id and r399.encuesta_det_id = 399)
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }

  public static function getEncuesta_049($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 49;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, 
       r400.valor as v01, r401.valor as v02, r420.valor as v03, r421.valor as v04, r422.valor as v05, r402.valor as v06, 
       r403.valor as v07, r404.valor as v08, r405.valor as v09, STRING_AGG(o424.texto, ', ') as v10, r406.valor as v11, 
       r407.valor as v12, r408.valor as v13, r409.valor as v14, r410.valor as v15, r411.valor as v16, r412.valor as v17, 
       r413.valor as v18, r414.valor as v19, r415.valor as v20, p416.nombre as v21, p417.nombre as v22, p419.nombre as v23, 
       r418.valor as v24 /* no modificar esta linea */
from respuestas r 
inner join respuesta_det r400 on (r400.respuesta_id = r.respuesta_id and r400.encuesta_det_id = 400)
inner join respuesta_det r401 on (r401.respuesta_id = r.respuesta_id and r401.encuesta_det_id = 401)
left join respuesta_det r420 on (r420.respuesta_id = r.respuesta_id and r420.encuesta_det_id = 420)
left join respuesta_det r421 on (r421.respuesta_id = r.respuesta_id and r421.encuesta_det_id = 421)
left join respuesta_det r422 on (r422.respuesta_id = r.respuesta_id and r422.encuesta_det_id = 422)
inner join respuesta_det r402 on (r402.respuesta_id = r.respuesta_id and r402.encuesta_det_id = 402)
inner join respuesta_det r403 on (r403.respuesta_id = r.respuesta_id and r403.encuesta_det_id = 403)
inner join respuesta_det r404 on (r404.respuesta_id = r.respuesta_id and r404.encuesta_det_id = 404)
inner join respuesta_det r405 on (r405.respuesta_id = r.respuesta_id and r405.encuesta_det_id = 405)
left join respuesta_det r424 on (r424.respuesta_id = r.respuesta_id and r424.encuesta_det_id = 424)
left join respuesta_det_opciones ro424 on (ro424.respuesta_det_id = r424.respuesta_det_id)
left join encuesta_det_opciones o424 on (o424.encuesta_det_opcion_id = ro424.encuesta_det_opcion_id)
inner join respuesta_det r406 on (r406.respuesta_id = r.respuesta_id and r406.encuesta_det_id = 406)
inner join respuesta_det r407 on (r407.respuesta_id = r.respuesta_id and r407.encuesta_det_id = 407)
inner join respuesta_det r408 on (r408.respuesta_id = r.respuesta_id and r408.encuesta_det_id = 408)
inner join respuesta_det r409 on (r409.respuesta_id = r.respuesta_id and r409.encuesta_det_id = 409)
inner join respuesta_det r410 on (r410.respuesta_id = r.respuesta_id and r410.encuesta_det_id = 410)
inner join respuesta_det r411 on (r411.respuesta_id = r.respuesta_id and r411.encuesta_det_id = 411)
inner join respuesta_det r412 on (r412.respuesta_id = r.respuesta_id and r412.encuesta_det_id = 412)
inner join respuesta_det r413 on (r413.respuesta_id = r.respuesta_id and r413.encuesta_det_id = 413)
inner join respuesta_det r414 on (r414.respuesta_id = r.respuesta_id and r414.encuesta_det_id = 414)
inner join respuesta_det r415 on (r415.respuesta_id = r.respuesta_id and r415.encuesta_det_id = 415)
left join respuesta_det r416 on (r416.respuesta_id = r.respuesta_id and r416.encuesta_det_id = 416)
left join puestos_caja p416 on (p416.puesto_id = r416.valor)
left join respuesta_det r417 on (r417.respuesta_id = r.respuesta_id and r417.encuesta_det_id = 417)
left join puestos_caja p417 on (p417.puesto_id = r417.valor)
left join respuesta_det r419 on (r419.respuesta_id = r.respuesta_id and r419.encuesta_det_id = 419)
left join puestos_caja p419 on (p419.puesto_id = r419.valor)
inner join respuesta_det r418 on (r418.respuesta_id = r.respuesta_id and r418.encuesta_det_id = 418)
where r.encuesta_id = ? " . $cond . "
group by convert(varchar, r.creado, 120), convert(varchar, r.creado, 8), r400.valor, r401.valor, r420.valor, 
         r421.valor, r422.valor, r402.valor, r403.valor, r404.valor, r405.valor, r406.valor, r407.valor, 
         r408.valor, r409.valor, r410.valor, r411.valor, r412.valor, r413.valor, r414.valor, r415.valor, 
         p416.nombre, p417.nombre, p419.nombre, r418.valor, r.fecha 
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }
  
  public static function getEncuesta_050($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 50;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, rtrim(r.correo) as correo, r.telefono, 
			 d425.nombre as v01, e426.texto v02
from respuestas r 
inner join respuesta_det r425 on (r425.respuesta_id = r.respuesta_id and r425.encuesta_det_id = 425)
inner join direcciones_envio d425 on (d425.direccion_id = r425.valor)
inner join respuesta_det r426 on (r426.respuesta_id = r.respuesta_id and r426.encuesta_det_id = 426)
inner join encuesta_det_opciones e426 on (e426.encuesta_det_opcion_id = r426.valor) 
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }
  
  public static function getEncuesta_051($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 51;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, 
			 rtrim(e427.texto + ' ' + coalesce(r427.texto_otro, '')) as v1 
from respuestas r 
inner join respuesta_det r427 on (r427.respuesta_id = r.respuesta_id and r427.encuesta_det_id = 427)
left join encuesta_det_opciones e427 on (e427.encuesta_det_opcion_id = r427.valor)
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
    
    return $ret;
  }
  
  public static function getEncuesta_052($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 52;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, 
			 rtrim(e428.texto + ' ' + coalesce(r428.texto_otro, '')) as v1, e429.texto as v2, e431.texto as v3, e432.texto as v4, e433.texto as v5, 
       e434.texto as v6, e435.texto as v7, e436.texto as v8, e437.texto as v9, e438.texto as v10, 
       r439.valor as v11, r440.valor as v12
from respuestas r 
inner join respuesta_det r428 on (r428.respuesta_id = r.respuesta_id and r428.encuesta_det_id = 428)
inner join encuesta_det_opciones e428 on (e428.encuesta_det_opcion_id = r428.valor)
inner join respuesta_det r429 on (r429.respuesta_id = r.respuesta_id and r429.encuesta_det_id = 429)
inner join encuesta_det_opciones e429 on (e429.encuesta_det_opcion_id = r429.valor)
inner join respuesta_det r431 on (r431.respuesta_id = r.respuesta_id and r431.encuesta_det_id = 431)
inner join encuesta_det_opciones e431 on (e431.encuesta_det_opcion_id = r431.valor)
inner join respuesta_det r432 on (r432.respuesta_id = r.respuesta_id and r432.encuesta_det_id = 432)
inner join encuesta_det_opciones e432 on (e432.encuesta_det_opcion_id = r432.valor)
inner join respuesta_det r433 on (r433.respuesta_id = r.respuesta_id and r433.encuesta_det_id = 433)
inner join encuesta_det_opciones e433 on (e433.encuesta_det_opcion_id = r433.valor)
inner join respuesta_det r434 on (r434.respuesta_id = r.respuesta_id and r434.encuesta_det_id = 434)
inner join encuesta_det_opciones e434 on (e434.encuesta_det_opcion_id = r434.valor)
inner join respuesta_det r435 on (r435.respuesta_id = r.respuesta_id and r435.encuesta_det_id = 435)
inner join encuesta_det_opciones e435 on (e435.encuesta_det_opcion_id = r435.valor)
inner join respuesta_det r436 on (r436.respuesta_id = r.respuesta_id and r436.encuesta_det_id = 436)
inner join encuesta_det_opciones e436 on (e436.encuesta_det_opcion_id = r436.valor)
inner join respuesta_det r437 on (r437.respuesta_id = r.respuesta_id and r437.encuesta_det_id = 437)
inner join encuesta_det_opciones e437 on (e437.encuesta_det_opcion_id = r437.valor)
inner join respuesta_det r438 on (r438.respuesta_id = r.respuesta_id and r438.encuesta_det_id = 438)
inner join encuesta_det_opciones e438 on (e438.encuesta_det_opcion_id = r438.valor)
inner join respuesta_det r439 on (r439.respuesta_id = r.respuesta_id and r439.encuesta_det_id = 439)
inner join respuesta_det r440 on (r440.respuesta_id = r.respuesta_id and r440.encuesta_det_id = 440)
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
    
    return $ret;
  }
  
  public static function getEncuesta_053($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 53;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, 
			 e445.texto v01, e446.texto v02, e447.texto v03
from respuestas r 
inner join respuesta_det r445 on (r445.respuesta_id = r.respuesta_id and r445.encuesta_det_id = 445)
inner join encuesta_det_opciones e445 on (e445.encuesta_det_opcion_id = r445.valor) 
inner join respuesta_det r446 on (r446.respuesta_id = r.respuesta_id and r446.encuesta_det_id = 446)
inner join encuesta_det_opciones e446 on (e446.encuesta_det_opcion_id = r446.valor) 
inner join respuesta_det r447 on (r447.respuesta_id = r.respuesta_id and r447.encuesta_det_id = 447)
inner join encuesta_det_opciones e447 on (e447.encuesta_det_opcion_id = r447.valor) 
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }
  
  public static function getEncuesta_054($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 54;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, 
       e449.texto + ' ' + coalesce(r449.texto_otro, '') + ' ' + coalesce(r449.texto_exp, '') as v01, 
       e450.texto v02, e453.texto v03, e454.texto v04, e455.texto v05, e456.texto v06, e459.texto v07, 
       e460.texto v08, e461.texto v09, r463.valor as v10, e464.texto v11
from respuestas r 
inner join respuesta_det r449 on (r449.respuesta_id = r.respuesta_id and r449.encuesta_det_id = 449) 
inner join encuesta_det_opciones e449 on (e449.encuesta_det_opcion_id = r449.valor) 
inner join respuesta_det r450 on (r450.respuesta_id = r.respuesta_id and r450.encuesta_det_id = 450) 
inner join encuesta_det_opciones e450 on (e450.encuesta_det_opcion_id = r450.valor) 
inner join respuesta_det r453 on (r453.respuesta_id = r.respuesta_id and r453.encuesta_det_id = 453) 
inner join encuesta_det_opciones e453 on (e453.encuesta_det_opcion_id = r453.valor) 
inner join respuesta_det r454 on (r454.respuesta_id = r.respuesta_id and r454.encuesta_det_id = 454) 
inner join encuesta_det_opciones e454 on (e454.encuesta_det_opcion_id = r454.valor) 
inner join respuesta_det r455 on (r455.respuesta_id = r.respuesta_id and r455.encuesta_det_id = 455) 
inner join encuesta_det_opciones e455 on (e455.encuesta_det_opcion_id = r455.valor) 
inner join respuesta_det r456 on (r456.respuesta_id = r.respuesta_id and r456.encuesta_det_id = 456) 
inner join encuesta_det_opciones e456 on (e456.encuesta_det_opcion_id = r456.valor) 
inner join respuesta_det r459 on (r459.respuesta_id = r.respuesta_id and r459.encuesta_det_id = 459) 
inner join encuesta_det_opciones e459 on (e459.encuesta_det_opcion_id = r459.valor) 
inner join respuesta_det r460 on (r460.respuesta_id = r.respuesta_id and r460.encuesta_det_id = 460) 
inner join encuesta_det_opciones e460 on (e460.encuesta_det_opcion_id = r460.valor) 
inner join respuesta_det r461 on (r461.respuesta_id = r.respuesta_id and r461.encuesta_det_id = 461) 
inner join encuesta_det_opciones e461 on (e461.encuesta_det_opcion_id = r461.valor) 
inner join respuesta_det r463 on (r463.respuesta_id = r.respuesta_id and r463.encuesta_det_id = 463) 
inner join respuesta_det r464 on (r464.respuesta_id = r.respuesta_id and r464.encuesta_det_id = 464) 
inner join encuesta_det_opciones e464 on (e464.encuesta_det_opcion_id = r464.valor) 
where r.encuesta_id = ? " . $cond . "
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }
  
  public static function getEncuesta_055($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 55;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, rtrim(r.correo) as correo, r.telefono, 
       e465.texto v01, concat(coalesce(r466.valor, ''), coalesce(r467.valor, ''), coalesce(r468.valor, ''), coalesce(r469.valor, '')) v02, r475.valor v03, 
       e491.texto v04, concat(coalesce(d493.nombre, ''), coalesce(p473.nombre, ''), ' ', coalesce(r477.valor, '')) v05, r470.valor v06, r471.valor v07, r472.valor v08, 
       r476.valor v09, r474.valor v10, r478.valor v11, r479.valor v12 
from respuestas r 
inner join respuesta_det r465 on (r465.respuesta_id = r.respuesta_id and r465.encuesta_det_id = 465) 
inner join encuesta_det_opciones e465 on (e465.encuesta_det_opcion_id = r465.valor) 
left join respuesta_det r466 on (r466.respuesta_id = r.respuesta_id and r466.encuesta_det_id = 466) 
left join respuesta_det r467 on (r467.respuesta_id = r.respuesta_id and r467.encuesta_det_id = 467) 
left join respuesta_det r468 on (r468.respuesta_id = r.respuesta_id and r468.encuesta_det_id = 468) 
left join respuesta_det r469 on (r469.respuesta_id = r.respuesta_id and r469.encuesta_det_id = 469) 
inner join respuesta_det r475 on (r475.respuesta_id = r.respuesta_id and r475.encuesta_det_id = 475) 

inner join respuesta_det r491 on (r491.respuesta_id = r.respuesta_id and r491.encuesta_det_id = 491) 
inner join encuesta_det_opciones e491 on (e491.encuesta_det_opcion_id = r491.valor) 

left join respuesta_det r493 on (r493.respuesta_id = r.respuesta_id and r493.encuesta_det_id = 493) 
left join direcciones_envio d493 on (d493.direccion_id = r493.valor) 

left join respuesta_det r473 on (r473.respuesta_id = r.respuesta_id and r473.encuesta_det_id = 473) 
left join provincias p473 on (p473.provincia_id = r473.valor)
left join respuesta_det r477 on (r477.respuesta_id = r.respuesta_id and r477.encuesta_det_id = 477) 

inner join respuesta_det r470 on (r470.respuesta_id = r.respuesta_id and r470.encuesta_det_id = 470) 
inner join respuesta_det r471 on (r471.respuesta_id = r.respuesta_id and r471.encuesta_det_id = 471) 
inner join respuesta_det r472 on (r472.respuesta_id = r.respuesta_id and r472.encuesta_det_id = 472) 
inner join respuesta_det r476 on (r476.respuesta_id = r.respuesta_id and r476.encuesta_det_id = 476) 
inner join respuesta_det r474 on (r474.respuesta_id = r.respuesta_id and r474.encuesta_det_id = 474) 
inner join respuesta_det r478 on (r478.respuesta_id = r.respuesta_id and r478.encuesta_det_id = 478) 
inner join respuesta_det r479 on (r479.respuesta_id = r.respuesta_id and r479.encuesta_det_id = 479) 
where r.encuesta_id = ? $cond 
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }
  
  public static function getEncuesta_056($conn, $respuesta_id = 0)
  {
    $ret = array("mensaje" => "", 
                 "registros" => null);
    $registros = array();
    
    try {
			$encuesta_id = 56;
			$cond = ($respuesta_id > 0) ? (" and r.respuesta_id = " . $respuesta_id . " ") : "";
			if ($respuesta_id == 0) $cond .= self::getCondicionDescargaAnnoActual($conn, $encuesta_id);
			
      $cmd = " 
select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, rtrim(r.correo) as correo, r.telefono, 
       e481.texto v01, e482.texto v02, e484.texto v03, r485.valor v04, r487.valor v05 
from respuestas r 
inner join respuesta_det r481 on (r481.respuesta_id = r.respuesta_id and r481.encuesta_det_id = 481) 
inner join encuesta_det_opciones e481 on (e481.encuesta_det_opcion_id = r481.valor) 
inner join respuesta_det r482 on (r482.respuesta_id = r.respuesta_id and r482.encuesta_det_id = 482) 
inner join encuesta_det_opciones e482 on (e482.encuesta_det_opcion_id = r482.valor) 
inner join respuesta_det r484 on (r484.respuesta_id = r.respuesta_id and r484.encuesta_det_id = 484) 
inner join encuesta_det_opciones e484 on (e484.encuesta_det_opcion_id = r484.valor) 
inner join respuesta_det r485 on (r485.respuesta_id = r.respuesta_id and r485.encuesta_det_id = 485) 
left join respuesta_det r487 on (r487.respuesta_id = r.respuesta_id and r487.encuesta_det_id = 487) 
where r.encuesta_id = ? $cond 
order by r.fecha ";
      $datos = sqlsrv_query($conn, $cmd, array($encuesta_id));
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
        array_push($registros, $row);
      }
			$ret["registros"] = $registros;
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["registros"] = null;
    }
		
    return $ret;
  }
  
}

?>