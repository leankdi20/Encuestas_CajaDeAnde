<?php

interface MensajeExitoEncuesta {
  public function obtenerMensaje($encuesta);
}

class MensajeExitoDefault implements MensajeExitoEncuesta {
  public function obtenerMensaje($encuesta) {
    return $encuesta["mensaje_exito"] ?? "La informaci&oacute;n se ha enviado exitosamente.";
  }
}

class MensajeExitoEncuesta48 implements MensajeExitoEncuesta {
  public function obtenerMensaje($encuesta) {
    return "Se recibió su gestión, la misma será resuelta en los próximos 10 días hábiles, 
    en caso de exceder dicho plazo le será comunicado por parte de la Oficina de la Contraloría de Servicios,
    al correo que usted tiene registrado en nuestro sistema.";
  }
}

class MensajeExitoEncuestaFactory {
  public static function crear($encuesta_id) {
    switch ((int)$encuesta_id) {
      case 48:
        return new MensajeExitoEncuesta48();
      default:
        return new MensajeExitoDefault();
    }
  }
}

?>
