<?php
include_once "header.php";

// echo openssl_decrypt("8b8g7zNjyLI6bBzm71Q=", ENC_CYPHER_VAL, ENC_KEY) . "<br><br>";

// $ret = [];
// $cmd = " select top 10 respuesta_id, creado
// 		 from respuestas 
// 		 order by respuesta_id desc ";
// $datos = sqlsrv_query($conn, $cmd, []);

// if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);

// while ($row = sqlsrv_fetch_array($datos, SQLSRV_FETCH_ASSOC)) {
// 	array_push($ret, $row);
// }
// echo json_encode($ret);

$c = "209020028"; // "01-1041-0314"; 0110410314
$c = isset($_GET["cedula"]) ? $_GET["cedula"] : $c;
echo json_encode(WebServices::correoPorCedula($c)) . "<br><br>";
echo json_encode(WebServices::nombrePorCedula($c)) . "<br><br>";
echo "Activo - " . WebServices::clienteActivoPorCedula($c) . "<br><br>";

// //echo openssl_decrypt("8b8g7zNjyLI6bBzm71Q=", ENC_CYPHER_VAL, ENC_KEY);

// $cedula = $c; // "401140707"; // '02-0407-0374';
// $request = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
// 													 xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
// 													 xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
// 							<soap:Body>
// 								<ObtenerClientePorCedula xmlns="http://tempuri.org/">
//                       <cedula>' . WebServices::aplicarMascara($cedula) . '</cedula>
//                     </ObtenerClientePorCedula>
// 							</soap:Body>
// 						</soap:Envelope>';
						
// $temp = peg_ejecutar($request);
// echo json_encode($temp) . "<br><br>";

// $request = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
// 													 xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
// 													 xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
// 							<soap:Body>
// 								<ObtenerDireccionEmail xmlns="http://tempuri.org/">
//                       <Codigo_Cliente>' . WebServices::aplicarMascara($cedula) . '</Codigo_Cliente>
//                     </ObtenerDireccionEmail>
// 							</soap:Body>
// 						</soap:Envelope>';
						
// $temp = peg_ejecutar($request);
// echo json_encode($temp) . "<br><br>";

// $request = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
// 													 xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
// 													 xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
// 							<soap:Body>
// 								<NombreCompleto xmlns="http://localhost/">
//                       <strCedula>' . WebServices::aplicarMascara($cedula) . '</strCedula>
//                     </NombreCompleto>
// 							</soap:Body>
// 						</soap:Envelope>';
						
// $temp = peg_ejecutar($request, "WsCGPWeb.asmx");
// echo json_encode($temp);

// function peg_ejecutar($request, $endpoint = "WsWebBanking.asmx")
// {
// 	$ret = array("valor" => null, "msg" => "");
	
// 	$ws_api = "http://172.16.8.226/WS_PROCOBA/" . $endpoint;
// 	$headers = array("Content-type: text/xml; charset=utf-8",
// 									 "Accept: text/xml",
// 									 "Cache-Control: no-cache",
// 									 "Pragma: no-cache",
// 									 "Content-length: " . strlen($request));
	
// 	$ch = curl_init();
// 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
// 	curl_setopt($ch, CURLOPT_URL, $ws_api);
// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// 	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
// 	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
// 	curl_setopt($ch, CURLOPT_POST, true);
// 	curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
// 	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// 	$res = curl_exec($ch); 
// 	$err_cod = curl_errno($ch);
// 	$err_msg = curl_error($ch);

// 	curl_close($ch);
	
// 	if ($err_cod != "0") {
// 		$ret["msg"] = "Error curl " . $err_cod . ". " . $err_msg;
// 		return $ret;
// 	}

// 	$resp_xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $res);
// 	$resp_sim = simplexml_load_string($resp_xml);
// 	$resp_json = json_encode($resp_sim, true);
	
// 	$ret["valor"] = json_decode($resp_json, true);
	
// 	return $ret;
// }

include_once "footer.php";
?>