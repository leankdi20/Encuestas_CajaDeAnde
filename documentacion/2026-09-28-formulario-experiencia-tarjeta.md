# Formulario Encuesta Experiencia Tarjeta

Fecha: 2026-09-28

## Resumen

Se documenta la creacion del formulario **Encuesta Experiencia** y los ajustes realizados en el formulario web para controlar la captura del nombre por medio de la cedula.

## Cambios en el formulario web

Archivos modificados:

- `encuestas/clases/QueryEncuesta.php`
- `encuestas/index.php`

Cambios aplicados:

- El campo **Nombre completo** queda como solo lectura (`readonly`).
- El usuario no puede escribir manualmente el nombre.
- El nombre se completa unicamente mediante la consulta automatica por cedula.
- Si la cedula no devuelve datos, el nombre queda vacio y bloqueado.
- Se elimino el mensaje que indicaba que el usuario podia completar el nombre manualmente.
- La etiqueta del campo cedula cambio de **formato 9 digitos** a **formato 10 digitos**.

## Motivo del cambio

El nombre del accionista debe provenir directamente del servicio de consulta por cedula, para evitar que el usuario escriba un nombre distinto al registrado.

## Comportamiento esperado

1. El usuario digita la cedula.
2. El formulario consulta `ajax.php?accion=leerSocio&cedula=...`.
3. Si encuentra el accionista, completa automaticamente el campo **Nombre completo**.
4. Si no encuentra el accionista o falla el servicio, el campo **Nombre completo** permanece bloqueado.

## Script de base de datos relacionado

Archivo relacionado:

- `crear_encuesta_experiencia_tarjeta.sql`

Ese script crea la encuesta **Encuesta Experiencia** con:

- Datos iniciales: nombre, descripcion, fechas, campos personales requeridos y mensaje de exito.
- Bloque **Solicitud y gestion**.
- Bloque **Momento de entrega**.
- Bloque **Claridad y beneficios**.
- Bloque **Intencion de uso y recomendacion**.
- Bloque **Comentarios finales**.

## Preguntas con leyenda 0 a 10

Las siguientes preguntas se crearon como tipo `10` y deben mostrar la leyenda:

```text
0 Nada probable -- 10 Totalmente probable
```

Preguntas:

- `Que tan probable es que utilice esta tarjeta frecuentemente en sus compras?`
- `Considerando el proceso de solicitud, la entrega y lo que conoce hasta ahora de sus beneficios, que tan probable es que recomiende a otra persona accionista solicitar una tarjeta de credito de Caja de ANDE?`

En el script se incluyen dentro del `enunciado` con HTML:

```html
<br><small>0 Nada probable &mdash; 10 Totalmente probable</small>
```

## Parametros importantes del script

Antes de ejecutar en otro ambiente, revisar:

- `@fecha_inicio`
- `@fecha_fin`
- `@correos`
- `@mensaje_exito`
- `@usuario_id`
- `nombre_csv`

Valores principales usados:

```sql
DECLARE @fecha_inicio DATE = CONVERT(DATE, GETDATE());
DECLARE @fecha_fin DATE = NULL;
DECLARE @correos VARCHAR(MAX) = NULL;
DECLARE @mensaje_exito VARCHAR(MAX) = '¡Muchas gracias por compartir su experiencia!';
DECLARE @usuario_id INT = 1;
```

Nombre de encuesta:

```sql
'Encuesta Experiencia'
```

Descripcion:

```sql
'Desde que solicitó su tarjeta de crédito hasta el momento de recibirla, su opinión nos ayuda a mejorar nuestros productos y servicios. Complete esta breve encuesta y participe en el sorteo de ₡100.000 en efectivo. Tiempo estimado: 4 a 5 minutos.'
```

Nombre CSV:

```sql
'experiencia_tarjeta_credito'
```

## Validaciones recomendadas despues de ejecutar

Consultar el id creado:

```sql
SELECT encuesta_id, nombre, descripcion, activo, nombre_csv
FROM dbo.encuestas
WHERE nombre_csv = 'experiencia_tarjeta_credito';
```

Validar preguntas creadas:

```sql
SELECT encuesta_det_id, encuesta_id, tipo_id, orden, enunciado
FROM dbo.encuesta_det
WHERE encuesta_id = @encuesta_id
ORDER BY orden;
```

Validar opciones:

```sql
SELECT ed.encuesta_det_id, ed.enunciado, edo.texto, edo.orden, edo.activo
FROM dbo.encuesta_det ed
INNER JOIN dbo.encuesta_det_opciones edo
    ON edo.encuesta_det_id = ed.encuesta_det_id
WHERE ed.encuesta_id = @encuesta_id
ORDER BY ed.orden, edo.orden;
```

Validar rangos de preguntas 0 a 10:

```sql
SELECT ed.encuesta_det_id, ed.enunciado, edm.llave, edm.valor
FROM dbo.encuesta_det ed
INNER JOIN dbo.encuesta_det_meta edm
    ON edm.encuesta_det_id = ed.encuesta_det_id
WHERE ed.encuesta_id = @encuesta_id
  AND ed.tipo_id = 10
ORDER BY ed.orden, edm.llave;
```

## Cambio de endpoint para consulta de accionistas

Fecha del cambio: 2026-09-30

Archivos modificados:

- `encuestas/config.php`
- `encuestas/clases/WebServices.php`

### Motivo

El ambiente de desarrollo no tenia conectividad al servicio anterior:

```text
http://172.16.8.226/WS_PROCOBA/
```

Se habilito un endpoint de desarrollo alternativo para consultar la informacion del accionista desde CORE:

```text
http://desawscore2doambiente:8787/ServiciosCORE/ObtenerInformacionClienteCORE
```

### Cambio en config.php

La variable de sesion `servidor_api` ahora apunta al endpoint CORE de desarrollo:

```php
$_SESSION["servidor_api"] = "http://desawscore2doambiente:8787/ServiciosCORE/ObtenerInformacionClienteCORE";
```

Para produccion, si el endpoint productivo usa el mismo formato de request y response, solo se debe cambiar esa URL en `config.php`.

### Cambio en WebServices.php

El servicio anterior usaba SOAP/XML. El nuevo endpoint CORE usa POST con JSON, por eso no bastaba con cambiar la URL.

Se agrego soporte para detectar cuando `servidor_api` apunta a `ObtenerInformacionClienteCORE` y, en ese caso, enviar:

```json
{
  "cedula": "0281178048"
}
```

El endpoint responde con:

```json
{
  "codResultado": 1,
  "nombre": "COLUNGA QUINTERO ZILLA MENTOR",
  "correo": "omnicanal@bdconsultores.com",
  "activo": 1
}
```

Los metodos afectados son:

- `nombrePorCedula`
- `correoPorCedula`
- `clienteActivoPorCedula`
- `clienteActivoPorCedulaObj`

Tambien se agrego cache interno por cedula dentro del mismo request para evitar llamar dos veces al endpoint cuando se solicita nombre y correo.

### Pruebas realizadas

Conectividad:

```powershell
Test-NetConnection desawscore2doambiente -Port 8787
```

Resultado esperado:

```text
TcpTestSucceeded: True
```

Prueba POST:

```powershell
Invoke-WebRequest "http://desawscore2doambiente:8787/ServiciosCORE/ObtenerInformacionClienteCORE" -Method POST -ContentType "application/json" -Body '{"cedula":"0281178048"}' -UseBasicParsing
```

Resultado obtenido:

```json
{"codResultado":1,"nombre":"COLUNGA QUINTERO ZILLA MENTOR","correo":"omnicanal@bdconsultores.com","activo":1}
```

Prueba desde el formulario:

```text
http://192.168.1.101:8005/ajax.php?accion=leerSocio&cedula=0281178048
```

Respuesta esperada:

```json
{"nombre":"COLUNGA QUINTERO ZILLA MENTOR","correo":"omnicanal@bdconsultores.com"}
```

## Tipo de socio en validacion de cedula

Fecha del cambio: 2026-10-02

Archivos modificados:

- `encuestas/ajax.php`
- `encuestas/clases/WebServices.php`
- `encuestas/clases/QueryEncuesta.php`
- `encuestas/index.php`

### Motivo

El endpoint CORE devuelve informacion adicional del tipo de socio:

```json
{
  "codResultado": 1,
  "nombre": "BACA MOLINA ZILLA NELDA",
  "correo": "omnicanal@bdconsultores.com",
  "activo": 1,
  "idTipoSocio": 25,
  "desTipoSocio": "PENSIONADO CCSS"
}
```

Se requiere que al validar la cedula tambien se muestre y quede disponible la descripcion del tipo de socio.

### Cambio aplicado

Se agrego el metodo `WebServices::informacionSocioPorCedula($cedula)` para devolver una respuesta normalizada con:

```json
{
  "nombre": "",
  "correo": "",
  "activo": false,
  "idTipoSocio": null,
  "desTipoSocio": ""
}
```

Cuando el endpoint CORE responde correctamente y el socio esta activo, se completan `nombre`, `correo`, `idTipoSocio` y `desTipoSocio`.

`ajax.php?accion=leerSocio&cedula=...` ahora devuelve tambien:

- `activo`
- `idTipoSocio`
- `desTipoSocio`

En el formulario:

- Se muestra debajo del nombre el texto `Tipo de socio: ...`.
- Se agregaron inputs ocultos para consumir el dato desde el frontend:

```html
<input type="hidden" id="idTipoSocio" name="idTipoSocio">
<input type="hidden" id="desTipoSocio" name="desTipoSocio">
```

### Prueba esperada

Al consultar:

```text
ajax.php?accion=leerSocio&cedula=0281178048
```

La respuesta debe incluir:

```json
{
  "nombre": "COLUNGA QUINTERO ZILLA MENTOR",
  "correo": "omnicanal@bdconsultores.com",
  "activo": true,
  "idTipoSocio": 10,
  "desTipoSocio": "ACTIVO"
}
```

## Notas

- El script usa transaccion con `SET XACT_ABORT ON`, `BEGIN TRY`, `BEGIN TRANSACTION`, `COMMIT` y `ROLLBACK` en caso de error.
- El campo **Nombre completo** en el formulario depende de que el servicio de cedula responda correctamente.
- Para ambientes de desarrollo, validar conectividad al endpoint CORE antes de probar la busqueda por cedula.
