<?php
include_once "header.php";

$texto_dependencias_js = "";

$encuesta_id = isset($_GET['id']) ? $_GET['id'] : 0;
$enc = Query::dataEncuesta($conn, $encuesta_id);  
$encuesta = $enc["encuesta"];
$encuesta_msg_bloqueo = $encuesta["mensaje_bloqueo"] ?? "";
$encuesta_activa = ($encuesta["activo"] ?? 0) == 1;
$mostrar_mensaje_fechas = $encuesta["mostrar_mensaje_fechas"] == 1;

$var_app = "";

$agente_id = isset($_GET['age_id']) ? $_GET['age_id'] : 0;
$agente_url = ($agente_id == 0) ? "" : ("&age_id=" . $agente_id);

$bloque_mensaje = ""; // esta variable va a manejar todo el html para la cajita de mensaje de exito / error

$token_form = md5(uniqid(rand(), true));
$_SESSION["form_token"] = $token_form;

// cargo los distintos componentes del encabezado
if (isset($encuesta) && $encuesta_activa && empty($encuesta_msg_bloqueo) && !$mostrar_mensaje_fechas) {
	echo "
		<main role='main' class='container text-gris-oscuro'>
      <div class='row px-2 py-5'>".
        $bloque_mensaje . "
        <div class='col-12 text-center'>";
	if (isset($_GET['app']) && $_GET['app'] != "1") {
		$var_app = "&app" . ($_GET['app'] == "" ? "" : ("=" . $_GET['app']));
		
		echo "
          <img src='assets/img/logo.png' class='img-logo'/>
          <h3 class='text-gris my-4'>" . $encuesta["nombre"] . "</h3>";
	}
	echo "
          <p>" . $encuesta["descripcion"] . "</p>
        </div>
        <div class='col-12'>
          <form id='formulario' action='envio.php?id=" . $encuesta_id . $agente_url . $var_app . "&accion=crear' method='post' enctype='multipart/form-data'>
            <div class='row'>";
						
  $uni_req_tex = $encuesta["unidad_oblig"] == 1 ? "" : " (opcional)";
  $age_req_tex = $encuesta["agente_oblig"] == 1 ? "" : " (opcional)";
  $ges_req_tex = $encuesta["gestion_oblig"] == 1 ? "" : " (opcional)";
  
  $agente = Agente::getAgente($conn, $agente_id)["agente"];
  $agente_id = isset($agente) ? $agente_id : 0;
  $sucursal_id = isset($agente) ? ($agente["sucursal_id"] ?? 0) : 0;
  $unidad_id = isset($agente) ? ($agente["unidad_id"] ?? 0) : 0;
  
  QueryEncuesta::cargarCedula($conn, $encuesta);
  QueryEncuesta::cargarNombre($conn, $encuesta);
  QueryEncuesta::cargarCorreo($conn, $encuesta);
  QueryEncuesta::cargarTelefono($conn, $encuesta);
  QueryEncuesta::cargarSucursalUnidadAgente($conn, $encuesta, $sucursal_id, $unidad_id, $agente_id);
  QueryEncuesta::cargarGestion($conn, $encuesta, $unidad_id);
  QueryEncuesta::cargarFechaNac($conn, $encuesta);
  QueryEncuesta::cargarEstadosEmp($conn, $encuesta);
  QueryEncuesta::cargarGeneros($conn, $encuesta);
  QueryEncuesta::cargarPuestos($conn, $encuesta);
  QueryEncuesta::cargarUbicacion($conn, $encuesta);
  QueryEncuesta::cargarEstCivil($conn, $encuesta);

  echo "
            </div>";
// cargo los distintos componentes del encabezado


  $arr_sel_mul_req = array();
  $num_pregunta = 1;
  foreach ($encuesta["detalles"] as $detalle) {
    $contenido_qed = "";
    
    $objQueryED = new QueryEncuestaDet($detalle, $encuesta);
    $objQueryED->setNumPregunta($num_pregunta);
    
    // $cl_bloq_tipo = $objQueryED->getClaseBloqTipo();
    
    echo "
            <div id='bloque-" . $objQueryED->det_id . "' det_id='" . $objQueryED->det_id . "' 
                 class='row my-2 " . $objQueryED->claseDisplayNoneDependencias() . "' " . $objQueryED->atributosDependencias() . ">";
    
    switch ($objQueryED->det_tipo_id) {
      case EDT_NUMERO: 
        $contenido_qed = $objQueryED->cargaNumero();               break;
      case EDT_MONTO: 
        $contenido_qed = $objQueryED->cargaMonto();                break;
      case EDT_CORREO: 
        $contenido_qed = $objQueryED->cargaCorreo();               break;
      case EDT_FECHA: 
        $contenido_qed = $objQueryED->cargaFecha();                break;
      case EDT_TEXTO_C: 
        $contenido_qed = $objQueryED->cargaTextoCorto();           break;
      case EDT_TEXTO_L: 
        $contenido_qed = $objQueryED->cargaTextoLargo();           break;
      case EDT_SEL_UNI_COM: 
        $contenido_qed = $objQueryED->cargaSeleccionUnicaCombo();  break;
      case EDT_SEL_UNI_RAD: 
        $contenido_qed = $objQueryED->cargaSeleccionUnicaRadio();  break;
      case EDT_SEL_MUL: 
        $contenido_qed = $objQueryED->cargaSeleccionMultiple();    break;
      case EDT_RATING_PTO: 
        $contenido_qed = $objQueryED->cargaRatingPuntos();         break;
      case EDT_RATING_BAR: 
        $contenido_qed = $objQueryED->cargaRatingBarra();          break;
      case EDT_ARCHIVO: 
        $contenido_qed = $objQueryED->cargaArchivo();              break;
      case EDT_LOCATION:
        $contenido_qed = "";                                       break;
      case EDT_TEXTO_SEP: 
        $contenido_qed = $objQueryED->cargaTextoSeparador();       break;
      case EDT_BOOLEANO: 
        $contenido_qed = $objQueryED->cargaBooleano();             break;
      case EDT_SUBTITULO: 
        $contenido_qed = $objQueryED->cargaSubtitulo();            break;
      case EDT_PARRAFO: 
        $contenido_qed = $objQueryED->cargaParrafo();              break;
      case EDT_PROVINCIA: 
        $contenido_qed = $objQueryED->cargaProvincia($conn);       break;
      case EDT_RATING_CUA: 
        $contenido_qed = $objQueryED->cargaRatingCuadros();        break;
      case EDT_DIRECCION_ENV: 
        $contenido_qed = $objQueryED->cargaDireccionEnvio($conn);  break;
      case EDT_LINK_EXT: 
        $contenido_qed = $objQueryED->cargaLinkExterno();          break;
      case EDT_PUESTO_CAJA: 
        $contenido_qed = $objQueryED->cargaPuestosCaja($conn);     break;
      default:                                                     break;
    }
    
    if ($objQueryED->dependeDeAlgo()) {
      $texto_dependencias_js .= ($texto_dependencias_js == "") ? " var dependencias_js = new Object();" : "";
      
      $texto_dependencias_js .= "
      dependencias_js[" . $objQueryED->det_id . "] = `" . trim($contenido_qed) . "`;";
    } else {
      echo $contenido_qed;
    }

    $num_pregunta = $objQueryED->getNuevoNumPregunta();
    
    echo "
            </div>";
  }
  
  if ($texto_dependencias_js != "") {
    $texto_dependencias_js .= "
    ";
  }
  
  echo "
						<input type='hidden' name='form_token' value='" . $token_form . "'>
            <button type='submit' id='btnSend' class='btn bg-gris-oscuro text-white px-5 pt-2 mt-2' onclick='intentarSubmit()'>Enviar</button>
						<label id='lblProcessing' class='mx-3 d-none'>Por favor espere unos segundos mientras se envía la información...</label>
          </form>
        </div>
      </div>
    </main>
    
    <script>
      " . $texto_dependencias_js . "
			function intentarSubmit() {
				let formulario = document.getElementById('formulario');
				let lblProcessing = document.getElementById('lblProcessing');
				let btnSend = document.getElementById('btnSend');
				
				if (formulario.reportValidity()) {
					lblProcessing.classList.remove('d-none');
					btnSend.setAttribute('disabled', 'disabled');
				
					formulario.submit();
				} else {
					alert('Por favor asegúrese de llenar todos los campos, de ser un(a) accionista habilitado(a) en Caja de ANDE y que sus datos estén actualizados.');
				}
			}
			
      function revisarCedula(cedula) {
        if (cedula.length <= 0) {
          document.getElementById('nombre').value = '';
        } else {
          fetch('ajax.php?accion=leerSocio&cedula='+cedula, {cache: 'no-cache'})
          .then(function(response) {
            if (response.status != 200)
              console.log('Ocurrió un error con el servicio: ' + response.status);
            else
              return response.json();
          })
          .then (function(json) {
            if (json.nombre != '') {
							let objNombre = document.getElementById('nombre');
							let objCorreo = document.getElementById('correo');
							
							if (objNombre) objNombre.value = json.nombre;
							if (objCorreo) objCorreo.value = json.correo;
						}
          })
          .catch(function(err) {
            console.log('Ocurrió un error con la ejecución', err);
          });
        }
      }
      
      function cambiarSucursal(select) {
        sucursal_id = select.options[select.selectedIndex].value;
        
        if (document.getElementById('agente')) {
          document.getElementById('agente').innerHTML = '<option disabled selected hidden value=\'\'>-- Agente --</option>';
        }
        
        if (document.getElementById('gestion')) {
          document.getElementById('gestion').innerHTML = '<option disabled selected hidden value=\'\'>-- Gestión --</option>';
        }
        
        fetch('ajax.php?accion=cambiarSucursal&sucursal='+sucursal_id)
        .then(function(result) {
          return result.text();
        })
        .then (function(data) {
          document.getElementById('unidad').innerHTML = '<option disabled selected hidden value=\'\'>-- Unidad --" . $uni_req_tex . "</option>'+data;
        })
        .catch(function(err) {
          console.log('Ocurrió un error con la ejecución', err);
        });
      }
      
      function cambiarUnidad(select) {
        unidad_id = select.options[select.selectedIndex].value;
        sucursal_id = document.getElementById('sucursal').value;
        
        if (document.getElementById('agente')) {
          fetch('ajax.php?accion=cambiarUnidadAgente&sucursal='+sucursal_id+'&unidad='+unidad_id)
          .then(function(result) {
            return result.text();
          })
          .then (function(data) {
            document.getElementById('agente').innerHTML = '<option disabled selected hidden value=\'\'>-- Agente --" . $age_req_tex . "</option>'+data;
          })
          .catch(function(err) {
            console.log('Ocurrió un error con la ejecución', err);
          });
        }
        
        if (document.getElementById('gestion')) {
          fetch('ajax.php?accion=cambiarUnidadGestion&unidad='+unidad_id)
          .then(function(result) {
            return result.text();
          })
          .then (function(data) {
            document.getElementById('gestion').innerHTML = '<option disabled selected hidden value=\'\'>-- Gestión --" . $ges_req_tex . "</option>'+data;
          })
          .catch(function(err) {
            console.log('Ocurrió un error con la ejecución', err);
          });
        } 
      }
      
      function cambiarProvincia(select) {
        provincia_id = select.options[select.selectedIndex].value;
        
        document.getElementById('distrito').innerHTML = '<option disabled selected hidden value=\'\'>-- Distrito --</option>';
        
        fetch('ajax.php?accion=cambiarProvincia&provincia='+provincia_id)
        .then(function(result) {
          return result.text();
        })
        .then (function(data) {
          document.getElementById('canton').innerHTML = '<option disabled selected hidden value=\'\'>-- Cantón --</option>'+data;
        })
        .catch(function(err) {
          console.log('Ocurrió un error con la ejecución', err);
        });
      }
      
      function cambiarCanton(select) {
        canton_id = select.options[select.selectedIndex].value;
        
        fetch('ajax.php?accion=cambiarCanton&canton='+canton_id)
        .then(function(result) {
          return result.text();
        })
        .then (function(data) {
          document.getElementById('distrito').innerHTML = '<option disabled selected hidden value=\'\'>-- Distrito --</option>'+data;
        })
        .catch(function(err) {
          console.log('Ocurrió un error con la ejecución', err);
        });
      }
      
      function mostrarOtroSelUni(padre_id, opcion_id, opcion, capa_text_otro, text_otro) {
        var partes = opcion.split('_');
        var capa_otro = document.getElementById(capa_text_otro);
        var input_otro = document.getElementById(text_otro);
        
        input_otro.value = '';
        capa_otro.style.display = (partes[1] == 'ot') ? 'inline-block' : 'none';
        
        mostrarOtrasPreguntas(padre_id, opcion_id);
      }
      
      function mostrarOtrasPreguntas(padre_seleccionado_id, opcion_seleccionada_id) {
        const capas_dep = document.querySelectorAll('[dep_alguien=\'1\']');
        
        capas_dep.forEach((capa_dep) => {
          if (capa_dep.getAttribute('dep_padre') == padre_seleccionado_id) { // si la capa depende de la pregunta...
            const dep_hijos_id_arr = capa_dep.getAttribute('dep_hijos').split(',');
            const det_id = capa_dep.getAttribute('det_id');
            var mostrar_capa = false;
            dep_hijos_id_arr.forEach(temp_hijo => {
              if (temp_hijo == opcion_seleccionada_id) mostrar_capa = true;
            });
            console.log(dep_hijos_id_arr);
            if (mostrar_capa) { // si la capa depende de la opcion seleccionada o no...
              capa_dep.classList.remove('d-none');
              capa_dep.innerHTML = dependencias_js[det_id];
            } else {
              capa_dep.classList.add('d-none');
              capa_dep.innerHTML = '';
            }
          }
          //if (capas_dep.attributes)
        });
        
        //console.log('DEBUG ' + padre_seleccionado_id +  ' - ' + opcion_seleccionada_id);
      }
      
      function validarSelMul(elClass, opcion, capa_text_otro, text_otro, bloqueaOtras) {
        el = document.getElementsByClassName(elClass);

        var atLeastOneChecked = false; 
        for (i = 0; i < el.length; i++) {
          if (el[i].checked === true) atLeastOneChecked = true;
        }

        for (i = 0; i < el.length; i++) {
          el[i].required = !(atLeastOneChecked === true);
        }
        
        if (bloqueaOtras == 1) {
          var bloqueaOtrasChecked = document.getElementById(opcion).checked === true;
          
          for (i = 0; i < el.length; i++) {
            if (!bloqueaOtrasChecked)
              el[i].disabled = false; // desbloqueo todas las opciones
            else {
              el[i].disabled = opcion != el[i].id;  // bloqueo las opciones
              if (opcion != el[i].id) el[i].checked = false;
            }
          }
          
          limpiarInputOtro(text_otro);
          mostrarCapaOtro(capa_text_otro, 'none');
        }
      }
      
      function mostrarOtroSelMul(opcion, capa_text_otro, text_otro, bloqueaOtras) {
        limpiarInputOtro(text_otro);
        
        var seleccionado = document.getElementById(opcion).checked;
        mostrarCapaOtro(capa_text_otro, seleccionado ? 'inline-block' : 'none');
      }
      
      function validarSelMulMostrarOtro(elClass, opcion, capa_text_otro, text_otro, bloqueaOtras) {
        validarSelMul(elClass, opcion, capa_text_otro, text_otro, bloqueaOtras);
        
        mostrarOtroSelMul(opcion, capa_text_otro, text_otro, bloqueaOtras);
      }
      
      function limpiarInputOtro(text_otro) {
        var input_otro = document.getElementById(text_otro);
        input_otro.value = '';
      }
      
      function mostrarCapaOtro(capa_text_otro, clase) {
        var capa_otro = document.getElementById(capa_text_otro);
        capa_otro.style.display = clase;
      }
      
      function mostrarExpBool(capa_text_exp, text_exp, val_bool) {
        var capa_exp = document.getElementById(capa_text_exp);
        var input_exp = document.getElementById(text_exp);
        
        input_exp.value = '';
        capa_exp.style.display = (val_bool == 1) ? 'inline-block' : 'none';
      }
    </script>";
} else {
	$msg_html = "";
	if (!isset($encuesta)) $msg_html = "Se presentó un error al cargar el formulario.";
	if (!$encuesta_activa && empty($msg_html)) $msg_html = "El formulario se encuentra inactivo."; 
	if ($mostrar_mensaje_fechas) $msg_html = "El formulario se activará el " . $encuesta["fecha_inicio_format"] . "."; 
	if (!empty($encuesta_msg_bloqueo) && empty($msg_html)) $msg_html = $encuesta_msg_bloqueo; 
	echo "
		<main role='main' class='container text-gris-oscuro'>
      <div class='row px-2 py-5'>
				<h3 class='text-center mt-3'>" . $msg_html . "</h3>
			</div>
		</main>";
}
include_once "footer.php";
?>