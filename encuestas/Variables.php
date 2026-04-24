<?php

// tipos de enunciados para las encuestas
define("EDT_NUMERO",        1);
define("EDT_MONTO",         2);
define("EDT_CORREO",        3);
define("EDT_FECHA",         4);
define("EDT_TEXTO_C",       5);   // texto corto (textbox)
define("EDT_TEXTO_L",       6);   // texto largo (textarea)
define("EDT_SEL_UNI_COM",   7);   // combobox
define("EDT_SEL_UNI_RAD",   8);   // radiobuttons
define("EDT_SEL_MUL",       9);
define("EDT_RATING_PTO",    10);  // rating con puntos (radiobuttons)
define("EDT_RATING_BAR",    11);  // rating con range de html
define("EDT_ARCHIVO",       12);
define("EDT_LOCATION",      13);
define("EDT_TEXTO_SEP",     14);  // texto separador para simular una agrupacion
define("EDT_BOOLEANO",      15);
define("EDT_SUBTITULO",     16);
define("EDT_PARRAFO",       17);
define("EDT_PROVINCIA",     18);
define("EDT_RATING_CUA",    19);  // rating con cuadrados
define("EDT_DIRECCION_ENV", 20);  // direccion de envio (sucursales o domicilio)
define("EDT_LINK_EXT",      21);  // link externo
define("EDT_PUESTO_CAJA",   22);  
// tipos de enunciados para las encuestas

// valores de meta para los enunciados de las preguntas
define("META_MIN", 			      "min");
define("META_MAX", 			      "max");
define("META_STEP",           "step");
define("META_FILE_SIZE",      "max_size_mb");
define("META_INC_DOMICILIO",  "inc_domicilio");
define("META_URL",            "url");
// valores de meta para los enunciados de las preguntas

define("ENCUESTA_PRUEBA", 40);

?>