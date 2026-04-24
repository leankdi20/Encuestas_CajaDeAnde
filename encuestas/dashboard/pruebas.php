<?php
include_once "includes_dashboard.php";

$sucursal_id = 0; // isset($_GET["sucursal_id"]) ? $_GET["sucursal_id"] : 0;
	$unidad_id = 0; // isset($_GET["unidad_id"]) ? $_GET["unidad_id"] : 0;
	$respuesta_id = 0; // isset($_GET["respuesta_id"]) ? $_GET["respuesta_id"] : 0;

$ret = Dashboard::consultarReporte($conn, $_GET["id"], $sucursal_id, $unidad_id, $respuesta_id);

// try {
// 	$registros = array();
    
//     try {
// 		$encuesta_id = 1;
		
// 		$cond = ""; $params = array($encuesta_id);
// 		if (isset($sucursal_id) && $sucursal_id != "") { $cond = " and r.sucursal_id = ? "; array_push($params, $sucursal_id); }
//       	if (isset($unidad_id) && $unidad_id != "")     { $cond = " and r.unidad_id = ? "; array_push($params, $unidad_id); }
			
//       	$cmd = " 
// select convert(varchar, r.creado, 120) as fecha, convert(varchar, r.creado, 8) as hora, rtrim(r.cedula) as cedula, r.nombre, a.nombre as agente, 
// 	   s.nombre as sucursal, u.nombre as unidad, g.nombre as gestion, 
// 	   r3.valor as v1, r4.valor as v2, r5.valor as v3, r59.valor as v4, 
// 	   r6.valor as v5, r7.valor as v6, r8.valor as v7, 
// 	   coalesce(r9.valor, '') as v8 
// from respuestas r 
// inner join agentes a on (a.agente_id = r.agente_id)
// inner join sucursales s on (s.sucursal_id = r.sucursal_id)
// inner join unidades u on (u.unidad_id = r.unidad_id)
// inner join gestiones g on (g.gestion_id = r.gestion_id)
// inner join respuesta_det r3 on (r3.respuesta_id = r.respuesta_id and r3.encuesta_det_id = 3)
// inner join respuesta_det r4 on (r4.respuesta_id = r.respuesta_id and r4.encuesta_det_id = 4)
// inner join respuesta_det r5 on (r5.respuesta_id = r.respuesta_id and r5.encuesta_det_id = 5)
// inner join respuesta_det r59 on (r59.respuesta_id = r.respuesta_id and r59.encuesta_det_id = 59)
// inner join respuesta_det r6 on (r6.respuesta_id = r.respuesta_id and r6.encuesta_det_id = 6)
// inner join respuesta_det r7 on (r7.respuesta_id = r.respuesta_id and r7.encuesta_det_id = 7)
// inner join respuesta_det r8 on (r8.respuesta_id = r.respuesta_id and r8.encuesta_det_id = 8)
// left join respuesta_det r9 on (r9.respuesta_id = r.respuesta_id and r9.encuesta_det_id = 9)
// where r.encuesta_id = ? 
// order by r.fecha ";
// 		$datos = sqlsrv_query($conn, $cmd, $params);
		
// 		if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
		
// 		while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
// 			array_push($registros, $row);
// 		}
// 		$ret["registros"] = $registros;
      
//      	sqlsrv_free_stmt($datos);
//     } catch (Exception $e) {
//       $ret["mensaje"] = $e->getMessage();
//       $ret["registros"] = null;
//     }
// } catch (Exception $e) {
// 	$ret["mensaje"] = $e->getMessage();
// }

echo json_encode($ret);

sqlsrv_close($conn);
?>