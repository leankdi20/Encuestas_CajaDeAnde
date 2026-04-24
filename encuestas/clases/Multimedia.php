<?php

function subir_archivo_citrix($file_type, $file_tmp_name, $file_size, $file_name, $citrix_folder_id, 
															$respuesta_id, $encuesta_det_id) {
  $url_publico = "";
  $token = authenticate(CITRIX_HOST, CITRIX_CLIENT_ID, CITRIX_CLIENT_SEC, CITRIX_USER, CITRIX_PASS);
  
  try {
    $file_name = trim(addslashes($file_name));
    $file_name = str_replace(' ', '_', $file_name);
    $file_name = limpiar_texto_archivos($file_name);
    $file_name = $respuesta_id . "_" . $encuesta_det_id . "_" . time() . "_" . $file_name;

    $url_publico = upload_file($token, $citrix_folder_id, $file_tmp_name, $file_type, $file_name);
  } catch (Exception $e) { }
  
  return $url_publico;
}

function limpiar_texto_archivos($texto) {
  $caracteres_por_quitar = ['“', '"', '&', '‘', "'", '<', '>', '#',             // por alguna extraña razon php no tomaba las tildes digitadas en Brackets pero si las copiadas de un archivo
                            'Á', 'É', 'Í', 'Ó', 'Ú', 'á', 'é', 'í', 'ó', 'ú',   // tildes copiadas del archivo
                            'Á', 'É', 'Í', 'Ó', 'Ú', 'á', 'é', 'í', 'ó', 'ú',   // tildes digitadas directamente en brackets (por si acaso)
                            'Ñ', 'ñ', 'ñ'];
  $ret = $texto;

  foreach ($caracteres_por_quitar as $caracter) {
    $ret = str_replace($caracter, '', $ret);
  }
  
  $caracteres_por_reemplazar = [" "];
  foreach ($caracteres_por_quitar as $caracter) {
    $ret = str_replace($caracter, '_', $ret);
  }

  return $ret;
}
/*
function authenticate($hostname, $client_id, $client_secret, $username, $password) {
  $uri = "https://".$hostname."/oauth/token";
	
  $body_data = array("grant_type" => "password", "client_id" => $client_id, "client_secret" => $client_secret,
                     "username" => $username, "password" => $password);
  //$headers = array('Content-Type:application/x-www-form-urlencoded');
   
  //$curl_resp = curl_response($uri, $headers, $body_data);
  
  $headers[] = "Content-Type: application/json";
  $curl_file = curl_response($uri, $headers, $body_data, true);
  $http_code = $curl_resp["code"];
	
  $token = NULL;
  if ($http_code == 200) $token = $curl_resp["res"];
  else echo "ERROR_CITRIX - " . json_encode($curl_file) . "<br>";
	
  return $token;
}
*/
function authenticate($hostname, $client_id, $client_secret, $username, $password) {
  $uri = "https://".$hostname."/oauth/token";
	
  $body_data = array("grant_type" => "password", "client_id" => $client_id, "client_secret" => $client_secret,
                     "username" => $username, "password" => $password);
  $headers = array('Content-Type:application/x-www-form-urlencoded');
   
  $curl_resp = curl_response($uri, $headers, $body_data);
  $http_code = $curl_resp["code"];
	
  $token = NULL;
  if ($http_code == 200) $token = $curl_resp["res"];
	
  return $token;
}

function get_authorization_header($token) { 
  return array("Authorization: Bearer ".$token->access_token); 
}

function get_hostname($token) { 
  return $token->subdomain.".sf-api.com"; 
}

/* function get_folder_with_query_parameters($token, $item_id) {
  $uri = "https://".get_hostname($token)."/sf/v3/Items(".$item_id.")?\$expand=Children&\$select=Id,Name,Children/Id,Children/Name,Children/CreationDate";
  
  $headers = get_authorization_header($token);

  $curl_resp = curl_response($uri, $headers);
  $root = $curl_resp["res"];
  
  echo $root->Id." ".$root->Name." "."<br>";
  if (property_exists($root, "Children")) {
    foreach($root->Children as $child) {
      echo $child->Id." ".$child->Name."<br>";
    }
  }
} */

function upload_file($token, $folder_id, $local_path, $mime_type, $file_name) {
  $url_publico = "";
  
  $uri = "https://" . get_hostname($token) . "/sf/v3/Items(" . $folder_id . ")/Upload";
  
  $headers = get_authorization_header($token);

  $curl_resp = curl_response($uri, $headers);

  $http_code = $curl_resp["code"];
  $upload_config = $curl_resp["res"];

  if ($http_code == 200) {
    $post["File1"] = new CurlFile($local_path, $mime_type, $file_name);

    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_URL, $upload_config->ChunkUri);
    curl_setopt ($ch, CURLOPT_POST, true);
    curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt ($ch, CURLOPT_VERBOSE, FALSE);
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt ($ch, CURLOPT_HEADER, true);

    $upload_response = curl_exec($ch);
    curl_exec($ch);
		
    $http_code_up = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close ($ch);

    if ($http_code_up == 200) {
      $uri = "https://" . get_hostname($token) . "/sf/v3/Items(" . $folder_id . ")/ByPath?path=/" . $file_name;
      
      $curl_info_file = curl_response($uri, $headers);
      $info_file_resp = $curl_info_file["res"];

      $new_file_id = $info_file_resp->Id;
      
      $uri = "https://" . get_hostname($token) . "/sf/v3/Shares";
      
      $params = [];
      $params["ShareType"] = "Send";
      $params["Items"][] = array("Id" => $new_file_id);
      $params["RequireLogin"] = false;
      $params["RequireUserInfo"] = false;
      $params["MaxDownloads"] = -1;

      $headers[] = "Content-Type: application/json";
      $curl_file = curl_response($uri, $headers, $params, true);
      $file_resp = $curl_file["res"];
      
      $url_publico = $file_resp->Uri;
    }
  } else {
    echo "Error subiendo el archivo. " . get_hostname($token) . " - " . $curl_resp["err"];
  }
  
  return $url_publico;
}

function curl_response($uri, $headers = [], $body_data = [], $es_json = false, $timeout = 30) {
  $ch = curl_init();
  
  curl_setopt($ch, CURLOPT_URL, $uri);
  curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_SSLVERSION, 1);
  curl_setopt($ch, CURLOPT_VERBOSE, FALSE);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  if (count($body_data) > 0) {
    if ($es_json) $data = json_encode($body_data);
    else          $data = http_build_query($body_data);
    
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_POST, TRUE);
  }

  $curl_response = curl_exec($ch);

  $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $curl_err_no = curl_errno($ch);
  $curl_err = curl_error($ch);

  curl_close ($ch);

  return array("code" => $http_code, "err_no" => $curl_err_no, "err" => $curl_err, "res" => json_decode($curl_response));
}

?>