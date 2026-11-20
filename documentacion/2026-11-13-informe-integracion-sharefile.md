# Detalle de integración con ShareFile para soporte

Fecha: 13 de noviembre de 2026.

Este informe describe la implementación encontrada en el repositorio local. No se ejecutaron solicitudes contra ShareFile ni se verificó la configuración del servidor desplegado. Los apartados pendientes requieren evidencia del incidente real.

## 1. Configuración de la API

- Aplicativo: formularios y encuestas, desarrollado en PHP, con consumo de API mediante cURL.
- Cuenta/tenant configurado: `cajadeande.sharefile.com`.
- Usuario de integración configurado: `formularios_web_test2@cajadeande.fi.cr`.
- Autenticación: solicitud OAuth con `grant_type=password`, enviando `client_id`, `client_secret`, `username` y `password`.
- Endpoint de autenticación: `POST https://cajadeande.sharefile.com/oauth/token`.
- Tipo de contenido de autenticación: `application/x-www-form-urlencoded`.
- Las siguientes solicitudes de metadatos y creación de enlaces utilizan `Authorization: Bearer <access_token>`.
- Host de la API: se construye a partir del campo `subdomain` del token como `https://<subdomain>.sf-api.com`.
- Versión utilizada en las rutas: `/sf/v3`.
- Carpeta de destino: se obtiene de `folder_id` de la encuesta; debe identificarse el valor de la encuesta afectada.
- El helper de solicitudes configura un timeout de 30 segundos y seguimiento de redirecciones. El POST de contenido tiene una configuración cURL separada y no establece timeout explícito.
- En el helper están desactivadas las verificaciones TLS de certificado y host. En la carga del contenido se desactiva la verificación del certificado. Esto describe el código actual y no constituye una recomendación de configuración.

No se incluyen contraseñas, secretos de cliente ni tokens en este informe.

## 2. Secuencia implementada y resultado esperado

| Paso | Solicitud/acción | Resultado que espera el código |
| --- | --- | --- |
| 1 | El usuario adjunta un archivo y envía el formulario. | Validación del tamaño máximo configurado para la pregunta; 5 MB cuando no hay un valor específico. |
| 2 | `POST /oauth/token` al tenant. | HTTP 200 y un objeto con `access_token` y `subdomain`. |
| 3 | `GET /sf/v3/Items(<folder_id>)/Upload` al host de API. | HTTP 200 y configuración con `ChunkUri`. |
| 4 | `POST` multipart al `ChunkUri`, con campo `File1`. | HTTP 200. |
| 5 | `GET /sf/v3/Items(<folder_id>)/ByPath?path=/<nombre_archivo>`. | Objeto del archivo con `Id`. |
| 6 | `POST /sf/v3/Shares` con cuerpo JSON. | Objeto con `Uri`, que se devuelve al aplicativo. |
| 7 | Inserción en SQL Server. | El enlace queda en `respuesta_det.valor`, asociado a la respuesta y pregunta. |

Antes de cargarlo se transforma el nombre del archivo y se agrega el prefijo `<respuesta_id>_<encuesta_det_id>_<timestamp>_`.

Cuerpo enviado para crear el enlace:

```json
{
  "ShareType": "Send",
  "Items": [{ "Id": "<id_del_archivo>" }],
  "RequireLogin": false,
  "RequireUserInfo": false,
  "MaxDownloads": -1
}
```

El código solicita un enlace sin exigir inicio de sesión ni datos del visitante. No envía destinatarios específicos ni fecha de vencimiento explícita. En la base de datos se guarda la URL, sin una configuración de permisos adicional en esa inserción. Solicitamos confirmar qué políticas de la cuenta afectan a los enlaces creados con estos parámetros.

## 2.1. Qué hace el sitio antes de llamar a la API de ShareFile

Detalle agregado el 19 de noviembre de 2026, verificado contra el código local.

1. **Validación en el navegador.** Al enviar, el JavaScript revisa el tamaño de los archivos y la validez de los campos aplicables. Si hay un campo inválido, muestra el aviso y detiene el envío. Si pasan las validaciones, muestra el indicador de procesamiento y deshabilita el botón de envío.
2. **Envío al servidor del aplicativo.** El navegador envía los campos y adjuntos por `POST multipart/form-data` a `envio.php?id=<encuesta_id>&accion=crear`. Esta solicitud va al aplicativo PHP; no es una llamada directa del navegador a ShareFile. PHP recibe los campos en `$_POST` y los archivos en `$_FILES`, incluyendo la ruta temporal del archivo recibido en el servidor.
3. **Control del tamaño total.** `envio.php` contempla el caso de un cuerpo enviado que supera `post_max_size`: si hay contenido pero `$_POST` está vacío, muestra un error y no invoca el proceso de creación.
4. **Inicio del procesamiento.** `Procesos::crear_respuesta` inicia una transacción en SQL Server, carga y valida la configuración de la encuesta, procesa los datos recibidos y verifica los campos obligatorios según esa configuración.
5. **Validación condicional del accionista.** Si la encuesta incluye cédula y esta es obligatoria, consulta el servicio de validación de cliente activo. Si el resultado no es satisfactorio, detiene el proceso antes de ShareFile.
6. **Validación de respuestas y adjuntos obligatorios.** Recorre los campos de preguntas recibidos y consulta su configuración. Para preguntas de archivo obligatorias comprueba la presencia en `$_FILES`, tamaño mayor que cero y tamaño máximo permitido. También comprueba respuestas requeridas y que se hayan recibido preguntas.
7. **Creación del encabezado en la base de datos.** Inserta el encabezado de la respuesta y obtiene `respuesta_id`. La transacción aún no está confirmada; el enlace todavía no se ha generado ni guardado. Según el orden de las preguntas, pueden haberse insertado detalles previos antes de llegar al adjunto.
8. **Preparación de los argumentos de carga.** Al procesar una pregunta de archivo, vuelve a verificar el tamaño máximo (con valor por defecto de 5 MB en esta etapa), toma el MIME informado en `$_FILES`, la ruta temporal, el tamaño y el nombre original. Si el tamaño es mayor que cero, invoca `subir_archivo_citrix` con esos datos, el `folder_id` de la encuesta, `respuesta_id` y el identificador de la pregunta.
9. **Primera llamada a ShareFile.** Al entrar en `subir_archivo_citrix`, la primera operación de red hacia ShareFile es la autenticación: `POST https://cajadeande.sharefile.com/oauth/token`, realizada por PHP/cURL con la cuenta de integración configurada.

Precisión sobre el orden: la limpieza del nombre y el prefijo `<respuesta_id>_<encuesta_det_id>_<timestamp>_` se aplican después de solicitar el token y antes de solicitar la configuración de carga en `/Items(<folder_id>)/Upload`.

En este flujo no se autentica ante ShareFile al usuario que más tarde abrirá el enlace ni se consultan sus permisos sobre la carpeta. Se utiliza la cuenta de integración del servidor. La creación del enlace mediante `/Shares` y su posterior guardado ocurren después de estas operaciones.

Referencias: `encuestas/index.php:63`, `encuestas/index.php:190`, `encuestas/index.php:256`, `encuestas/envio.php:17`, `encuestas/clases/Procesos.php:50`, `encuestas/clases/Procesos.php:165`, `encuestas/clases/Procesos.php:184`, `encuestas/clases/Procesos.php:233`, `encuestas/clases/Procesos.php:299` y `encuestas/clases/Multimedia.php:3`.

## 3. Dónde falla: apertura del enlace guardado

Según lo reportado por el usuario, el fallo ocurre al abrir el enlace que ya se encuentra guardado en la base de datos. El usuario afectado tiene permisos sobre la carpeta de ShareFile, pero no puede visualizar el documento mediante ese enlace. No se reporta un fallo al adjuntar o guardar el formulario.

El valor guardado procede del campo `Uri` de la respuesta de `POST /sf/v3/Shares`. Es un enlace compartido generado por la API. El aplicativo no construye una ruta de navegación a la carpeta ni incorpora al enlace los permisos del usuario que posteriormente lo abre.

El usuario confirmó que la misma cuenta sí puede ingresar a ShareFile, abrir la carpeta y visualizar los documentos. El fallo se presenta al intentar acceder directamente mediante la URL guardada en la base de datos. Por lo tanto, el comportamiento reportado se concentra en el acceso mediante ese enlace; no se reporta una falta general de permisos para consultar la carpeta y sus documentos.

La causa técnica sigue pendiente de confirmar: no se dispone del mensaje exacto de ShareFile, estado HTTP de la apertura, capturas ni propiedades efectivas del enlace. Debe verificarse también que la URL almacenada y la URL abierta coincidan íntegramente con la `Uri` devuelta por la API.

Los permisos de carpeta y las condiciones efectivas del enlace deben revisarse con el proveedor; la configuración enviada por el aplicativo no demuestra por sí sola qué políticas aplicó ShareFile al enlace creado.

Limitaciones del diagnóstico actual:

- Si falla la autenticación, se devuelve `null` sin conservar un diagnóstico específico para el usuario.
- El helper devuelve estado HTTP, error cURL y cuerpo decodificado, pero el flujo no los registra de forma sistemática por etapa.
- Al solicitar la configuración de carga, el mensaje de error incluye el error cURL, sin mostrar el cuerpo de error de la API ni el estado HTTP.
- No hay validación explícita del estado HTTP al consultar `ByPath` ni al crear el enlace en `/Shares`.
- La función externa captura excepciones sin registrarlas.
- Se observan dos llamadas consecutivas a `curl_exec` para el mismo POST de contenido. El estado HTTP consultado corresponde a la segunda ejecución. Es un hallazgo del código a revisar, no una causa confirmada del incidente.

## 4. Pasos para documentar el resultado real

1. Registrar la URL y el nombre del formulario afectado, el ambiente y la fecha/hora con zona horaria.
2. Usar un archivo de prueba sin datos personales y registrar nombre, extensión y tamaño.
3. Adjuntar el archivo y enviar el formulario.
4. Identificar el enlace guardado en `respuesta_det.valor` y compararlo con la `Uri` original, si se dispone de la respuesta de la API. Confirmar que no esté truncado ni alterado al presentarlo o abrirlo.
5. Con el usuario afectado, que según lo reportado tiene permisos sobre la carpeta, abrir ese enlace. Registrar el mensaje exacto, las redirecciones y la fecha/hora del fallo.
6. Documentar el acceso satisfactorio con la misma cuenta al navegar directamente a la carpeta y visualizar los documentos en ShareFile, comportamiento ya confirmado por el usuario. Para la evidencia del caso, identificar el mismo archivo al que corresponde el enlace afectado.
7. Obtener las propiedades efectivas del enlace en ShareFile: vigencia, estado, archivo asociado y restricciones de acceso. Compararlas con los parámetros enviados al crearlo.
8. Recopilar la respuesta HTTP de la apertura del enlace y cualquier identificador de correlación. Si hace falta revisar la creación, obtener del servidor PHP la respuesta de `POST /sf/v3/Shares`; esa llamada no aparece como solicitud directa a ShareFile en el navegador del usuario.

Datos del incidente por completar:

- Ambiente / URL del formulario:
- Encuesta y `folder_id` efectivos:
- Fecha y hora del intento (Costa Rica, UTC-6):
- Archivo de prueba y tamaño:
- Acción exacta que falla: abrir el enlace guardado en la base de datos con un usuario que tiene permisos sobre la carpeta.
- Mensaje observado:
- URL final / estado HTTP de la apertura / respuesta de error:
- Resultado esperado: visualizar el documento al abrir el enlace guardado.
- Resultado obtenido: el usuario con permisos sobre la carpeta no puede visualizar el documento mediante el enlace, según lo reportado.
- Acceso desde ShareFile: el usuario confirma que puede abrir la carpeta y visualizar los documentos con la misma cuenta. Para la evidencia del caso, identificar el archivo concreto asociado al enlace afectado.

## 5. Capturas y evidencia para adjuntar

Pendientes; no se dispone de capturas reales en el repositorio.

- Captura de la pantalla que aparece al abrir el enlace guardado, con el mensaje completo.
- Captura de los permisos del usuario afectado sobre la carpeta, ocultando datos de otras personas.
- Evidencia del resultado de abrir el mismo documento navegando directamente por la carpeta con la misma cuenta.
- Propiedades del enlace y respuesta HTTP de la apertura, si están disponibles.

Ocultar contraseñas, `client_secret`, tokens, encabezados de autorización y datos personales antes de compartir evidencia. Evitar publicar enlaces completos que den acceso a documentos.

## 6. Consultas al proveedor

El incidente reportado ocurre al abrir el enlace compartido guardado en la base de datos: un usuario con permisos sobre la carpeta no logra visualizar el documento desde ese enlace. La misma cuenta sí puede ingresar directamente a ShareFile, abrir la carpeta y visualizar los documentos. Solicitamos:

- Revisar el estado y las propiedades efectivas del enlace afectado, su vigencia y el archivo asociado.
- Confirmar si alguna política del tenant restringe los enlaces `Send` creados con `RequireLogin=false`, `RequireUserInfo=false` y `MaxDownloads=-1`.
- Explicar cómo se evalúan las restricciones de ese enlace respecto de los permisos que el usuario ya tiene sobre la carpeta.
- Confirmar qué configuración de enlace debe usar la API para permitir el acceso esperado a los usuarios autorizados.
- Con el mensaje y la hora del incidente, identificar en sus registros la razón concreta por la que se rechaza o no se completa la apertura.

## Referencias del repositorio

- `encuestas/config.php`: tenant, cuenta de integración y credenciales configuradas.
- `encuestas/clases/Multimedia.php`: autenticación, carga y creación del enlace.
- `encuestas/clases/Procesos.php`: validación del archivo e invocación de la carga antes del guardado.
- `encuestas/clases/RespuestaDet.php`: inserción del enlace en `respuesta_det.valor`.

Nota para la recolección interna: `encuestas/prueba_files.php` imprime el token de acceso y llama a la función de carga con menos argumentos de los que exige su firma actual. No utilizar esa página tal como está para producir evidencia compartible del flujo vigente.
