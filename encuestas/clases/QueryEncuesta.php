<?php

class QueryEncuesta {

  public static function cargarCedula($conn, $encuesta) {
    $incluye_nombre = $encuesta["incluye_nombre"] == 1;
    $incluye_cedula = $encuesta["incluye_cedula"] == 1;
    $ced_req = $encuesta["cedula_oblig"] == 1 ? " required " : "";
    $ced_req_tex = $encuesta["cedula_oblig"] == 1 ? " (formato 9 dígitos:)" : " (formato 9 dígitos: )";
    $on_input = "";
    if ($incluye_nombre) $on_input = " oninput='revisarCedula(this.value)' ";

    if ($incluye_cedula) {
      echo "
              <div class='col-12 col-md-6'>
                <div class='form-group'>
                  <label class='field-kicker'>Cédula" . $ced_req_tex . "</label>
                  <input type='text' class='form-control' autocomplete='off' inputmode='numeric' id='cedula' name='cedula' placeholder='Digite su cédula'" . $ced_req . $on_input . "
                         maxlength='12' onkeypress='return event.charCode >= 48 && event.charCode <= 57 && this.value.length < 12'>
                  <small id='cedulaFeedback' class='d-none'></small>
                  <label style='font-size: 12px;' class=''>Ingrese solo números sin guiones ni espacios</label>
                </div>
              </div>";
    }
  }

  public static function cargarNombre($conn, $encuesta) {
    $incluye_nombre = $encuesta["incluye_nombre"] == 1;
    $nom_req = $encuesta["nombre_oblig"] == 1 ? " required" : "";
    $nom_req_tex = $encuesta["nombre_oblig"] == 1 ? "" : " (opcional)";

    if ($incluye_nombre) {
      echo "
              <div class='col-12 col-md-6'>
                <div class='form-group'>
                  <label class='field-kicker'>Nombre completo" . $nom_req_tex . "</label>
                  <input type='text' class='form-control' autocomplete='off' id='nombre' name='nombre' placeholder='Digite su nombre completo'" . $nom_req . "
                         onkeypress='return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || event.charCode == 32'>
                  <small id='nombreFeedback' class='d-none'></small>
                </div>
              </div>";
    }
  }

  public static function cargarCorreo($conn, $encuesta) {
    $incluye_cedula = $encuesta["incluye_cedula"] == 1;
    $incluye_correo = $encuesta["incluye_correo"] == 1;
    $correo_req = $encuesta["correo_oblig"] == 1 ? " required " : "";
    $correo_req_tex = $encuesta["correo_oblig"] == 1 ? "" : " (opcional)";

    if ($incluye_correo) {
      echo "
              <div class='col-12 col-md-6 " . ($incluye_cedula ? " d-none " : "") . "'>
                <div class='form-group'>
                  <label class='field-kicker'>Correo electr&oacute;nico" . $correo_req_tex . "</label>
                  <input type='email' class='form-control' id='correo' name='correo' placeholder='nombre@dominio.com'" . $correo_req . ">
                </div>
              </div>";
    }
  }

  public static function cargarTelefono($conn, $encuesta) {
    $incluye_telefono = $encuesta["incluye_telefono"] == 1;
    $telefono_req = $encuesta["telefono_oblig"] == 1 ? " required " : "";
    $telefono_req_tex = $encuesta["telefono_oblig"] == 1 ? "" : " (opcional)";

    if ($incluye_telefono) {
      echo "
              <div class='col-12 col-md-6'>
                <div class='form-group'>
                  <label class='field-kicker'>N&uacute;mero telef&oacute;nico" . $telefono_req_tex . "</label>
                  <input type='number' min='10000000' max='99999999' step='1' class='form-control' name='telefono' placeholder='Ejemplo: 88887777'" . $telefono_req . ">
                </div>
              </div>";
    }
  }

  public static function cargarSucursalUnidadAgente($conn, $encuesta, $sucursal_id, $unidad_id, $agente_id) {
    $incluye_sucursal = $encuesta["incluye_sucursal"] == 1;
    $suc_req = $encuesta["sucursal_oblig"] == 1 ? " required " : "";
    $suc_req_tex = $encuesta["sucursal_oblig"] == 1 ? "" : " (opcional)";

    $incluye_unidad = $encuesta["incluye_unidad"] == 1;
    $uni_req = $encuesta["unidad_oblig"] == 1 ? " required " : "";
    $uni_req_tex = $encuesta["unidad_oblig"] == 1 ? "" : " (opcional)";

    $incluye_agente = $encuesta["incluye_agente"] == 1;
    $agente_req = $encuesta["agente_oblig"] == 1 ? " required " : "";
    $agente_req_tex = $encuesta["agente_oblig"] == 1 ? "" : " (opcional)";

    if ($incluye_sucursal) {
      $suc_read = ($sucursal_id > 0) ? " readonly disabled " : "";
      $uni_read = ($unidad_id > 0) ? " readonly disabled " : "";

      echo "
              <div class='col-12 col-md-6'>
                <div class='form-group'>
                  <label class='field-kicker'>Sucursal" . $suc_req_tex . "</label>
                  <select class='form-control' onchange='cambiarSucursal(this)' id='sucursal' name='sucursal'" . $suc_req . " " . $suc_read . ">
                    <option disabled selected hidden value=''>Seleccione una sucursal</option>";
      Query::listarSucursales($conn, $sucursal_id);
      echo "
                  </select>
                </div>
              </div>";

      if ($suc_read != "") {
        echo "
              <input type='hidden' name='sucursal' value='" . $sucursal_id . "'/>";
      }

      if ($incluye_unidad) {
        $uni_onchange = $incluye_agente ? " onchange='cambiarUnidad(this)' " : "";

        echo "
              <div class='col-12 col-md-6'>
                <div class='form-group'>
                  <label class='field-kicker'>Unidad" . $uni_req_tex . "</label>
                  <select class='form-control' id='unidad' name='unidad'" . $uni_req . $uni_read . $uni_onchange . ">
                    <option disabled selected hidden value=''>Seleccione una unidad</option>";
        if ($unidad_id > 0)
          Query::listarUnidades($conn, $sucursal_id, $unidad_id);
        echo "
                  </select>
                </div>
              </div>";

        if ($uni_read != "") {
          echo "
              <input type='hidden' name='unidad' value='" . $unidad_id . "'/>";
        }
      }
    }

    if ($incluye_agente) {
      $age_read = ($agente_id > 0) ? " readonly disabled " : "";

      echo "
              <div class='col-12 col-md-6'>
                <div class='form-group'>
                  <label class='field-kicker'>Agente" . $agente_req_tex . "</label>
                  <select class='form-control' id='agente' name='agente'" . $agente_req . " " . $age_read . ">
                    <option disabled selected hidden value=''>Seleccione un agente</option>";
      if (!$incluye_sucursal && !$incluye_unidad)
        Query::listarAgentes($conn, $agente_id);
      else
        Query::listarAgentesSucUni($conn, $sucursal_id, $unidad_id, $agente_id);
      echo "
                  </select>
                </div>
              </div>";

      if ($age_read != "") {
        echo "
              <input type='hidden' name='agente' value='" . $agente_id . "'/>";
      }
    }
  }

  public static function cargarGestion($conn, $encuesta, $unidad_id) {
    $incluye_sucursal = $encuesta["incluye_sucursal"] == 1;
    $incluye_unidad = $encuesta["incluye_unidad"] == 1;

    $incluye_gestion = $encuesta["incluye_gestion"] == 1;
    $ges_req = $encuesta["gestion_oblig"] == 1 ? " required " : "";
    $ges_req_tex = $encuesta["gestion_oblig"] == 1 ? "" : " (opcional)";

    if ($incluye_sucursal && $incluye_unidad && $incluye_gestion) {
      echo "
              <div class='col-12 col-md-6'>
                <div class='form-group'>
                  <label class='field-kicker'>Gesti&oacute;n" . $ges_req_tex . "</label>
                  <select class='form-control' id='gestion' name='gestion'" . $ges_req . ">
                    <option disabled selected hidden value=''>Seleccione una gesti&oacute;n</option>";
      Query::listarGestiones($conn, $unidad_id);
      echo "
                  </select>
                </div>
              </div>";
    }
  }

  public static function cargarFechaNac($conn, $encuesta) {
    $incluye_fecha_nac = $encuesta["incluye_fecha_nac"] == 1;
    $fec_nac_req = $encuesta["fecha_nac_oblig"] == 1 ? " required " : "";
    $fec_nac_req_tex = $encuesta["fecha_nac_oblig"] == 1 ? "" : " (opcional)";
    $min_date = " min='1900-01-01' ";
    $max_date = " max='".date("Y-m-d")."' ";

    if ($incluye_fecha_nac) {
      echo "
              <div class='col-12 col-md-6'>
                <div class='form-group'>
                  <label class='field-kicker'>Fecha de nacimiento" . $fec_nac_req_tex . "</label>
                  <input type='text' class='form-control' ". $min_date . $max_date ." name='fecha_nac' placeholder='Seleccione una fecha'" . $fec_nac_req . "
                         onfocus='(this.type=\"date\")' onblur='(this.type=\"text\")'>
                </div>
              </div>";
    }
  }

  public static function cargarEstadosEmp($conn, $encuesta) {
    $incluye_estado_emp = $encuesta["incluye_estado_emp"] == 1;
    $estado_emp_req = $encuesta["estado_emp_oblig"] == 1 ? " required " : "";
    $estado_emp_req_tex = $encuesta["estado_emp_oblig"] == 1 ? "" : " (opcional)";
    $estado_emp_disabled = $encuesta["estado_emp_oblig"] == 1 ? " disabled hidden " : "";

    if ($incluye_estado_emp) {
      echo "
              <div class='col-12 col-md-6'>
                <div class='form-group'>
                  <label class='field-kicker'>Tipo accionista" . $estado_emp_req_tex . "</label>
                  <select class='form-control' name='estado_emp'" . $estado_emp_req . ">
                    <option " . $estado_emp_disabled . " selected value=''>Seleccione una opci&oacute;n</option>";
      Query::listarEstadosEmp($conn);
      echo "
                  </select>
                </div>
              </div>";
    }
  }

  public static function cargarGeneros($conn, $encuesta) {
    $incluye_genero = $encuesta["incluye_genero"] == 1;
    $genero_req = $encuesta["genero_oblig"] == 1 ? " required " : "";
    $genero_req_tex = $encuesta["genero_oblig"] == 1 ? "" : " (opcional)";
    $genero_disabled = $encuesta["genero_oblig"] == 1 ? " disabled hidden " : "";

    if ($incluye_genero) {
      echo "
              <div class='col-12 col-md-6'>
                <div class='form-group'>
                  <label class='field-kicker'>G&eacute;nero" . $genero_req_tex . "</label>
                  <select class='form-control' name='genero'" . $genero_req . ">
                    <option " . $genero_disabled . " selected value=''>Seleccione una opci&oacute;n</option>";
      Query::listarGeneros($conn);
      echo "
                  </select>
                </div>
              </div>";
    }
  }

  public static function cargarPuestos($conn, $encuesta) {
    $incluye_puesto = $encuesta["incluye_puesto"] == 1;
    $puesto_req = $encuesta["puesto_oblig"] == 1 ? " required " : "";
    $puesto_req_tex = $encuesta["puesto_oblig"] == 1 ? "" : " (opcional)";
    $puesto_disabled = $encuesta["puesto_oblig"] == 1 ? " disabled hidden " : "";

    if ($incluye_puesto) {
      echo "
              <div class='col-12 col-md-6'>
                <div class='form-group'>
                  <label class='field-kicker'>Puesto" . $puesto_req_tex . "</label>
                  <select class='form-control' name='puesto'" . $puesto_req . ">
                    <option " . $puesto_disabled . " selected value=''>Seleccione una opci&oacute;n</option>";
      Query::listarPuestos($conn);
      echo "
                  </select>
                </div>
              </div>";
    }
  }

  public static function cargarUbicacion($conn, $encuesta) {
    $incluye_ubicacion = $encuesta["incluye_ubicacion"] == 1;
    $ubicacion_req = $encuesta["ubicacion_oblig"] == 1 ? " required " : "";
    $ubicacion_req_tex = $encuesta["ubicacion_oblig"] == 1 ? "" : " (opcional)";

    if ($incluye_ubicacion) {
      echo "
              <div class='col-12 col-md-6'>
                <div class='form-group'>
                  <label class='field-kicker'>Provincia" . $ubicacion_req_tex . "</label>
                  <select class='form-control' onchange='cambiarProvincia(this)' id='provincia' name='provincia'" . $ubicacion_req . ">
                    <option disabled selected hidden value=''>Seleccione una provincia</option>";
      Query::listarProvincias($conn);
      echo "
                  </select>
                </div>
              </div>
              <div class='col-12 col-md-6'>
                <div class='form-group'>
                  <label class='field-kicker'>Cant&oacute;n" . $ubicacion_req_tex . "</label>
                  <select class='form-control' onchange='cambiarCanton(this)' id='canton' name='canton'" . $ubicacion_req . ">
                    <option disabled selected hidden value=''>Seleccione un cant&oacute;n</option>
                  </select>
                </div>
              </div>
              <div class='col-12 col-md-6'>
                <div class='form-group'>
                  <label class='field-kicker'>Distrito" . $ubicacion_req_tex . "</label>
                  <select class='form-control' id='distrito' name='distrito'" . $ubicacion_req . ">
                    <option disabled selected hidden value=''>Seleccione un distrito</option>
                  </select>
                </div>
              </div>";
    }
  }

  public static function cargarEstCivil($conn, $encuesta) {
    $incluye_est_civil = $encuesta["incluye_est_civil"] == 1;
    $est_civil_req = $encuesta["est_civil_oblig"] == 1 ? " required " : "";
    $est_civil_req_tex = $encuesta["est_civil_oblig"] == 1 ? "" : " (opcional)";
    $est_civil_disabled = $encuesta["est_civil_oblig"] == 1 ? " disabled hidden " : "";

    if ($incluye_est_civil) {
      echo "
              <div class='col-12 col-md-6'>
                <div class='form-group'>
                  <label class='field-kicker'>Estado civil" . $est_civil_req_tex . "</label>
                  <select class='form-control' name='est_civil'" . $est_civil_req . ">
                    <option " . $est_civil_disabled . " selected value=''>Seleccione una opci&oacute;n</option>";
      Query::listarEstCiviles($conn);
      echo "
                  </select>
                </div>
              </div>";
    }
  }

}

?>
