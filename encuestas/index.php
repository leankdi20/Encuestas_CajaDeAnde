<?php
include_once "header.php";

$texto_dependencias_js = "";

$encuesta_id = isset($_GET['id']) ? $_GET['id'] : 0;
$enc = Query::dataEncuesta($conn, $encuesta_id);
$encuesta = $enc["encuesta"];
if ($enc["mensaje"] != "") die($enc["mensaje"]);
$encuesta_msg_bloqueo = $encuesta["mensaje_bloqueo"] ?? "";
$encuesta_activa = ($encuesta["activo"] ?? 0) == 1;
$mostrar_mensaje_fechas = ($encuesta["mostrar_mensaje_fechas"] ?? 0) == 1;

$var_app = "";

$agente_id = isset($_GET['age_id']) ? $_GET['age_id'] : 0;
$agente_url = ($agente_id == 0) ? "" : ("&age_id=" . $agente_id);

$bloque_mensaje = "";

$token_form = md5(uniqid(rand(), true));
$_SESSION["form_token"] = $token_form;

if (isset($encuesta) && $encuesta_activa && empty($encuesta_msg_bloqueo) && !$mostrar_mensaje_fechas) {
  echo "
    <main role='main' class='survey-page'>
      <div class='survey-shell container py-4 py-md-5'>".
        $bloque_mensaje . "
        <section class='survey-card'>
          <div class='survey-hero'>";
  if (isset($_GET['app']) && $_GET['app'] != "1") {
    $var_app = "&app" . ($_GET['app'] == "" ? "" : ("=" . $_GET['app']));
  }
  echo "
            <div class='survey-brand'>
              <img src='https://storageproyectosmercadeo.blob.core.windows.net/masaccesosbeneficios/banners/Logocajadeande.png' class='img-logo' alt='Caja de ANDE'/>
              <span class='survey-badge'>Gestión Caja de ANDE</span>
            </div>
            <h1 class='survey-title'>" . $encuesta["nombre"] . "</h1>
            <p class='survey-description'>" . $encuesta["descripcion"] . "</p>
          </div>
          <div class='survey-body'>
            <div class='survey-stepper' id='surveyStepper'></div>
            <form id='formulario' action='envio.php?id=" . $encuesta_id . $agente_url . $var_app . "&accion=crear' method='post' enctype='multipart/form-data'>
              <div class='survey-intake row'>";

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

  $num_pregunta = 1;
  foreach ($encuesta["detalles"] as $detalle) {
    $contenido_qed = "";

    $objQueryED = new QueryEncuestaDet($detalle, $encuesta);
    $objQueryED->setNumPregunta($num_pregunta);

    echo "
              <div id='bloque-" . $objQueryED->det_id . "' det_id='" . $objQueryED->det_id . "'
                   class='survey-question row my-2 " . $objQueryED->claseDisplayNoneDependencias() . "' " . $objQueryED->atributosDependencias() . ">";

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
              <div class='survey-actions' id='surveyActions'>
                <button type='button' id='btnPrevStep' class='btn btn-outline-brand d-none'>Anterior</button>
                <button type='button' id='btnNextStep' class='btn btn-brand-secondary d-none'>Siguiente</button>
                <button type='button' id='btnSend' class='btn btn-brand px-5 pt-2 mt-2' onclick='intentarSubmit()'>Enviar</button>
                <label id='lblProcessing' class='survey-processing mx-3 d-none'>Por favor espere unos segundos mientras se env&iacute;a la informaci&oacute;n...</label>
              </div>
            </form>
          </div>
        </section>
      </div>
    </main>

    <script>
      " . $texto_dependencias_js . "
      function validarArchivo(input) {
        var maxSizeMb = parseFloat(input.getAttribute('data-max-size-mb') || '0');
        var file = input.files && input.files.length > 0 ? input.files[0] : null;

        input.setCustomValidity('');

        if (!file || !maxSizeMb) return true;

        if (file.size > maxSizeMb * 1024 * 1024) {
          var mensaje = 'El archivo seleccionado supera el tama\u00f1o permitido de ' + maxSizeMb + 'MB. Seleccione una imagen o documento m\u00e1s liviano.';
          input.value = '';
          input.setCustomValidity(mensaje);
          input.reportValidity();
          if (!input.required) input.setCustomValidity('');
          return false;
        }

        return true;
      }

      function validarArchivosFormulario(formulario) {
        var archivos = Array.from(formulario.querySelectorAll('input[type=\"file\"]')).filter(function(input) {
          return !input.disabled && input.offsetParent !== null;
        });

        return archivos.every(function(input) {
          return validarArchivo(input);
        });
      }

      function intentarSubmit() {
        let formulario = document.getElementById('formulario');
        let lblProcessing = document.getElementById('lblProcessing');
        let btnSend = document.getElementById('btnSend');

        if (!validarArchivosFormulario(formulario)) return;

        if (formulario.reportValidity()) {
          lblProcessing.classList.remove('d-none');
          btnSend.setAttribute('disabled', 'disabled');
          formulario.submit();
        } else {
          alert('Por favor aseg\u00farese de completar los campos requeridos, de ser un(a) accionista habilitado(a) en Caja de ANDE y de que sus datos est\u00e9n actualizados.');
        }
      }

      function inicializarEtapasEncuesta() {
        const formulario = document.getElementById('formulario');
        const stepper = document.getElementById('surveyStepper');
        const intake = formulario ? formulario.querySelector('.survey-intake') : null;
        const questions = formulario ? Array.from(formulario.querySelectorAll('.survey-question')) : [];
        const actions = document.getElementById('surveyActions');
        const btnPrev = document.getElementById('btnPrevStep');
        const btnNext = document.getElementById('btnNextStep');
        const btnSend = document.getElementById('btnSend');

        if (!formulario || !stepper || !intake || questions.length === 0) return;

        const steps = [];
        const createStep = function(title, description) {
          const panel = document.createElement('section');
          panel.className = 'survey-step d-none';

          const header = document.createElement('div');
          header.className = 'survey-step__header';

          const eyebrow = document.createElement('span');
          eyebrow.className = 'survey-step__eyebrow';
          eyebrow.textContent = 'Etapa';

          const heading = document.createElement('h2');
          heading.className = 'survey-step__title';
          heading.textContent = title;

          header.appendChild(eyebrow);
          header.appendChild(heading);

          if (description) {
            const copy = document.createElement('p');
            copy.className = 'survey-step__description';
            copy.textContent = description;
            header.appendChild(copy);
          }

          panel.appendChild(header);
          formulario.insertBefore(panel, actions);
          return { title, panel };
        };

        const introStep = createStep('Informaci\u00f3n inicial', 'Complete sus datos para personalizar la experiencia antes de continuar.');
        introStep.panel.appendChild(intake);
        steps.push(introStep);

        let activeBucket = null;
        let fallbackIndex = 1;

        questions.forEach(function(question) {
          const heading = question.querySelector('h5, h6');

          if (heading) {
            activeBucket = createStep(heading.textContent.trim(), 'Revise este bloque y contin\u00fae cuando termine.');
            steps.push(activeBucket);
          }

          if (!activeBucket) {
            activeBucket = createStep('Bloque ' + fallbackIndex, 'Contin\u00fae con las siguientes preguntas.');
            steps.push(activeBucket);
            fallbackIndex += 1;
          }

          activeBucket.panel.appendChild(question);
        });

        if (steps.length <= 1) return;

        btnPrev.classList.remove('d-none');
        btnNext.classList.remove('d-none');

        const progress = document.createElement('div');
        progress.className = 'survey-stepper__track';
        stepper.appendChild(progress);

        let activeIndex = 0;

        const markers = steps.map(function(step, index) {
          const marker = document.createElement('button');
          marker.type = 'button';
          marker.className = 'survey-stepper__item';
          marker.innerHTML = '<span class=\"survey-stepper__index\">' + (index + 1) + '</span><span class=\"survey-stepper__label\">' + step.title + '</span>';
          marker.addEventListener('click', function() {
            goToStep(index);
          });
          progress.appendChild(marker);
          return marker;
        });

        function goToStep(index) {
          activeIndex = index;

          steps.forEach(function(step, stepIndex) {
            step.panel.classList.toggle('d-none', stepIndex !== activeIndex);
          });

          markers.forEach(function(marker, markerIndex) {
            marker.classList.toggle('is-active', markerIndex === activeIndex);
            marker.classList.toggle('is-complete', markerIndex < activeIndex);
          });

          btnPrev.disabled = activeIndex === 0;
          btnNext.classList.toggle('d-none', activeIndex === steps.length - 1);
          btnSend.classList.toggle('d-none', activeIndex !== steps.length - 1);

          window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        btnPrev.addEventListener('click', function() {
          if (activeIndex > 0) goToStep(activeIndex - 1);
        });

        btnNext.addEventListener('click', function() {
          const currentPanel = steps[activeIndex].panel;
          const currentFields = Array.from(currentPanel.querySelectorAll('input, select, textarea')).filter(function(field) {
            return field.type !== 'hidden' && !field.disabled && field.offsetParent !== null;
          });

          const invalidField = currentFields.find(function(field) {
            if (field.type === 'file' && !validarArchivo(field)) return true;
            return !field.checkValidity();
          });

          if (invalidField) {
            invalidField.reportValidity();
            invalidField.focus();
            return;
          }

          if (activeIndex < steps.length - 1) goToStep(activeIndex + 1);
        });

        goToStep(0);
      }

      let cedulaLookupTimer = null;

      function limpiarCedula(cedula) {
        return (cedula || '').replace(/[^0-9]/g, '');
      }

      function setCedulaFeedback(message, type) {
        var feedback = document.getElementById('cedulaFeedback');
        if (!feedback) return;

        feedback.className = 'survey-feedback survey-feedback--' + type;
        feedback.textContent = message;
      }

      function setNombreFeedback(message, type) {
        var feedback = document.getElementById('nombreFeedback');
        if (!feedback) return;

        feedback.className = 'survey-feedback survey-feedback--' + type;
        feedback.textContent = message;
      }

      function habilitarEdicionManualNombre(permitirEdicion) {
        var objNombre = document.getElementById('nombre');
        if (!objNombre) return;

        objNombre.readOnly = !permitirEdicion;
        objNombre.classList.toggle('is-autofilled', !permitirEdicion && objNombre.value !== '');
      }

      function limpiarDatosAccionista() {
        var objNombre = document.getElementById('nombre');
        var objCorreo = document.getElementById('correo');

        if (objNombre) {
          objNombre.value = '';
          objNombre.classList.remove('is-autofilled');
        }

        if (objCorreo) objCorreo.value = '';

        setCedulaFeedback('', 'neutral');
        setNombreFeedback('', 'neutral');
        habilitarEdicionManualNombre(true);
      }

      function aplicarDatosAccionista(datos) {
        var objNombre = document.getElementById('nombre');
        var objCorreo = document.getElementById('correo');
        var nombre = (datos && datos.nombre ? datos.nombre : '').trim();
        var correo = (datos && datos.correo ? datos.correo : '').trim();

        if (objNombre) {
          objNombre.value = nombre;
          habilitarEdicionManualNombre(nombre === '');
        }

        if (objCorreo && correo !== '') objCorreo.value = correo;

        if (nombre !== '') {
          setCedulaFeedback('Accionista localizado correctamente.', 'success');
          setNombreFeedback('El nombre se complet\u00f3 autom\u00e1ticamente seg\u00fan la c\u00e9dula digitada.', 'success');
        } else {
          setCedulaFeedback('No se encontraron datos para esta c\u00e9dula. Puede completar el nombre manualmente.', 'warning');
          setNombreFeedback('No fue posible completar el nombre de forma autom\u00e1tica.', 'warning');
          habilitarEdicionManualNombre(true);
        }
      }

      function revisarCedula(cedula) {
        var cedulaLimpia = limpiarCedula(cedula);
        var objCedula = document.getElementById('cedula');

        if (objCedula && objCedula.value !== cedulaLimpia) objCedula.value = cedulaLimpia;

        if (cedulaLookupTimer) window.clearTimeout(cedulaLookupTimer);

        if (cedulaLimpia.length === 0) {
          limpiarDatosAccionista();
          return;
        }

        if (cedulaLimpia.length < 8) {
          limpiarDatosAccionista();
          setCedulaFeedback('Digite la c\u00e9dula completa para buscar al accionista.', 'neutral');
          return;
        }

        setCedulaFeedback('Buscando datos del accionista...', 'neutral');
        setNombreFeedback('Consultando el nombre asociado a la c\u00e9dula.', 'neutral');

        cedulaLookupTimer = window.setTimeout(function() {
          fetch('ajax.php?accion=leerSocio&cedula=' + encodeURIComponent(cedulaLimpia), { cache: 'no-cache' })
          .then(function(response) {
            if (response.status !== 200) {
              throw new Error('Servicio no disponible. Estado HTTP: ' + response.status);
            }

            return response.json();
          })
          .then(function(json) {
            aplicarDatosAccionista(json || {});
          })
          .catch(function(err) {
            console.log('Ocurri\u00f3 un error con la ejecuci\u00f3n', err);
            setCedulaFeedback('No fue posible consultar el servicio en este momento.', 'warning');
            setNombreFeedback('Puede completar el nombre manualmente mientras el servicio vuelve a estar disponible.', 'warning');
            habilitarEdicionManualNombre(true);
          });
        }, 350);
      }

      function cambiarSucursal(select) {
        sucursal_id = select.options[select.selectedIndex].value;

        if (document.getElementById('agente')) {
          document.getElementById('agente').innerHTML = '<option disabled selected hidden value=\"\">-- Agente --</option>';
        }

        if (document.getElementById('gestion')) {
          document.getElementById('gestion').innerHTML = '<option disabled selected hidden value=\"\">-- Gesti&oacute;n --</option>';
        }

        fetch('ajax.php?accion=cambiarSucursal&sucursal=' + sucursal_id)
        .then(function(result) {
          return result.text();
        })
        .then(function(data) {
          document.getElementById('unidad').innerHTML = '<option disabled selected hidden value=\"\">-- Unidad --" . $uni_req_tex . "</option>' + data;
        })
        .catch(function(err) {
          console.log('Ocurri\u00f3 un error con la ejecuci\u00f3n', err);
        });
      }

      function cambiarUnidad(select) {
        unidad_id = select.options[select.selectedIndex].value;
        sucursal_id = document.getElementById('sucursal').value;

        if (document.getElementById('agente')) {
          fetch('ajax.php?accion=cambiarUnidadAgente&sucursal=' + sucursal_id + '&unidad=' + unidad_id)
          .then(function(result) {
            return result.text();
          })
          .then(function(data) {
            document.getElementById('agente').innerHTML = '<option disabled selected hidden value=\"\">-- Agente --" . $age_req_tex . "</option>' + data;
          })
          .catch(function(err) {
            console.log('Ocurri\u00f3 un error con la ejecuci\u00f3n', err);
          });
        }

        if (document.getElementById('gestion')) {
          fetch('ajax.php?accion=cambiarUnidadGestion&unidad=' + unidad_id)
          .then(function(result) {
            return result.text();
          })
          .then(function(data) {
            document.getElementById('gestion').innerHTML = '<option disabled selected hidden value=\"\">-- Gesti&oacute;n --" . $ges_req_tex . "</option>' + data;
          })
          .catch(function(err) {
            console.log('Ocurri\u00f3 un error con la ejecuci\u00f3n', err);
          });
        }
      }

      function cambiarProvincia(select) {
        provincia_id = select.options[select.selectedIndex].value;

        document.getElementById('distrito').innerHTML = '<option disabled selected hidden value=\"\">-- Distrito --</option>';

        fetch('ajax.php?accion=cambiarProvincia&provincia=' + provincia_id)
        .then(function(result) {
          return result.text();
        })
        .then(function(data) {
          document.getElementById('canton').innerHTML = '<option disabled selected hidden value=\"\">-- Cant&oacute;n --</option>' + data;
        })
        .catch(function(err) {
          console.log('Ocurri\u00f3 un error con la ejecuci\u00f3n', err);
        });
      }

      function cambiarCanton(select) {
        canton_id = select.options[select.selectedIndex].value;

        fetch('ajax.php?accion=cambiarCanton&canton=' + canton_id)
        .then(function(result) {
          return result.text();
        })
        .then(function(data) {
          document.getElementById('distrito').innerHTML = '<option disabled selected hidden value=\"\">-- Distrito --</option>' + data;
        })
        .catch(function(err) {
          console.log('Ocurri\u00f3 un error con la ejecuci\u00f3n', err);
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

      function seleccionarOpcionLista(select, padre_id, capa_text_otro, text_otro) {
        var opcion = select.options[select.selectedIndex];
        var opcion_id = opcion.value;
        var es_otro = opcion.getAttribute('data-otro') == '1';
        var capa_otro = document.getElementById(capa_text_otro);
        var input_otro = document.getElementById(text_otro);

        if (capa_otro && input_otro) {
          input_otro.value = '';
          capa_otro.style.display = es_otro ? 'block' : 'none';
        }

        mostrarOtrasPreguntas(padre_id, opcion_id);
      }

      function seleccionarOpcionesListaMultiple(select, capa_text_otro, text_otro) {
        var opciones = Array.from(select.selectedOptions);
        var mostrar_otro = opciones.some(function(opcion) {
          return opcion.getAttribute('data-otro') == '1';
        });
        var capa_otro = document.getElementById(capa_text_otro);
        var input_otro = document.getElementById(text_otro);

        if (capa_otro && input_otro) {
          if (!mostrar_otro) input_otro.value = '';
          capa_otro.style.display = mostrar_otro ? 'block' : 'none';
        }
      }

      function toggleMultiSelect(dropdownId) {
        var dropdown = document.getElementById(dropdownId);
        if (!dropdown) return;

        document.querySelectorAll('.survey-multiselect__menu').forEach(function(menu) {
          if (menu.id !== dropdownId) {
            menu.classList.add('d-none');
            menu.classList.remove('is-open-up');
          }
        });

        if (dropdown.classList.contains('d-none')) {
          dropdown.classList.remove('is-open-up');
          dropdown.classList.remove('d-none');

          var rect = dropdown.getBoundingClientRect();
          var spaceBelow = window.innerHeight - rect.top;
          var spaceAbove = rect.top;
          var needsOpenUp = rect.height > spaceBelow && spaceAbove > spaceBelow;

          if (needsOpenUp) dropdown.classList.add('is-open-up');
        } else {
          dropdown.classList.add('d-none');
          dropdown.classList.remove('is-open-up');
        }
      }

      function sincronizarSeleccionMultiple(baseName, summaryId, capa_text_otro, text_otro) {
        var hiddenSelect = document.getElementById('hidden_' + baseName);
        var summary = document.getElementById(summaryId);
        var seleccionados = [];

        if (!hiddenSelect || !summary) return;

        var multiselect = document.querySelector(\"[data-multiselect-name='\" + baseName + \"']\");
        if (!multiselect) return;

        var checkboxes = Array.from(multiselect.querySelectorAll('input[type=\"checkbox\"]'));

        Array.from(hiddenSelect.options).forEach(function(option) {
          option.selected = false;
        });

        checkboxes.forEach(function(checkbox) {
          if (checkbox.checked) {
            var option = Array.from(hiddenSelect.options).find(function(item) {
              return item.value === checkbox.value;
            });

            if (option) {
              option.selected = true;
              seleccionados.push(option.text);
            }
          }
        });

        summary.textContent = seleccionados.length > 0
          ? seleccionados.join(', ')
          : 'Seleccione una o varias opciones';

        seleccionarOpcionesListaMultiple(hiddenSelect, capa_text_otro, text_otro);
      }

      function inicializarMultiselects() {
        document.querySelectorAll('[data-multiselect-name]').forEach(function(multiselect) {
          var baseName = multiselect.getAttribute('data-multiselect-name');
          var summary = multiselect.querySelector('.survey-multiselect__summary');
          var hiddenSelect = document.getElementById('hidden_' + baseName);

          if (!summary || !hiddenSelect) return;

          var summaryId = summary.id;
          var capaOtro = '';
          var textoOtro = '';

          if (baseName) {
            textoOtro = baseName + '_otro';
            capaOtro = 'capa_' + textoOtro;
          }

          sincronizarSeleccionMultiple(baseName, summaryId, capaOtro, textoOtro);
        });
      }

      document.addEventListener('click', function(event) {
        if (!event.target.closest('[data-multiselect]')) {
          document.querySelectorAll('.survey-multiselect__menu').forEach(function(menu) {
            menu.classList.add('d-none');
            menu.classList.remove('is-open-up');
          });
        }
      });

      function seleccionarBooleanoLista(select, capa_text_exp, text_exp, true_exp, false_exp) {
        var capa_exp = document.getElementById(capa_text_exp);
        var input_exp = document.getElementById(text_exp);
        var value = select.value;
        var mostrar = (value == '1' && true_exp == 1) || (value == '0' && false_exp == 1);

        if (capa_exp && input_exp) {
          if (!mostrar) input_exp.value = '';
          capa_exp.style.display = mostrar ? 'block' : 'none';
        }
      }

      function mostrarOtrasPreguntas(padre_seleccionado_id, opcion_seleccionada_id) {
        const capas_dep = document.querySelectorAll('[dep_alguien=\'1\']');

        capas_dep.forEach((capa_dep) => {
          if (capa_dep.getAttribute('dep_padre') == padre_seleccionado_id) {
            const dep_hijos_id_arr = capa_dep.getAttribute('dep_hijos').split(',');
            const det_id = capa_dep.getAttribute('det_id');
            var mostrar_capa = false;
            dep_hijos_id_arr.forEach(temp_hijo => {
              if (temp_hijo == opcion_seleccionada_id) mostrar_capa = true;
            });

            if (mostrar_capa) {
              capa_dep.classList.remove('d-none');
              capa_dep.innerHTML = dependencias_js[det_id];
            } else {
              capa_dep.classList.add('d-none');
              capa_dep.innerHTML = '';
            }
          }
        });
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
              el[i].disabled = false;
            else {
              el[i].disabled = opcion != el[i].id;
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
        capa_exp.style.display = (val_bool == 1) ? 'block' : 'none';
      }

      document.addEventListener('DOMContentLoaded', function() {
        inicializarEtapasEncuesta();
        inicializarMultiselects();
      });
    </script>";
} else {
  $msg_html = "";
  if (!isset($encuesta)) $msg_html = "Se present&oacute; un error al cargar el formulario.";
  if (!$encuesta_activa && empty($msg_html)) $msg_html = "El formulario se encuentra inactivo.";
  if ($mostrar_mensaje_fechas) $msg_html = "El formulario se activar&aacute; el " . $encuesta["fecha_inicio_format"] . ".";
  if (!empty($encuesta_msg_bloqueo) && empty($msg_html)) $msg_html = $encuesta_msg_bloqueo;
  echo "
    <main role='main' class='survey-page'>
      <div class='survey-shell container py-4 py-md-5'>
        <section class='survey-card survey-card--message text-center'>
          <img src='https://storageproyectosmercadeo.blob.core.windows.net/masaccesosbeneficios/banners/Logocajadeande.png' class='img-logo mb-4' alt='Caja de ANDE'/>
          <h3 class='survey-title survey-title--message'>" . $msg_html . "</h3>
        </section>
      </div>
    </main>";
}
include_once "footer.php";
?>
