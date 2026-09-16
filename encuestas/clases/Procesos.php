<?php

class Procesos {
  
  // -------------------------------------- ENCUESTAS ------------------------------------- //
  
  public static function encuesta($conn, $encuesta_id)
  {
    $ret = array("mensaje" => "", 
                 "encuesta" => null);
    
    try {
      // obtengo el objeto de la encuesta
      $enc = Encuesta::getEncuesta($conn, $encuesta_id);
      
      $mens = $enc["mensaje"];
      if ($mens != "") throw new Exception("Error cargando la encuesta. " . $mens);
      
      $encuesta = $enc["encuesta"];
      // obtengo el objeto de la encuesta
      
      
      $ret["encuesta"] = $encuesta;  
    } catch (Exception $e) {
      $ret["mensaje"] = $e->getMessage();
      $ret["encuesta"] = null;
    }
    
    return $ret;
  }
  
  public static function crear_respuesta($conn, $encuesta_id, $obj_post)
  {
    $ret = array("mensaje" => "");
    
    try {
      sqlsrv_begin_transaction($conn);
      
      
      // ----------------------- BLOQUE DE VALIDACIONES ----------------------- //
      $en = self::encuesta($conn, $encuesta_id);
      
      $mens = $en["mensaje"];
      if ($mens != "") throw new Exception("Error cargando la encuesta. " . $mens);
      
      $enc = $en["encuesta"];
      
      
      $nombre          = filter_var(htmlspecialchars(trim($obj_post["nombre"] ?? "")));
      $cedula          = filter_var(htmlspecialchars(trim($obj_post["cedula"] ?? "")));
      $sucursal_id     = filter_var(htmlspecialchars($obj_post["sucursal"] ?? ""));
      $unidad_id       = filter_var(htmlspecialchars($obj_post["unidad"] ?? ""));
      $agente_id       = filter_var(htmlspecialchars($obj_post["agente"] ?? ""));
      $gestion_id      = filter_var(htmlspecialchars($obj_post["gestion"] ?? ""));
      $correo          = filter_var(htmlspecialchars(trim($obj_post["correo"] ?? "")));
      $telefono        = filter_var(htmlspecialchars(trim($obj_post["telefono"] ?? "")));
      
      $fecha_nac       = filter_var(htmlspecialchars($obj_post["fecha_nac"] ?? ""));
      $estado_emp_id   = filter_var(htmlspecialchars($obj_post["estado_emp"] ?? ""));
      $estado_otro     = filter_var(htmlspecialchars($obj_post["estado_emp_otro"] ?? ""));
      $genero_id       = filter_var(htmlspecialchars($obj_post["genero"] ?? ""));
      $genero_otro     = filter_var(htmlspecialchars($obj_post["genero_otro"] ?? ""));
      $puesto_id       = filter_var(htmlspecialchars($obj_post["puesto"] ?? ""));
      $puesto_otro     = filter_var(htmlspecialchars($obj_post["puesto_otro"] ?? ""));
      $provincia_id    = filter_var(htmlspecialchars($obj_post["provincia"] ?? ""));
      $canton_id       = filter_var(htmlspecialchars($obj_post["canton"] ?? ""));
      $distrito_id     = filter_var(htmlspecialchars($obj_post["distrito"] ?? ""));
      $est_civil_id    = filter_var(htmlspecialchars($obj_post["est_civil"] ?? ""));
      
      
      $nombre         = ($nombre == "") ? null : $nombre;
      $cedula         = ($cedula == "") ? null : $cedula;
      $sucursal_id    = ($sucursal_id == "") ? null : $sucursal_id;
      $unidad_id      = ($unidad_id == "") ? null : $unidad_id;
      $agente_id      = ($agente_id == "") ? null : $agente_id;
      $gestion_id     = ($gestion_id == "") ? null : $gestion_id;
      $correo         = ($correo == "") ? null : $correo;
      $telefono       = ($telefono == "") ? null : $telefono;
      $fecha_nac      = ($fecha_nac == "") ? null : $fecha_nac;
      $estado_emp_id  = ($estado_emp_id == "") ? null : $estado_emp_id;
      $estado_otro    = ($estado_otro == "") ? null : $estado_otro;
      $genero_id      = ($genero_id == "") ? null : $genero_id;
      $genero_otro    = ($genero_otro == "") ? null : $genero_otro;
      $puesto_id      = ($puesto_id == "") ? null : $puesto_id;
      $puesto_otro    = ($puesto_otro == "") ? null : $puesto_otro;
      $provincia_id   = ($provincia_id == "") ? null : $provincia_id;
      $canton_id      = ($canton_id == "") ? null : $canton_id;
      $distrito_id    = ($distrito_id == "") ? null : $distrito_id;
      $est_civil_id   = ($est_civil_id == "") ? null : $est_civil_id;
      
      $fecha = date('Y-m-d');
      
      $datos_enc = [];
      if ($nombre != "") $datos_enc["nombre"] = $nombre;
      if ($cedula != "") $datos_enc["cedula"] = $cedula;
      if ($correo != "") $datos_enc["correo"] = $correo;
      if ($telefono != "") $datos_enc["telefono"] = $telefono;
      
      $incluye_cedula = $enc["incluye_cedula"] == 1;
			$cedula_oblig = $enc["cedula_oblig"] == 1;
	  
      if ($enc["incluye_nombre"] == 1 && $enc["nombre_oblig"] == 1 && ($nombre == "" || !isset($nombre)))
        throw new Exception("El nombre es obligatorio");
      if ($incluye_cedula && $cedula_oblig && ($cedula == "" || !isset($cedula)))
        throw new Exception("La cédula es obligatoria");
      if ($enc["incluye_sucursal"] == 1 && $enc["sucursal_oblig"] && !isset($sucursal_id))
        throw new Exception("La sucursal es obligatoria");
      if ($enc["incluye_unidad"] == 1 && $enc["unidad_oblig"] && !isset($unidad_id))
        throw new Exception("La unidad es obligatoria");
      if ($enc["incluye_agente"] == 1 && $enc["agente_oblig"] && !isset($agente_id))
        throw new Exception("El agente es obligatorio");
      if ($enc["incluye_gestion"] == 1 && $enc["gestion_oblig"] && !isset($gestion_id))
        throw new Exception("La gestión es obligatoria");
      if ($enc["incluye_correo"] == 1 && $enc["correo_oblig"] && !isset($correo))
        throw new Exception("El correo es obligatorio");
      if ($enc["incluye_telefono"] == 1 && $enc["telefono_oblig"] && !isset($telefono))
        throw new Exception("El teléfono es obligatorio");
      
      
      if ($enc["incluye_fecha_nac"] == 1 && $enc["fecha_nac_oblig"] && !isset($fecha_nac))
        throw new Exception("La fecha de nacimiento es obligatoria");
      if ($enc["incluye_estado_emp"] == 1 && $enc["estado_emp_oblig"] && !isset($estado_emp_id))
        throw new Exception("El estado del colaborador es obligatorio");
      if ($enc["incluye_genero"] == 1 && $enc["genero_oblig"] && !isset($genero_id))
        throw new Exception("El género es obligatorio");
      if ($enc["incluye_ubicacion"] == 1 && $enc["ubicacion_oblig"] && 
          (!isset($provincia_id) || !isset($canton_id) || !isset($distrito_id)))
        throw new Exception("La provincia, cantón y distrito son obligatorios");
      if ($enc["incluye_puesto"] == 1 && $enc["puesto_oblig"] && !isset($puesto_id))
        throw new Exception("El puesto es obligatorio");
      if ($enc["incluye_est_civil"] == 1 && $enc["est_civil_oblig"] && !isset($est_civil_id))
        throw new Exception("El estado civil es obligatorio");
      
      
			// valido la cedula digitada con lo que nos devuelve SISTECA
			if ($incluye_cedula == 1 && $cedula_oblig) {
				$res_ced = WebServices::clienteActivoPorCedulaObj($cedula);
				
				if ($res_ced["activo"]) {
					Funciones::insertar_log($conn, "La cedula $cedula esta activa");
				} else {
					Funciones::insertar_log($conn, "La cedula $cedula presenta error ($encuesta_id). " . $res_ced["msg"]);
					throw new Exception("Por favor asegurarse de digitar una cédula válida de accionista de Caja de ANDE. Cédula digitada: $cedula");
				}
			}
			// valido la cedula digitada con lo que nos devuelve SISTECA
	  
	  
      $correos = "";
      if (isset($enc["correos"])) $correos = trim($enc["correos"]);
      
      
      $hay_preguntas = false;
      foreach ($obj_post as $llave => $valor) {
        $partes = explode("_", $llave);
        $count_partes = count($partes);
        
        if ($count_partes > 0 && $count_partes < 3) {
          if ($partes[0] == "det") { // me aseguro de jalar solo los names de los enunciados de detalle
            $hay_preguntas = true;
            
            $det_id = $partes[1];
            
            $enc_det = EncuestaDet::getEncuestaDet($conn, $det_id);

            $mens = $enc_det["mensaje"];
            if ($mens != "") throw new Exception("Error cargando el detalle de la encuesta. " . $mens);

            $encuesta_det = $enc_det["encuesta_det"];
            $tipo_id = $encuesta_det["tipo_id"];
            
            
            if ($encuesta_det["opcional"] == 0) {
              if ($tipo_id == EDT_ARCHIVO) {
                $llave_file = Funciones::name_file($llave);
                
                if (!isset($_FILES[$llave_file])) throw new Exception("Hay archivos que no se cargaron correctamente (llave no encontrada $llave_file).");
                  
                $file_size = $_FILES[$llave_file]["size"];
                $file_max_size = $encuesta_det["meta"][META_FILE_SIZE];
                
                if ($file_size == 0) 
                  throw new Exception("Hay archivos que no se cargaron correctamente (tamaño).");
                if (($file_size / 1024 / 1024) > $file_max_size)
                  throw new Exception("El archivo seleccionado supera el tama&ntilde;o permitido de ".$file_max_size."MB. Seleccione una imagen o documento m&aacute;s liviano.");
              } elseif ($tipo_id == EDT_SEL_MUL) {
                if (!isset($valor) || count($valor) <= 0) 
                  throw new Exception("Hay puntos requeridos que no se completaron (1 - ".$det_id.")");
              } else {
                if (!isset($valor) || trim($valor) == "") 
                  throw new Exception("Hay puntos requeridos que no se completaron (2 - ".$det_id.$llave.$valor.")");
              }
            }
          }
        }
      }
      
      if (!$hay_preguntas) throw new Exception("Error cargando las respuestas");
      // ----------------------- BLOQUE DE VALIDACIONES ----------------------- //
      
      
      // creo el encabezado de la respuesta de la encuesta
      $res = Respuesta::insertar($conn, 
                                 $encuesta_id, $nombre, $cedula, $sucursal_id, $unidad_id, 
                                 $agente_id, $gestion_id, $fecha, $fecha_nac, $estado_emp_id, 
                                 $estado_otro, $genero_id, $genero_otro, $puesto_id, $puesto_otro, 
                                 $provincia_id, $canton_id, $distrito_id, $correo, $telefono, 
                                 $est_civil_id);
      
      $mens = $res["mensaje"];
      if ($mens != "") throw new Exception("Error creando la respuesta. " . $mens);
      
      $respuesta_id = $res["datos"]["respuesta_id"];
      // creo el encabezado de la respuesta de la encuesta 
      
      
      // creo los registros para cada uno de los puntos de la encuesta
      foreach ($obj_post as $llave => $valor) {
        $partes = explode("_", $llave);
        $count_partes = count($partes);
        
        if ($count_partes > 0 && $count_partes < 3) { // no tomo en cuenta los keys de otros en este ciclo
          if ($partes[0] == "det") {
            $det_id = $partes[1];
            $texto_otro = null;
            $texto_exp = null;
            
            
            // cargo el objeto de cada enunciado de la encuesta
            $enc_det = EncuestaDet::getEncuestaDet($conn, $det_id);

            $mens = $enc_det["mensaje"];
            if ($mens != "") throw new Exception("Error cargando el detalle de la encuesta. " . $mens);

            $encuesta_det = $enc_det["encuesta_det"];
            $tipo_id = $encuesta_det["tipo_id"];
            $otro_id = $encuesta_det["otro_id"] ?? 0;
            // cargo el objeto de cada enunciado de la encuesta
            
            
            // en caso de ser seleccion multiple o unica, leo el campo de otro
            if ($otro_id > 0 && ($tipo_id == EDT_SEL_UNI_COM || $tipo_id == EDT_SEL_UNI_RAD)) {
              $llave_otro = Funciones::name_otro($llave);
              
              $texto_otro_temp = $obj_post[$llave_otro] ?? "";
              
              if (trim($texto_otro_temp) != "") $texto_otro = $texto_otro_temp;
            }
            // en caso de ser seleccion multiple o unica, leo el campo de otro
            
            
            // en caso de ser booleano, leo el campo de explicacion de la pregunta
            if ($tipo_id == EDT_BOOLEANO) {
              $llave_exp = Funciones::name_exp($llave);
              
              $texto_exp_temp = $obj_post[$llave_exp] ?? "";
              
              if (trim($texto_exp_temp) != "") $texto_exp = $texto_exp_temp;
            }
            // en caso de ser booleano, leo el campo de explicacion de la pregunta
            
            
            // si el tipo es seleccion multiple el valor en la tabla respuesta_det va a ser nulo
            $val = ($tipo_id == EDT_SEL_MUL) ? null : filter_var(trim($valor));
            // si el tipo es seleccion multiple el valor en la tabla respuesta_det va a ser nulo


            // en caso de ser un archivo, procedo a subirlo antes para sacar la ruta de la imagen
						if ($tipo_id == EDT_ARCHIVO) {
							$llave_file = Funciones::name_file($llave);
							$file = $_FILES[$llave_file];

							if (isset($file)) {
								$file_size = $file["size"];
                $file_max_size = $encuesta_det["meta"][META_FILE_SIZE] ?? 5;

                if (($file_size / 1024 / 1024) > $file_max_size)
                  throw new Exception("El archivo seleccionado supera el tama&ntilde;o permitido de ".$file_max_size."MB. Seleccione una imagen o documento m&aacute;s liviano.");

								if ($file_size > 0) {
									$file_type = $file["type"];
									$file_tmp_name = $file["tmp_name"];

									$file_name = $file["name"];

									$val = subir_archivo_citrix($file_type, $file_tmp_name, $file_size, $file_name, $enc["folder_id"], 
																							$respuesta_id, $det_id);

									if ($val == "") throw new Exception("Hubo un error subiendo uno de los documentos (" . $llave_file . ").");
								}
							}
						}
            // en caso de ser un archivo, procedo a subirlo antes para sacar la ruta de la imagen


            // en caso de ser una seleccion unica cargo los correos de la opcion seleccionada
            if ($tipo_id == EDT_SEL_UNI_COM || $tipo_id == EDT_SEL_UNI_RAD) {
              $enc_det_opc = EncuestaDetOpciones::getEncuestaDetOpciones($conn, $det_id, $dicc = true);

              $mens = $enc_det_opc["mensaje"];
              if ($mens != "") throw new Exception("Error cargando la opciones de la pregunta. " . $mens);

              $encuesta_det_opciones = $enc_det_opc["encuesta_det_opciones"];
              
              $opcion_sel = $encuesta_det_opciones[$val];
              if (isset($opcion_sel["correos"]) && !empty($opcion_sel["correos"])) $correos .= ($correos == "" ? "" : "; ") . trim($opcion_sel["correos"]);
            }
            // en caso de ser una seleccion unica cargo los correos de la opcion seleccionada


            // inserto el registro de cada enunciado como respuesta. Sel multiple -> valor=null
						if ($tipo_id != EDT_ARCHIVO || ($tipo_id == EDT_ARCHIVO && $file_size > 0)) {
							$res_det = RespuestaDet::insertar($conn, $respuesta_id, $det_id, $tipo_id, $val, $texto_otro, 
																								$texto_exp);

							$mens = $res_det["mensaje"];
							if ($mens != "") throw new Exception("Error creando los items de la respuesta. " . $mens);

							$respuesta_det_id = $res_det["datos"]["respuesta_det_id"];
						}
            // inserto el registro de cada enunciado como respuesta 


            // en caso de ser seleccion multiple, la variable valor va a ser un arreglo
            if ($tipo_id == EDT_SEL_MUL) {
              // cargo las opciones del item de seleccion multiple
              $enc_det_opc = EncuestaDetOpciones::getEncuestaDetOpciones($conn, $det_id, $dicc = true);

              $mens = $enc_det_opc["mensaje"];
              if ($mens != "") throw new Exception("Error cargando la opciones de la pregunta. " . $mens);

              $encuesta_det_opciones = $enc_det_opc["encuesta_det_opciones"];
              // cargo las opciones del item de seleccion multiple
              
              
              foreach ($valor as $val) {
                $texto_otro = null;
                
                if ($encuesta_det_opciones[$val]["otro"] == 1) {
                  $texto_otro_temp = $obj_post[$llave . "_otro"] ?? "";
              
                  if (trim($texto_otro_temp) != "") $texto_otro = $texto_otro_temp;
                }

                $res_det = RespuestaDetOpcion::insertar($conn, $respuesta_det_id, $val, $texto_otro);

                $mens = $res_det["mensaje"];
                if ($mens != "") throw new Exception("Error creando las opciones de los items de la respuesta. " . $mens);

                $respuesta_det_opcion_id = $res_det["datos"]["respuesta_det_opcion_id"];
              }
            }
            // en caso de ser seleccion multiple, la variable valor va a ser un arreglo 
          }
        }
      }
      // creo los registros para cada uno de los puntos de la encuesta
      
      
      sqlsrv_commit($conn);
      
      
      if (!$_SESSION["dev"]) Procesos::enviar_correos($conn, $enc, $correos, $cedula, $nombre, 
                                                      $respuesta_id);
    } catch (Exception $e) {
      sqlsrv_rollback($conn);
      $ret["mensaje"] = $e->getMessage();
    }
    
    return $ret;
  }
  
  public static function enviar_correos($conn, $encuesta, $correos, $cedula_usuario, $nombre_usuario, 
                                        $respuesta_id) {
    if ($correos != "") {
      $encuesta_nom = $encuesta["nombre"];
      $encuesta_url = "https://app.powerbi.com/groups/me/reports/56eb49c6-8aa6-44a6-ac66-a8e78da45eb4/696f7283108d18abb2e7?ctid=263ebb7b-92db-4a61-8cb1-25ecefb1e05c&experience=power-bi";
      
      $partes_correos = explode(";", $correos);
      
			$texto_ced = (($cedula_usuario ?? "") == "") ? "" : (" - Cédula: " . $cedula_usuario);
      $titulo = "Nueva respuesta del formulario " . $encuesta_nom . $texto_ced;
      $mensaje = "<p>";
      
      if ($nombre_usuario != "") 
        $mensaje .= $nombre_usuario . $texto_ced . " envió una nueva respuesta del formulario " . $encuesta_nom . ". ";
      else 
        $mensaje .= "Se recibió una nueva respuesta del formulario " . $encuesta_nom . ". ";
      
      $mensaje .= "</p><p>Puede descargar las respuestas <a href='" . $encuesta_url . "'>aquí</a>.</p><br><br>";

      // if ($encuesta["encuesta_id"] == ENCUESTA_PRUEBA) Procesos::generar_tabla_html($conn, $respuesta_id, $mensaje);

      Respuesta::actualizarCorreos($conn, $respuesta_id, $correos);
      
      foreach ($partes_correos as $correo) {
        $correo = trim($correo);
        
        if ($correo != "") {
          $msg_mail = Correo::enviarCorreo($correo, $titulo, $mensaje);

          Respuesta::insertarRespuestaEnvioCorreo($conn, $respuesta_id, $correo, $msg_mail);
        }
      }
    }
  }

  public static function generar_tabla_html($conn, $respuesta_id, $texto_html) {
    $ret = $texto_html;

    $opciones_enc = [];
    $opciones_enc[] = array("cod" => "nombre",       "nom" => "Nombre");
    $opciones_enc[] = array("cod" => "cedula",       "nom" => "Cédula"); 
    $opciones_enc[] = array("cod" => "correo",       "nom" => "Correo electrónico"); 
    $opciones_enc[] = array("cod" => "telefono",     "nom" => "Teléfono"); 
    $opciones_enc[] = array("cod" => "fecha",        "nom" => "Fecha envío"); 
    $opciones_enc[] = array("cod" => "fecha_nac",    "nom" => "Fecha nacimiento"); 
    $opciones_enc[] = array("cod" => "sucursal",     "nom" => "Sucursal"); 
    $opciones_enc[] = array("cod" => "unidad",       "nom" => "Unidad"); 
    $opciones_enc[] = array("cod" => "estado_emp",   "nom" => "Estado"); 
    $opciones_enc[] = array("cod" => "genero",       "nom" => "Género"); 
    $opciones_enc[] = array("cod" => "puesto",       "nom" => "Puesto"); 
    $opciones_enc[] = array("cod" => "provincia",    "nom" => "Provincia"); 
    $opciones_enc[] = array("cod" => "canton",       "nom" => "Cantón"); 
    $opciones_enc[] = array("cod" => "distrito",     "nom" => "Distrito"); 
    $opciones_enc[] = array("cod" => "agente",       "nom" => "Agente"); 
    $opciones_enc[] = array("cod" => "gestion",      "nom" => "Gestión"); 
    $opciones_enc[] = array("cod" => "estado_civil", "nom" => "Estado civil");

    $respuesta = Respuesta::getRespuesta($conn, $respuesta_id);

    $ret .= "
    <table>
      <thead class='thead-dark'>
        <tr>
          <th>Enunciado</th>
          <th>Valor</th>
        </tr>
      </thead>
      <tbody>";

    foreach ($opciones_enc as $opcion) {
      $cod = $opcion["cod"];
      $nom = $opcion["nom"];

      // if (!empty($respuesta[])) {
      //   $ret .= "
      //   <tr>
      //     <th>$cod</th>
      //     <th>$nom</th>
      //   </tr>";
      // }
    }

    foreach ($respuesta["detalles"] as $detalle) {
      $ret .= "
        <tr>
          <th>" . $detalle["enunciado"] . "</th>";
      if ($detalle["tipo_id"] != EDT_SEL_MUL) {
        $ret .= "
          <th>" . $detalle["valor"] . "</th>";
      } else {
        $texto_opc = "";
        foreach ($detalle["opciones"] as $opcion) {
          $texto_opc .= (empty($texto_opc) ? "" : " - ") . $opcion["texto"];
        }
        $ret .= "
          <th>$texto_opc</th>";
      }
      echo "
        </tr>";
    }

    $ret .= "
      </tbody>
    </table>";

    return $ret;
  }
  
}

?>
