<?php

class Funciones {
  
  public static function str_insert($str_to_insert, $pos, $string)
  {
    return substr($string, 0, $pos) . $str_to_insert . substr($string, $pos);
  }
  
  public static function name_otro($name_det)
  {
    return $name_det . "_otro";
  }
  
  public static function name_exp($name_det)
  {
    return $name_det . "_exp";
  }
  
  public static function name_file($name_det)
  {
    return $name_det . "_file";
  }
  
  public static function limpiar_texto_archivos($texto) {
    $caracteres_por_quitar = ['“', '"', '&', '‘', "'", '<', '>', '#',             // por alguna extraña razon php no tomaba las tildes digitadas en Brackets pero si las copiadas de un archivo
                              'Á', 'É', 'Í', 'Ó', 'Ú', 'á', 'é', 'í', 'ó', 'ú',   // tildes copiadas del archivo
                              'Á', 'É', 'Í', 'Ó', 'Ú', 'á', 'é', 'í', 'ó', 'ú'];  // tildes digitadas directamente en brackets (por si acaso)
    $ret = $texto;

    foreach ($caracteres_por_quitar as $caracter) {
      $ret = str_replace($caracter, '', $ret);
    }

    return $ret;
  }

  public static function sana_input($input) {
    return filter_var($input, FILTER_SANITIZE_STRING);
  }

  public static function insertar_log($conn, $texto) {
    try {
      $cmd = " INSERT INTO logs (texto) VALUES (?);";
      $params = array($texto);
      $datos = sqlsrv_query($conn, $cmd, $params);
      
      if (!$datos) throw new Exception(sqlsrv_errors()[0]['message']);
      
      sqlsrv_free_stmt($datos);
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["datos"] = null;
    }
  }

}

?>