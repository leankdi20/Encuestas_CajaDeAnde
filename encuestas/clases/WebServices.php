<?php

class WebServices {
  private static $cache_cliente_core = array();

  public static function informacionSocioPorCedula($cedula)
  {
    $ret = array(
      "nombre" => "",
      "correo" => "",
      "activo" => false,
      "idTipoSocio" => null,
      "desTipoSocio" => ""
    );

    $cedula = self::aplicarMascara($cedula);
    if (strlen($cedula) < 9) return $ret;

    if (self::usaServicioCore()) {
      $cliente = self::obtenerInformacionClienteCore($cedula);
      if ($cliente["msg"] != "") return $ret;

      $valor = $cliente["valor"];
      $ret["activo"] = ($valor["activo"] ?? 0) == 1;

      if ($ret["activo"]) {
        $ret["nombre"] = $valor["nombre"] ?? "";
        $ret["correo"] = $valor["correo"] ?? "";
        $ret["idTipoSocio"] = $valor["idTipoSocio"] ?? null;
        $ret["desTipoSocio"] = $valor["desTipoSocio"] ?? "";
      }

      return $ret;
    }

    $ret["nombre"] = self::nombrePorCedula($cedula);
    $ret["correo"] = self::correoPorCedula($cedula);
    $ret["activo"] = self::clienteActivoPorCedula($cedula);

    return $ret;
  }
  
  public static function nombrePorCedula($cedula)
  {
    $cedula = self::aplicarMascara($cedula);
    
    if (strlen($cedula) < 9) return "";

    if (self::usaServicioCore()) {
      $cliente = self::obtenerInformacionClienteCore($cedula);
      if ($cliente["msg"] != "" || ($cliente["valor"]["activo"] ?? 0) != 1) return "";

      return $cliente["valor"]["nombre"] ?? "";
    }
    
    $request = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
                               xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
                               xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                  <soap:Body>
                    <NombreCompleto xmlns="http://localhost/">
                      <strCedula>' . $cedula . '</strCedula>
                    </NombreCompleto>
                  </soap:Body>
                </soap:Envelope>';
    
    $valor = self::ejecutar($request);
		
		if ($valor["msg"] != "") return ""; // $valor["msg"];
		
    return $valor['valor']['soapBody']['NombreCompletoResponse']['NombreCompletoResult']['objNombreCompleto']['NombreCompleto'] ?? "";
  }
  
  public static function correoPorCedula($cedula)
  {
    $cedula = self::aplicarMascara($cedula);
    
    if (strlen($cedula) < 9) return "";

    if (self::usaServicioCore()) {
      $cliente = self::obtenerInformacionClienteCore($cedula);
      if ($cliente["msg"] != "" || ($cliente["valor"]["activo"] ?? 0) != 1) return "";

      return $cliente["valor"]["correo"] ?? "";
    }
    
    $request = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
                               xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
                               xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                  <soap:Body>
                    <ObtenerDireccionEmail xmlns="http://tempuri.org/">
                      <Codigo_Cliente>' . $cedula . '</Codigo_Cliente>
                    </ObtenerDireccionEmail>
                  </soap:Body>
                </soap:Envelope>';
    
    $valor = self::ejecutar($request, $ruta = "WsWebBanking.asmx");
		
		if ($valor["msg"] != "") return ""; // $valor["msg"];
		
		if (isset($valor['valor']['soapBody']['ObtenerDireccionEmailResponse']['ObtenerDireccionEmailResult']['objObtenerDireccionEmail']['CorreoElectronico']))
			return $valor['valor']['soapBody']['ObtenerDireccionEmailResponse']['ObtenerDireccionEmailResult']['objObtenerDireccionEmail']['CorreoElectronico'] ?? "";
		
		if (isset($valor['valor']['soapBody']['ObtenerDireccionEmailResponse']['ObtenerDireccionEmailResult']['objObtenerDireccionEmail'][0]['CorreoElectronico']))
			return $valor['valor']['soapBody']['ObtenerDireccionEmailResponse']['ObtenerDireccionEmailResult']['objObtenerDireccionEmail'][0]['CorreoElectronico'] ?? "";
    
		return "";
  }
  
  public static function clienteActivoPorCedula($cedula)
  {
    $cedula = self::aplicarMascara($cedula);
    
    if (strlen($cedula) < 9) return false;

    if (self::usaServicioCore()) {
      $cliente = self::obtenerInformacionClienteCore($cedula);
      if ($cliente["msg"] != "") return false;

      return ($cliente["valor"]["activo"] ?? 0) == 1;
    }
    
    $request = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
                               xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
                               xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                  <soap:Body>
                    <ObtenerClientePorCedula xmlns="http://tempuri.org/">
                      <cedula>' . $cedula . '</cedula>
                    </ObtenerClientePorCedula>
                  </soap:Body>
                </soap:Envelope>';
    
    $valor = self::ejecutar($request, $ruta = "WsWebBanking.asmx");
		
		if ($valor["msg"] != "") return false; // $valor["msg"];
		
		if (isset($valor['valor']['soapBody']['ObtenerClientePorCedulaResponse']['ObtenerClientePorCedulaResult']['objObtenerCliente']['EstadoCliente']))
			return ($valor['valor']['soapBody']['ObtenerClientePorCedulaResponse']['ObtenerClientePorCedulaResult']['objObtenerCliente']['EstadoCliente'] ?? 0) == 1;
		
		if (isset($valor['valor']['soapBody']['ObtenerClientePorCedulaResponse']['ObtenerClientePorCedulaResult']['objObtenerCliente'][0]['EstadoCliente']))
			return ($valor['valor']['soapBody']['ObtenerClientePorCedulaResponse']['ObtenerClientePorCedulaResult']['objObtenerCliente'][0]['EstadoCliente'] ?? 0) == 1;
    
		return false;
  }
  
  public static function clienteActivoPorCedulaObj($cedula)
  {
		$ret = array("activo" => false, "msg" => "", "idTipoSocio" => null, "desTipoSocio" => "");
		
		try {
			$cedula = self::aplicarMascara($cedula);
    
			if (strlen($cedula) < 9) throw new Exception("Cedula " . $cedula . " con formato incorrecto");

      if (self::usaServicioCore()) {
        $cliente = self::obtenerInformacionClienteCore($cedula);
        if ($cliente["msg"] != "") throw new Exception($cliente["msg"]);

        $valor = $cliente["valor"];
        $ret["activo"] = ($valor["activo"] ?? 0) == 1;
        $ret["idTipoSocio"] = $valor["idTipoSocio"] ?? null;
        $ret["desTipoSocio"] = $valor["desTipoSocio"] ?? "";
        return $ret;
      }
			
			$request = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
																 xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
																 xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
										<soap:Body>
											<ObtenerClientePorCedula xmlns="http://tempuri.org/">
												<cedula>' . $cedula . '</cedula>
											</ObtenerClientePorCedula>
										</soap:Body>
									</soap:Envelope>';
			
			$valor = self::ejecutar($request, $ruta = "WsWebBanking.asmx");
			
			if ($valor["msg"] != "") throw new Exception($valor["msg"]);
			
			if (isset($valor['valor']['soapBody']['ObtenerClientePorCedulaResponse']['ObtenerClientePorCedulaResult']['objObtenerCliente']['EstadoCliente']))
				$ret["activo"] = ($valor['valor']['soapBody']['ObtenerClientePorCedulaResponse']['ObtenerClientePorCedulaResult']['objObtenerCliente']['EstadoCliente'] ?? 0) == 1;
			
			if (isset($valor['valor']['soapBody']['ObtenerClientePorCedulaResponse']['ObtenerClientePorCedulaResult']['objObtenerCliente'][0]['EstadoCliente']))
				$ret["activo"] = ($valor['valor']['soapBody']['ObtenerClientePorCedulaResponse']['ObtenerClientePorCedulaResult']['objObtenerCliente'][0]['EstadoCliente'] ?? 0) == 1;
		} catch (Exception $e) {
			$ret["activo"] = false;
      $ret["msg"] = $e->getMessage();
    }
    
    return $ret;
  }
  
  public static function aplicarMascara($cedula)
  {
    $cedula = str_replace("-", "", $cedula);
    $cedula = str_replace(" ", "", $cedula);
    
    if (strlen($cedula) == 9) {
      $cedula = "0".$cedula;
      // $cedula = Funciones::str_insert("-", 6, $cedula);
      // $cedula = Funciones::str_insert("-", 2, $cedula);
      return $cedula;
    }
    if (strlen($cedula) == 8) {
			$cedula = "00".$cedula;
      // $cedula = Funciones::str_insert("-", 4, $cedula);
      // $cedula = Funciones::str_insert("-", 1, $cedula);
      return $cedula;
    }
    // if (strlen($cedula) == 12) 
      return $cedula;
    
    return "";
  }

  private static function usaServicioCore()
  {
    $ws_api = $_SESSION["servidor_api"] ?? "";

    return stripos($ws_api, "ObtenerInformacionClienteCORE") !== false;
  }

  private static function obtenerInformacionClienteCore($cedula)
  {
    // CORE requiere el cero inicial; mantener la cédula como texto en el JSON.
    $cedula = self::aplicarMascara((string)$cedula);
    if (isset(self::$cache_cliente_core[$cedula])) return self::$cache_cliente_core[$cedula];

		$ret = array("valor" => null, "msg" => "");
    $ws_api = $_SESSION["servidor_api"] ?? "";
    $request = json_encode(array("cedula" => $cedula));
    $headers = array("Content-Type: application/json",
                     "Accept: application/json",
                     "Content-length: " . strlen($request));

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ws_api);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $res = curl_exec($ch);
    $err_cod = curl_errno($ch);
    $err_msg = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (PHP_VERSION_ID < 80000) curl_close($ch);

    if ($err_cod != "0") {
			$ret["msg"] = "Error curl " . $err_cod . ". " . $err_msg;
      self::$cache_cliente_core[$cedula] = $ret;
			return $ret;
		}

    if ($http_code < 200 || $http_code >= 300) {
			$ret["msg"] = "Error HTTP " . $http_code . " consultando CORE";
      self::$cache_cliente_core[$cedula] = $ret;
			return $ret;
		}

    $valor = json_decode($res, true);
    if (!is_array($valor)) {
			$ret["msg"] = "Respuesta invalida consultando CORE";
      self::$cache_cliente_core[$cedula] = $ret;
			return $ret;
		}

    if (($valor["codResultado"] ?? 0) != 1) {
			$ret["msg"] = "CORE no encontro informacion para la cedula";
      self::$cache_cliente_core[$cedula] = $ret;
			return $ret;
		}

    $ret["valor"] = $valor;
    self::$cache_cliente_core[$cedula] = $ret;

    return $ret;
  }
  
  public static function ejecutar($request, $endpoint = "WsCGPWeb.asmx")
  {
		$ret = array("valor" => null, "msg" => "");
		
    $ws_api = $_SESSION["servidor_api"] . $endpoint;
    $headers = array("Content-type: text/xml; charset=utf-8",
                     "Accept: text/xml",
                     "Cache-Control: no-cache",
                     "Pragma: no-cache",
                     "Content-length: " . strlen($request));
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_URL, $ws_api);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $res = curl_exec($ch); 
    $err_cod = curl_errno($ch);
    $err_msg = curl_error($ch);

    if (PHP_VERSION_ID < 80000) curl_close($ch);
    
    if ($err_cod != "0") {
			$ret["msg"] = "Error curl " . $err_cod . ". " . $err_msg;
			return $ret;
		}

    $resp_xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $res);
    $resp_sim = simplexml_load_string($resp_xml);
    $resp_json = json_encode($resp_sim, true);
		
		$ret["valor"] = json_decode($resp_json, true);
    
    return $ret;
  }
  
}

?>
