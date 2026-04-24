<?php

class QueryEncuestaDet {
  
  public $encuesta_det;
  public $det_id = 0;
  public $det_nom = "";
  public $det_enunciado = "";
	public $det_placeholder = "";
	public $det_texto_desc = "";
  public $det_requerido = false;
	public $det_requerido_ast = "";
  public $det_tipo_id = 0;
  public $num_pregunta = 0;
  public $num_pregunta_texto = "";
  public $dep_enc_det_id = null;
  public $dep_enc_det_opcion_id = null;
  public $meta = null;
  //public $dependencias = null;
  public $tipos_archivo = null;
  public $muestra_num_pregunta = true;
  
  function __construct($p_encuesta_det, $encuesta) {
    $this->encuesta_det = $p_encuesta_det;
    
    $this->det_id = $p_encuesta_det["encuesta_det_id"];
    $this->det_enunciado = $p_encuesta_det["enunciado"];
		$this->det_placeholder = $p_encuesta_det["placeholder"];
    $this->det_texto_desc = $p_encuesta_det["texto_descripcion"];
    $this->det_requerido = ($p_encuesta_det["opcional"] == 0) ? "required" : "";
    $this->det_requerido_ast = ($p_encuesta_det["opcional"] == 0) ? 
      "<span class='font-weight-bold text-danger'>*</span>" : "";
    $this->det_tipo_id = $p_encuesta_det["tipo_id"];
    $this->dep_enc_det_id = $p_encuesta_det["dep_encuesta_det_id"];
    $this->dep_enc_det_opcion_id = $p_encuesta_det["dep_encuesta_det_opcion_id"];
    
    $this->meta = $p_encuesta_det["meta"];
    //$this->dependencias = $p_encuesta_det["dependencias"];
    $this->tipos_archivo = $p_encuesta_det["tipos_archivo"];
    
    $this->det_nom = $this->crearNameParaPost();
    
    $this->muestra_num_pregunta = ($encuesta["mostrar_num_pregunta"] ?? 1) == 1;
  }
  
  public function setNumPregunta($p_num_pregunta) { 
    $this->num_pregunta = $p_num_pregunta;
    
    if ($this->muestra_num_pregunta) $this->num_pregunta_texto = $this->num_pregunta . ". ";
  }
  
  public function getNuevoNumPregunta() { 
    $temp_n = $this->num_pregunta;
    switch ($this->det_tipo_id) {
      case EDT_NUMERO:      $temp_n = $this->num_pregunta + 1; break;
      case EDT_MONTO:       $temp_n = $this->num_pregunta + 1; break;
      case EDT_CORREO:      $temp_n = $this->num_pregunta + 1; break;
      case EDT_FECHA:       $temp_n = $this->num_pregunta + 1; break;
      case EDT_TEXTO_C:     $temp_n = $this->num_pregunta + 1; break;
      case EDT_TEXTO_L:     $temp_n = $this->num_pregunta + 1; break;
      case EDT_SEL_UNI_COM: $temp_n = $this->num_pregunta + 1; break;
      case EDT_SEL_UNI_RAD: $temp_n = $this->num_pregunta + 1; break;
      case EDT_SEL_MUL:     $temp_n = $this->num_pregunta + 1; break;
      case EDT_RATING_PTO:  $temp_n = $this->num_pregunta + 1; break;
      case EDT_RATING_BAR:  $temp_n = $this->num_pregunta + 1; break;
      case EDT_ARCHIVO:     $temp_n = $this->num_pregunta + 1; break;
      case EDT_LOCATION:    $temp_n = $this->num_pregunta + 1; break;
      case EDT_TEXTO_SEP:   break;
      case EDT_BOOLEANO:    $temp_n = $this->num_pregunta + 1; break;
      case EDT_SUBTITULO:   break;
      case EDT_PARRAFO:     break;
      case EDT_PROVINCIA:   $temp_n = $this->num_pregunta + 1; break;
      case EDT_RATING_CUA:  $temp_n = $this->num_pregunta + 1; break;
      case EDT_PUESTO_CAJA: $temp_n = $this->num_pregunta + 1; break;
      default: break;
    }
    return $temp_n;
  }
  
  public function getClaseBloqTipo() { 
    if (!$this->dependeDeAlgo()) return "";
    
    switch ($this->det_tipo_id) {
      case EDT_NUMERO:      return " bloq-input ";
      case EDT_MONTO:       return " bloq-input ";
      case EDT_CORREO:      return " bloq-input ";
      case EDT_FECHA:       return " bloq-input ";
      case EDT_TEXTO_C:     return " bloq-input ";
      case EDT_TEXTO_L:     return " bloq-input ";
      case EDT_SEL_UNI_COM: return " bloq-combo ";
      case EDT_SEL_UNI_RAD: return " bloq-radio ";
      case EDT_SEL_MUL:     return " bloq-check ";
      case EDT_RATING_PTO:  return " bloq-radio ";
      case EDT_RATING_BAR:  return " bloq-range ";
      case EDT_ARCHIVO:     return " bloq-file ";
      case EDT_LOCATION:    return "";
      case EDT_TEXTO_SEP:   return "";
      case EDT_BOOLEANO:    return " bloq-radio ";
      case EDT_SUBTITULO:   return "";
      case EDT_PARRAFO:   	return "";
      case EDT_PROVINCIA:   return " bloq-combo ";
      case EDT_PUESTO_CAJA: return " bloq-combo ";
      default:              return "";
    }
  }
  
  private function crearNameParaPost() {
    // creo el name de esta manera para poder utilizarlo y hacer valiaciones a la hora
    // de interactuar con la base de datos. Del lado de la base de datos se hace un 
    // explode de _ para sacar el id del item de detalle
    return "det_" . $this->det_id;
  }
  
  public function dependeDeAlgo() { 
    return isset($this->dep_enc_det_id) && $this->dep_enc_det_id != "" && isset($this->dep_enc_det_opcion_id) && $this->dep_enc_det_opcion_id != "";
  }
  
  public function getHtmlTextoDescripcion() {
		if ($this->det_texto_desc != "") {
			return "<small>" . $this->det_texto_desc . "</small>";
		}
		
    return "";
  }
  
  public function claseDisplayNoneDependencias() { // define si la pregunta depende de otra, de inicio aparece oculta
    if (isset($this->dep_enc_det_id) && $this->dep_enc_det_id != "" && 
        isset($this->dep_enc_det_opcion_id) && $this->dep_enc_det_opcion_id != "") {
      return "d-none";
    }
    return "";
  }
  
  public function atributosDependencias() { // incluye los atributos de la pregunta y la opcion de la que depende la pregunta actual
    $ret = "";
    if (isset($this->dep_enc_det_id) && $this->dep_enc_det_id != "") $ret .= " dep_padre='" . $this->dep_enc_det_id . "' ";
    if (isset($this->dep_enc_det_opcion_id) && $this->dep_enc_det_opcion_id != "") $ret .= " dep_hijos='" . $this->dep_enc_det_opcion_id . "' ";
    if ($ret != "") $ret .= " dep_alguien='1' ";
    return $ret;
  }
    
  public function cargaNumero() { 
    $min = " min='" . ($this->meta[META_MIN] ?? 0) . "' ";
    $max = " max='" . ($this->meta[META_MAX] ?? 100000000) . "' ";
    $step = " step='" . ($this->meta[META_STEP] ?? 1) . "' ";
		$placeholder = ($this->det_placeholder == "") ? "Número" : $this->det_placeholder;
    
    return "
              <div class='col-12 col-md-6'>
                <div class='form-group'>
                  <label>" . $this->num_pregunta_texto . $this->det_requerido_ast . $this->det_enunciado . "</label>
                  <input class='form-control' type='number' " . $min . $max . $step . " placeholder='" . $placeholder . "' 
                         name='" . $this->det_nom . "' " . $this->det_requerido . ">" . 
									$this->getHtmlTextoDescripcion() . "
                </div>
              </div>";
  }
  
  public function cargaMonto() { 
    $min = "min='0'";
    $step = "step='0.01'";
		$placeholder = ($this->det_placeholder == "") ? "Número" : $this->det_placeholder;
    
    return "
              <div class='col-12 col-md-6'>
                <div class='form-group'>
                  <label>" . $this->num_pregunta_texto . $this->det_requerido_ast . $this->det_enunciado . "</label>
                  <input class='form-control' type='number' " . $min . $step . " placeholder='Monto' 
                         name='" . $this->det_nom . "' " . $this->det_requerido . ">" . 
									$this->getHtmlTextoDescripcion() . "
                </div>
              </div>";
  }
  
  public function cargaCorreo() { 
    return "
              <div class='col-12 col-md-6'>
                <div class='form-group'>
                  <label>" . $this->num_pregunta_texto . $this->det_requerido_ast . $this->det_enunciado . "</label>
                  <input class='form-control' type='email' placeholder='correo@dominio.com' name='" . $this->det_nom . "' " . $this->det_requerido . ">" . 
									$this->getHtmlTextoDescripcion() . "
                </div>
              </div>";
  }
  
  public function cargaFecha() { 
    if (isset($this->meta)) {
      if (isset($this->meta[META_MIN])) $meta_min = $this->meta[META_MIN];
      if (isset($this->meta[META_MAX])) $meta_max = $this->meta[META_MAX];
    }
    
    $min = (isset($meta_min)) ? " min='" . $meta_min . "'" : "";
    $max = (isset($meta_max)) ? " max='" . $meta_max . "'" : "";
    
    return "
              <div class='col-12 col-md-6'>
                <div class='form-group'>" . $this->num_pregunta_texto . $this->det_requerido_ast . $this->det_enunciado . "
                  <input class='form-control' type='date' " . $min . $max . " placeholder='Fecha' 
                         name='" . $this->det_nom . "' " . $this->det_requerido . ">" . 
									$this->getHtmlTextoDescripcion() . "
                </div>
              </div>";
  }
  
  public function cargaTextoCorto() {
    $tipo = "C";
    return $this->cargaTexto($tipo);
  }
  
  public function cargaTextoLargo() {
    $tipo = "L";
    return $this->cargaTexto($tipo);
  }
  
  private function cargaTexto($tipo) {
    $ret = "";
    
    $max_length = isset($this->meta[META_MAX]) ? (" maxlength='" . $this->meta[META_MAX] . "' ") : "";
    
    $ret .= "
              <div class='col-12 col-md-6'>
                <div class='form-group'>
                  <label>" . $this->num_pregunta_texto . $this->det_requerido_ast . $this->det_enunciado . "</label>";
    if ($tipo == "C") {
      $ret .= "
                  <input class='form-control' type='text' name='" . $this->det_nom . "' " . $max_length . $this->det_requerido . ">" . $this->getHtmlTextoDescripcion();
    } else {
      $ret .= "
                  <textarea class='form-control' name='" . $this->det_nom . "' " . $max_length . $this->det_requerido . "></textarea>" . $this->getHtmlTextoDescripcion();
    }
    
    $ret .= "
                </div>
              </div>";
             
    return $ret;
  }
  
  public function cargaSeleccionUnicaCombo() { // AUN NO VALIDAMOS ESTA OPCION (LA OPCION DE OTRO NO ESTA)
    $ret = "";
    $opciones = $this->encuesta_det["opciones"];
    $dis = $encuesta["puesto_oblig"] == 1 ? "disabled hidden" : "";
    $tiene_otro = false;
    
    $ret .= "
              <div class='col-12 col-md-6'>
                <p class='mb-0'>" . $this->num_pregunta_texto . $this->det_requerido_ast . $this->det_enunciado . "</p>
                <select class='form-control' name='" . $this->det_nom . "' " . $this->det_requerido . ">
                  <option " . $dis . " selected value=''>-- Seleccione una opción --</option>";
    foreach ($opciones as $opcion) {
			$sel = $opcion["seleccionada"] == 1 ? " selected " : "";
			
      if ($opcion["activo"] == "1") {
        $ret .= "
                    <option value='" . $opcion["encuesta_det_opcion_id"] . "'" . $sel . ">" . $opcion["texto"] . "</option>";
        
        if ($opcion["otro"] == 1) $tiene_otro = true;
      }
    }
    $ret .= "
                </select>";
    if ($tiene_otro) {
      $ret .= "
                <div class='col-12 col-md-6'>

                </div>";
    }
    $ret .= "
              </div>";
              
    return $ret;
  }
  
  public function cargaSeleccionUnicaRadio() { // Estetica sin revisar
    $ret = "";
    $opciones = $this->encuesta_det["opciones"];
    $tiene_otro = false;
    $name_otro = Funciones::name_otro($this->det_nom);
    $name_capa_otro = "capa_" . $name_otro;
    
    foreach ($opciones as $opcion) {
      if ($opcion["activo"] == "1") {
        if ($opcion["otro"] == 1) $tiene_otro = true;
      }
    }
    
    $ret .= "
              <div class='col-12'>
                <p class='mb-0'>" . $this->num_pregunta_texto . $this->det_requerido_ast . $this->det_enunciado . "</p>";
    $cont_opc = 1;
    $count_opc = count($opciones);
    foreach ($opciones as $opcion) {
      $opcion_id = $opcion["encuesta_det_opcion_id"];
			$chk = $opcion["seleccionada"] == 1 ? " checked " : "";
      
      if ($opcion["activo"] == "1") {
        if ($tiene_otro) {
          // _ot para la opcion de otro, _no para las opciones normales
          $opc_js_id = $opcion["encuesta_det_opcion_id"] . ($opcion["otro"] == 1 ? "_ot" : "_no"); 
          $opc_js_id_texto = " id='" . $opc_js_id . "' "; 
          
          $on_click = " onclick='mostrarOtroSelUni(\"".$this->det_id."\", \"".$opcion_id."\", \"".$opc_js_id."\", \"".$name_capa_otro."\", \"".$name_otro."\")' ";
        } else {
          $opc_js_id_texto = "";
          $on_click = " onclick='mostrarOtrasPreguntas(\"".$this->det_id."\", \"".$opcion_id."\")' ";
        }
        
        $req = ($cont_opc == $count_opc) ? $this->det_requerido : "";
        
        $ret .= "
                  <div class='form-check form-check-inline'>
                    <input class='form-check-input' type='radio' " . $opc_js_id_texto . $on_click . " name='" . $this->det_nom . "' 
                           value='" . $opcion_id . "' " . $chk . $req . ">
                    <label class='form-check-label'>" . $opcion["texto"] . "</label>
                  </div>";
        
        $cont_opc += 1;
      }
    }
    
    if ($tiene_otro) {
      $ret .= "
                <div class='form-check form-check-inline' id='" . $name_capa_otro . "' style='display: none'>
                  <input class='form-control' type='text' id='" . $name_otro . "' name='" . $name_otro . "'  maxlength='255'>
                </div>";
    }
    
    $ret .= "
              </div>";
              
    return $ret;
  }
  
  public function cargaSeleccionMultiple() { // Estetica sin revisar
    $ret = "";
    $opciones = $this->encuesta_det["opciones"];
//    $on_click = ($this->det_requerido == "") ? "" : " onclick='validarSelMul(\"".$this->det_nom."\")' ";
    $tiene_otro = false;
    $name_otro = Funciones::name_otro($this->det_nom);
    $name_capa_otro = "capa_" . $name_otro;
    
    foreach ($opciones as $opcion) {
      if ($opcion["activo"] == "1") {
        if ($opcion["otro"] == 1) $tiene_otro = true;
      }
    }
    
    $ret .= "
              <div class='col-12'>
                <p class='mb-0'>" . $this->num_pregunta_texto . $this->det_requerido_ast . $this->det_enunciado . "</p>";
    foreach ($opciones as $opcion) {
      if ($opcion["activo"] == "1") {
        $opc_bloq = $opcion["bloquea_otras"];
        
        if ($opcion["otro"] == 1) {
          $opc_js_id = $opcion["encuesta_det_opcion_id"] . "_ot";
          $opc_js_id_texto = " id='" . $opc_js_id . "' "; 
          
          if ($this->det_requerido == "") {
            $on_click = " onclick='mostrarOtroSelMul(\"".$opc_js_id."\", \"".$name_capa_otro."\", \"".$name_otro."\", ".$opc_bloq.")' ";
          } else {
            $on_click = " onclick='validarSelMulMostrarOtro(\"".$this->det_nom."\", \"".$opc_js_id."\", \"".$name_capa_otro."\", \"".$name_otro."\", ".$opc_bloq.")' ";
          }
        } else {
          $opc_js_id = $opcion["encuesta_det_opcion_id"];
          $opc_js_id_texto = " id='" . $opc_js_id . "' "; 
          
          $on_click = ($this->det_requerido == "") ? "" : " onclick='validarSelMul(\"".$this->det_nom."\", \"".$opc_js_id."\", \"".$name_capa_otro."\", \"".$name_otro."\", ".$opc_bloq.")' ";
        }
        
        $ret .= "
                <div class='form-check form-check-inline'>
                  <input class='form-check-input " . $this->det_nom . "' type='checkbox' " . $opc_js_id_texto . $on_click . " name='" . $this->det_nom . "[]' value='" . $opcion["encuesta_det_opcion_id"] . "' " . $this->det_requerido . " " . $on_click . ">
                  <label class='form-check-label'>" . $opcion["texto"] . "</label>
                </div>";
      }
    }
    
    if ($tiene_otro) {
      $ret .= "
                <div class='form-check form-check-inline' id='" . $name_capa_otro . "' style='display: none'>
                  <input class='form-control' type='text' id='" . $name_otro . "' name='" . $name_otro . "'  maxlength='255'>
                </div>";
    }
    
    $ret .= "
              </div>";
              
    return $ret;
  }
  
  public function cargaRatingPuntos() {
    $ret = "";
    
    $meta_min = $this->meta[META_MIN] ?? 0;
    $meta_max = $this->meta[META_MAX] ?? 10;
    
    $ret .= "
              <div class='col-12'>
                <p class='mb-0'>" . $this->num_pregunta_texto . $this->det_requerido_ast . $this->det_enunciado . "</p>";
    $cont_meta = $meta_min;
    while ($cont_meta <= $meta_max) {
      $ret .= "
                <div class='form-check form-check-inline'>
                  <input class='form-check-input' type='radio' name='".$this->det_nom."' 
                         value='".$cont_meta."' ".($cont_meta == $meta_min ? $this->det_requerido : "").">
                  <label class='form-check-label'>".$cont_meta."</label>
                </div>";
      $cont_meta += 1;
    }
    $ret .= "
              </div>";
              
    return $ret;
  }
  
  public function cargaRatingBarra() {
    $ret = "";
    
    $meta_min = $this->meta[META_MIN] ?? 0;
    $meta_max = $this->meta[META_MAX] ?? 10;
    $meta_step = $this->meta[META_STEP] ?? 1;
    
    $ret .= "
              <div class='col-12'>
                <p class='mb-0'>" . $this->num_pregunta_texto . $this->det_requerido_ast . $this->det_enunciado . "</p>
                <div class='form-group mb-0'>
                  <input class='form-control-range' type='range' name='".$this->det_nom."' min='".$meta_min."' max='".$meta_max."' step='".$meta_step."' value='".$meta_max."' ".$this->det_requerido.">
                </div>
              </div>
              <div class='col-4'>".$meta_min."</div>
              <div class='col-4 text-center'>".(ceil)($meta_max/2)."</div>
              <div class='col-4 text-right'>".$meta_max."</div>";
              
    return $ret;
  }
  
  public function cargaRatingCuadros() {
    $ret = "";
    
    $meta_min = $this->meta[META_MIN] ?? 0;
    $meta_max = $this->meta[META_MAX] ?? 10;
    
    $ret .= "
              <div class='col-12'>
                <p class='mb-0'>" . $this->num_pregunta_texto . $this->det_requerido_ast . $this->det_enunciado . "</p>
                <div class='rating-puntos d-flex'>";
    $cont_meta = $meta_min;
    while ($cont_meta <= $meta_max) {
      $ret .= "
                  <input type='radio' name='".$this->det_nom."' id='".$this->det_nom."_".$cont_meta."' 
                         value='".$cont_meta."' ".($cont_meta == $meta_min ? $this->det_requerido : "").">
                  <label for='".$this->det_nom."_".$cont_meta."'>". ($cont_meta < 10 ? "&nbsp;&nbsp;" : "") . $cont_meta ."</label>
                ";
      $cont_meta += 1;
    }
    $ret .= "
                </div>
                <p><small>Escala de " . $meta_min . " a " . $meta_max . ", donde " . $meta_min . " es nada satisfactorio y " . $meta_max . " es muy satisfactorio</small></p>
              </div>";
              
    return $ret;
  }
  
  public function cargaArchivo() {
    $ret = "";
    
    $meta_size = $this->meta[META_FILE_SIZE] ?? 5;
    $extensiones = "";
    
    $cont_ta = 1;
    $count_ta = count($this->tipos_archivo);
    foreach ($this->tipos_archivo as $tipo_archivo) {
      $extensiones .= $tipo_archivo["extension"] . ($cont_ta == $count_ta ? "" : ",");
      $cont_ta += 1;
    }
    
    $ret .= "
              <div class='col-12'>
                <p class='mb-0'>" . $this->num_pregunta_texto . $this->det_requerido_ast . $this->det_enunciado . "</p>
                <div class='form-group'>
                  <label for='".Funciones::name_file($this->det_nom)."'><small>Seleccione archivo para subir (máximo ".$meta_size."MB) - 
                    Formatos permitidos: " . $extensiones . "</small>
                  </label>
                  <input type='file' class='form-control-file' id='".Funciones::name_file($this->det_nom)."' 
                         name='".Funciones::name_file($this->det_nom)."' accept='".$extensiones."' " . $this->det_requerido . ">
                  <input type='hidden' name='".$this->det_nom."'>
                </div>
              </div>";
              
    return $ret;
  }
  
  public function cargaTextoSeparador() {
    $ret = "";
    
    $ret .= "
              <div class='col-12'>
                <h6 class='mt-3'>".$this->det_enunciado."</h6>
                <hr class='mt-1 w-25 float-left'/>
              </div>";
              
    return $ret;
  }
  
  public function cargaBooleano() {
    $ret = "";
    
    // si el booleano tiene para explicar la seleccion va a tener registros en encuesta_det_opciones
    // el orden va a decir si es para el true o false (0 - 1)
    
    $name_exp = Funciones::name_exp($this->det_nom);
    $name_capa_exp = "capa_" . $name_exp;
    
    $false_exp = 0; $true_exp = 0;
    $on_click_false = ""; $on_click_true = "";
    $opciones = $this->encuesta_det["opciones"];
    
    foreach ($opciones as $opcion) {
      if ($opcion["orden"] == 0 && $opcion["explicacion"] == 1) $false_exp = 1;
      if ($opcion["orden"] == 1 && $opcion["explicacion"] == 1) $true_exp = 1;
    }
    
    if ($false_exp == 1 || $true_exp == 1) { 
      $on_click_false = " onclick='mostrarExpBool(\"".$name_capa_exp."\", \"".$name_exp."\", ".$false_exp.")' ";
      $on_click_true = " onclick='mostrarExpBool(\"".$name_capa_exp."\", \"".$name_exp."\", ".$true_exp.")' ";
    }
    
    $ret .= "
              <div class='col-12'>
                <p class='mb-0'>" . $this->num_pregunta_texto . $this->det_requerido_ast . $this->det_enunciado . "</p>
                <div class='form-check form-check-inline'>
                  <input class='form-check-input' type='radio' name='".$this->det_nom."' value='1' ".$this->det_requerido . $on_click_true . ">
                  <label class='form-check-label'>Sí</label>
                </div>
                <div class='form-check form-check-inline'>
                  <input class='form-check-input' type='radio' name='".$this->det_nom."' value='0' ".$this->det_requerido . $on_click_false . ">
                  <label class='form-check-label'>No</label>
                </div>";
    if ($false_exp == 1 || $true_exp == 1) {
      $ret .= "
                <div class='form-check form-check-inline' id='" . $name_capa_exp . "' style='display: none'>
                  <input class='form-control' type='text' id='".$name_exp."' name='".$name_exp."' placeholder='¿por qué?' maxlength='255'>
                </div>";
    }
    $ret .= "
              </div>";
              
    return $ret;
  }
  
  public function cargaSubtitulo() {
    $ret = "";
    
    $ret .= "
              <div class='col-12'>
                <h5 class='mt-3'>".$this->det_enunciado."</h6>
              </div>";
              
    return $ret;
  }
  
  public function cargaParrafo() {
    $ret = "";
    
    $ret .= "
              <div class='col-12'>
                <p class='mt-3'>".$this->det_enunciado."</p>
              </div>";
              
    return $ret;
  }
  
  public function cargaProvincia($conn) {
    $ret = "";
    $imprimir = false;
    
    $ret .= "
              <div class='col-12 col-md-6'>
                <p class='mb-0'>" . $this->num_pregunta_texto . $this->det_requerido_ast . $this->det_enunciado . "</p>
                <div class='form-group'>
                  <select class='form-control' name='".$this->det_nom."'>
                    <option disabled selected hidden value=''>-- Provincia --</option>";
    $ret .= Query::listarProvincias($conn, $imprimir);
    $ret .= "
                  </select>
                </div>
              </div>";
              
    return $ret;
  }
  
  public function cargaDireccionEnvio($conn) {
    $ret = "";
    $imprimir = false;
    
    $inc_domicilio = $this->meta[META_INC_DOMICILIO] ?? 0;
    
    $ret .= "
              <div class='col-12 col-md-6'>
                <p class='mb-0'>" . $this->num_pregunta_texto . $this->det_requerido_ast . $this->det_enunciado . "</p>
                <div class='form-group'>
                  <select class='form-control' name='".$this->det_nom."' " . $this->det_requerido . ">
                    <option disabled selected hidden value=''>-- Seleccione una opción --</option>";
    $ret .= Query::listarDireccionesEnvio($conn, $inc_domicilio, $imprimir);
    $ret .= "
                  </select>
                </div>
              </div>";
              
    return $ret;
  }
  
  public function cargaLinkExterno() {
    $ret = "";
    
    $link = $this->meta[META_URL] ?? "";
    
    $ret .= "
              <div class='col-12'>
                <p class='mb-0'>" . $this->det_requerido_ast . $this->det_enunciado . "</p>
                <div class='form-group'>
                  <a href='" . $link . "' target='_blank'>Abrir enlace</a>
                </div>
              </div>";
              
    return $ret;
  }

  public function cargaPuestosCaja($conn) {
    $ret = "";
    $imprimir = false;
    
    $ret .= "
              <div class='col-12 col-md-6'>
                <p class='mb-0'>" . $this->num_pregunta_texto . $this->det_requerido_ast . $this->det_enunciado . "</p>
                <div class='form-group'>
                  <select class='form-control' name='".$this->det_nom."'>
                    <option disabled selected hidden value=''>-- Seleccione un puesto --</option>";
    $ret .= Query::listarPuestosCaja($conn, $imprimir);
    $ret .= "
                  </select>
                </div>
              </div>";
              
    return $ret;
  }
  
}

?>