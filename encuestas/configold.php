<?php


session_start();
$_SESSION["dev"] = false;


define("ENC_CYPHER_VAL",	"AES-128-CTR");
define("ENC_KEY", 			 	"JavaTpoint");

/* BASE DE DATOS */
//$db_hostname = 'WEBDESDB-VSRV1\SQLSERVERWEB';
//$db_hostname = 'WEBDESDB-VSRV1\SQLSERVERWEBCERT';
$db_hostname = 'DBWEBPROD-SRV';
$db_database = 'encuestas_db';
$db_username = 'encuestas';
$db_password = openssl_decrypt("8b8g7zNjyLI6bBzm71Q=", ENC_CYPHER_VAL, ENC_KEY);
$mensajeDB = "";
/* BASE DE DATOS */


/* API QUE CONSULTA DATOS DE LOS ACCIONISTAS */
$_SESSION["servidor_api"] = "http://172.16.8.226/WS_PROCOBA/";
/* API QUE CONSULTA DATOS DE LOS ACCIONISTAS */


/* DATOS DE CONFIGURACION PARA SUBIDA DE ARCHIVOS A CITRIX */
define("CITRIX_HOST", 			 "cajadeande.sharefile.com");
define("CITRIX_USER", 			 "formularios_web_test2@cajadeande.fi.cr");
define("CITRIX_PASS", 			 openssl_decrypt("3rsx4nZxj6c4fku57RI2gD0L9g==", ENC_CYPHER_VAL, ENC_KEY));
define("CITRIX_CLIENT_ID", 	 "wHFWHKxqWUcFyLaNh1QccX3COZEYMtxV");
define("CITRIX_CLIENT_SEC",  "ej1lE8tN7N2r0tIZpXfB5HHz6TeWct5zf3CSEnfHErEHCdxQ");
define("CITRIX_FOLDER_COMP", "foade54b-fb70-4c8f-87af-da94ea346153");
define("CITRIX_FOLDER_PER",  "fo12b9e7-e7f2-4d3e-a056-abc99d3c0d7e");
/* DATOS DE CONFIGURACION PARA SUBIDA DE ARCHIVOS A CITRIX */


try {
  $connOptions = array("Database"     => $db_database, 
                       "Uid"          => $db_username, 
                       "PWD"          => $db_password, 
                       "CharacterSet" => "UTF-8");
  $conn = sqlsrv_connect($db_hostname, $connOptions);
  
  if ($conn == false) {
    throw new Exception("No fue posible conectar a la base de datos. " . 
                        sqlsrv_errors()[0]['message']);
  }
} catch (Exception $e) {  
  $mensajeDB = $e->getMessage();  
}

?>