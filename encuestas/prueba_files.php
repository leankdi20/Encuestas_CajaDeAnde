<?php
include_once "header.php";

$token = authenticate(CITRIX_HOST, CITRIX_CLIENT_ID, CITRIX_CLIENT_SEC, CITRIX_USER, CITRIX_PASS);
//authenticate($citrix_hostname, $citrix_client_id, $citrix_client_secret, $citrix_username, $citrix_password);

echo "Hostname - " . CITRIX_HOST . "<br>";
echo "Access Token obtenido - " . $token->access_token . "<br><br>";

if (isset($_GET["accion"]) && $_GET["accion"] == "upload") {
  foreach ($_FILES as $post_name => $file) {
    $file_type = $file["type"];
    $file_tmp_name = $file["tmp_name"];
    $file_size = $file["size"];
    $file_name = $file["name"];
    
    echo $post_name . " - " . json_encode($file)."<br><br>";

    $url_publico = subir_archivo_citrix($file_type, $file_tmp_name, $file_size, $file_name);
    echo "URL PUBLICO - " . $url_publico;
  }
}

echo "
<html lang='en'>
  <head></head>
  <body>
    <form action='prueba_files.php?accion=upload' method='post' enctype='multipart/form-data'>
      <div>
        <label>Seleccione archivo 1</label>
        <input type='file' class='form-control-file' name='adjunto_1' accept='.jpg,.jpeg,.png,.gif,.pdf,.txt'>
      </div>
      <!--<div>
        <label>Seleccione archivo 2</label>
        <input type='file' class='form-control-file' name='adjunto_2' accept='.jpg,.jpeg,.png,.gif,.pdf,.txt'>
      </div>-->
      <div>
        <button type='submit' class='btn btn-info'>Subir</button>
      </div>
    </form>
  </body>
</html>";

include_once "footer.php";
?>