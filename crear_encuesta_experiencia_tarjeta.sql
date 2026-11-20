/*
  Script base para crear la encuesta:
  "Queremos conocer su experiencia"

  Antes de ejecutar:
  - Ajustar fechas, correos y campos de datos iniciales.
  - Confirmar si la tabla encuestas tiene todos estos campos en ambiente destino.
  - Ejecutar primero en QA/desarrollo.
*/

SET XACT_ABORT ON;

BEGIN TRY
BEGIN TRANSACTION;

DECLARE @encuesta_id INT;
DECLARE @det_id INT;

DECLARE @fecha_inicio DATE = CONVERT(DATE, GETDATE());
DECLARE @fecha_fin DATE = '2026-12-31';
DECLARE @correos VARCHAR(MAX) = NULL;
DECLARE @mensaje_exito VARCHAR(MAX) = '¡Muchas gracias por compartir su experiencia!';
DECLARE @usuario_id INT = 1; -- Ajustar por el usuario real que crea la encuesta.

INSERT INTO dbo.encuestas (
    nombre,
    descripcion,
    fecha_inicio,
    fecha_fin,
    incluye_nombre,
    nombre_oblig,
    incluye_cedula,
    cedula_oblig,
    incluye_sucursal,
    sucursal_oblig,
    incluye_unidad,
    unidad_oblig,
    incluye_agente,
    agente_oblig,
    incluye_gestion,
    gestion_oblig,
    incluye_fecha_nac,
    fecha_nac_oblig,
    incluye_estado_emp,
    estado_emp_oblig,
    incluye_genero,
    genero_oblig,
    incluye_ubicacion,
    ubicacion_oblig,
    incluye_puesto,
    puesto_oblig,
    incluye_correo,
    correo_oblig,
    incluye_telefono,
    telefono_oblig,
    incluye_est_civil,
    est_civil_oblig,
    correos,
    mensaje_exito,
    mensaje_bloqueo,
    mostrar_num_pregunta,
    folder_id,
    nombre_csv,
    activo,
    creado_por,
    actualizado_por
)
VALUES (
    'Queremos conocer su experiencia',
    'Desde que solicitó su tarjeta de crédito hasta el momento de recibirla, su opinión nos ayuda a mejorar nuestros productos y servicios. Complete esta breve encuesta y participe en el sorteo de ₡100.000 en efectivo. Tiempo estimado: 4 a 5 minutos.',
    @fecha_inicio,
    @fecha_fin,
    1, 1,
    1, 1,
    0, 0,
    0, 0,
    0, 0,
    0, 0,
    0, 0,
    0, 0,
    0, 0,
    0, 0,
    0, 0,
    1, 0,
    1, 0,
    0, 0,
    @correos,
    @mensaje_exito,
    NULL,
    1,
    NULL,
    'experiencia_tarjeta_credito',
    1,
    @usuario_id,
    @usuario_id
);

SET @encuesta_id = SCOPE_IDENTITY();

/* Bloque: Solicitud y gestión */
INSERT INTO encuesta_det (encuesta_id, tipo_id, enunciado, texto_descripcion, placeholder, opcional, orden, dep_encuesta_det_id, dep_encuesta_det_opcion_id)
VALUES (@encuesta_id, 16, 'SOLICITUD Y GESTIÓN', NULL, NULL, 1, 10, NULL, NULL);

INSERT INTO encuesta_det (encuesta_id, tipo_id, enunciado, texto_descripcion, placeholder, opcional, orden, dep_encuesta_det_id, dep_encuesta_det_opcion_id)
VALUES (@encuesta_id, 8, 'Pensando en el proceso para obtener su tarjeta de crédito, ¿qué tan fácil o difícil le resultó realizar la solicitud?', NULL, NULL, 0, 20, NULL, NULL);
SET @det_id = SCOPE_IDENTITY();
INSERT INTO encuesta_det_opciones (encuesta_det_id, texto, otro, explicacion, bloquea_otras, correos, seleccionada, orden, activo) VALUES
(@det_id, 'Muy difícil.', 0, 0, 0, NULL, 0, 1, 1),
(@det_id, 'Difícil.', 0, 0, 0, NULL, 0, 2, 1),
(@det_id, 'Ni fácil ni difícil.', 0, 0, 0, NULL, 0, 3, 1),
(@det_id, 'Fácil.', 0, 0, 0, NULL, 0, 4, 1),
(@det_id, 'Muy fácil.', 0, 0, 0, NULL, 0, 5, 1);

INSERT INTO encuesta_det (encuesta_id, tipo_id, enunciado, texto_descripcion, placeholder, opcional, orden, dep_encuesta_det_id, dep_encuesta_det_opcion_id)
VALUES (@encuesta_id, 8, '¿Qué tan satisfecho(a) quedó con la gestión de su solicitud?', NULL, NULL, 0, 30, NULL, NULL);
SET @det_id = SCOPE_IDENTITY();
INSERT INTO encuesta_det_opciones (encuesta_det_id, texto, otro, explicacion, bloquea_otras, correos, seleccionada, orden, activo) VALUES
(@det_id, 'Muy insatisfecho(a).', 0, 0, 0, NULL, 0, 1, 1),
(@det_id, 'Insatisfecho(a).', 0, 0, 0, NULL, 0, 2, 1),
(@det_id, 'Ni satisfecho(a) ni insatisfecho(a).', 0, 0, 0, NULL, 0, 3, 1),
(@det_id, 'Satisfecho(a).', 0, 0, 0, NULL, 0, 4, 1),
(@det_id, 'Muy satisfecho(a).', 0, 0, 0, NULL, 0, 5, 1);

INSERT INTO encuesta_det (encuesta_id, tipo_id, enunciado, texto_descripcion, placeholder, opcional, orden, dep_encuesta_det_id, dep_encuesta_det_opcion_id)
VALUES (@encuesta_id, 8, 'Durante el proceso, ¿qué tan clara fue la información que recibió sobre el estado y los pasos de su solicitud?', NULL, NULL, 0, 40, NULL, NULL);
SET @det_id = SCOPE_IDENTITY();
INSERT INTO encuesta_det_opciones (encuesta_det_id, texto, otro, explicacion, bloquea_otras, correos, seleccionada, orden, activo) VALUES
(@det_id, 'Nada clara.', 0, 0, 0, NULL, 0, 1, 1),
(@det_id, 'Poco clara.', 0, 0, 0, NULL, 0, 2, 1),
(@det_id, 'Medianamente clara.', 0, 0, 0, NULL, 0, 3, 1),
(@det_id, 'Muy clara.', 0, 0, 0, NULL, 0, 4, 1),
(@det_id, 'Totalmente clara.', 0, 0, 0, NULL, 0, 5, 1);

INSERT INTO encuesta_det (encuesta_id, tipo_id, enunciado, texto_descripcion, placeholder, opcional, orden, dep_encuesta_det_id, dep_encuesta_det_opcion_id)
VALUES (@encuesta_id, 8, '¿Cómo calificaría el tiempo transcurrido desde su solicitud hasta recibir la tarjeta?', 'Selección única.', NULL, 0, 50, NULL, NULL);
SET @det_id = SCOPE_IDENTITY();
INSERT INTO encuesta_det_opciones (encuesta_det_id, texto, otro, explicacion, bloquea_otras, correos, seleccionada, orden, activo) VALUES
(@det_id, 'Mucho más rápido de lo esperado', 0, 0, 0, NULL, 0, 1, 1),
(@det_id, 'Más rápido de lo esperado', 0, 0, 0, NULL, 0, 2, 1),
(@det_id, 'Dentro de lo esperado', 0, 0, 0, NULL, 0, 3, 1),
(@det_id, 'Más lento de lo esperado', 0, 0, 0, NULL, 0, 4, 1),
(@det_id, 'Mucho más lento de lo esperado', 0, 0, 0, NULL, 0, 5, 1);

INSERT INTO encuesta_det (encuesta_id, tipo_id, enunciado, texto_descripcion, placeholder, opcional, orden, dep_encuesta_det_id, dep_encuesta_det_opcion_id)
VALUES (@encuesta_id, 8, '¿Recibió su tarjeta en el plazo que le habían informado?', NULL, NULL, 0, 60, NULL, NULL);
SET @det_id = SCOPE_IDENTITY();
INSERT INTO encuesta_det_opciones (encuesta_det_id, texto, otro, explicacion, bloquea_otras, correos, seleccionada, orden, activo) VALUES
(@det_id, 'Sí', 0, 0, 0, NULL, 0, 1, 1),
(@det_id, 'No', 0, 0, 0, NULL, 0, 2, 1),
(@det_id, 'No me informaron un plazo', 0, 0, 0, NULL, 0, 3, 1);

/* Bloque: Momento de entrega */
INSERT INTO encuesta_det (encuesta_id, tipo_id, enunciado, texto_descripcion, placeholder, opcional, orden, dep_encuesta_det_id, dep_encuesta_det_opcion_id)
VALUES (@encuesta_id, 16, 'MOMENTO DE ENTREGA', NULL, NULL, 1, 70, NULL, NULL);

INSERT INTO encuesta_det (encuesta_id, tipo_id, enunciado, texto_descripcion, placeholder, opcional, orden, dep_encuesta_det_id, dep_encuesta_det_opcion_id)
VALUES (@encuesta_id, 8, 'En general, ¿cómo califica la experiencia al recibir su nueva tarjeta?', NULL, NULL, 0, 80, NULL, NULL);
SET @det_id = SCOPE_IDENTITY();
INSERT INTO encuesta_det_opciones (encuesta_det_id, texto, otro, explicacion, bloquea_otras, correos, seleccionada, orden, activo) VALUES
(@det_id, 'Muy mala.', 0, 0, 0, NULL, 0, 1, 1),
(@det_id, 'Mala.', 0, 0, 0, NULL, 0, 2, 1),
(@det_id, 'Regular.', 0, 0, 0, NULL, 0, 3, 1),
(@det_id, 'Buena.', 0, 0, 0, NULL, 0, 4, 1),
(@det_id, 'Excelente.', 0, 0, 0, NULL, 0, 5, 1);

INSERT INTO encuesta_det (encuesta_id, tipo_id, enunciado, texto_descripcion, placeholder, opcional, orden, dep_encuesta_det_id, dep_encuesta_det_opcion_id)
VALUES (@encuesta_id, 9, 'Al recibir su tarjeta, ¿cuáles de las siguientes sensaciones describen mejor su experiencia?', 'Seleccione hasta tres opciones.', NULL, 0, 90, NULL, NULL);
SET @det_id = SCOPE_IDENTITY();
INSERT INTO encuesta_det_opciones (encuesta_det_id, texto, otro, explicacion, bloquea_otras, correos, seleccionada, orden, activo) VALUES
(@det_id, 'Me sentí valorado(a) como accionista.', 0, 0, 0, NULL, 0, 1, 1),
(@det_id, 'Exclusividad.', 0, 0, 0, NULL, 0, 2, 1),
(@det_id, 'Confianza.', 0, 0, 0, NULL, 0, 3, 1),
(@det_id, 'Seguridad.', 0, 0, 0, NULL, 0, 4, 1),
(@det_id, 'Satisfacción.', 0, 0, 0, NULL, 0, 5, 1),
(@det_id, 'Entusiasmo.', 0, 0, 0, NULL, 0, 6, 1),
(@det_id, 'Cercanía con Caja de ANDE.', 0, 0, 0, NULL, 0, 7, 1),
(@det_id, 'Indiferencia.', 0, 0, 0, NULL, 0, 8, 1),
(@det_id, 'Confusión.', 0, 0, 0, NULL, 0, 9, 1),
(@det_id, 'Otra', 1, 0, 0, NULL, 0, 10, 1);

INSERT INTO encuesta_det (encuesta_id, tipo_id, enunciado, texto_descripcion, placeholder, opcional, orden, dep_encuesta_det_id, dep_encuesta_det_opcion_id)
VALUES (@encuesta_id, 8, '¿En qué medida la forma en que recibió la tarjeta le hizo sentir que se trataba de un producto especial para las personas accionistas?', NULL, NULL, 0, 100, NULL, NULL);
SET @det_id = SCOPE_IDENTITY();
INSERT INTO encuesta_det_opciones (encuesta_det_id, texto, otro, explicacion, bloquea_otras, correos, seleccionada, orden, activo) VALUES
(@det_id, 'Nada.', 0, 0, 0, NULL, 0, 1, 1),
(@det_id, 'Poco.', 0, 0, 0, NULL, 0, 2, 1),
(@det_id, 'Moderadamente.', 0, 0, 0, NULL, 0, 3, 1),
(@det_id, 'Bastante.', 0, 0, 0, NULL, 0, 4, 1),
(@det_id, 'Mucho.', 0, 0, 0, NULL, 0, 5, 1);

/* Bloque: Claridad y beneficios */
INSERT INTO encuesta_det (encuesta_id, tipo_id, enunciado, texto_descripcion, placeholder, opcional, orden, dep_encuesta_det_id, dep_encuesta_det_opcion_id)
VALUES (@encuesta_id, 16, 'CLARIDAD Y BENEFICIOS', NULL, NULL, 1, 110, NULL, NULL);

INSERT INTO encuesta_det (encuesta_id, tipo_id, enunciado, texto_descripcion, placeholder, opcional, orden, dep_encuesta_det_id, dep_encuesta_det_opcion_id)
VALUES (@encuesta_id, 8, 'Después de recibir la información de su tarjeta, ¿qué tan claros le quedaron sus principales beneficios?', NULL, NULL, 0, 120, NULL, NULL);
SET @det_id = SCOPE_IDENTITY();
INSERT INTO encuesta_det_opciones (encuesta_det_id, texto, otro, explicacion, bloquea_otras, correos, seleccionada, orden, activo) VALUES
(@det_id, 'Nada claros.', 0, 0, 0, NULL, 0, 1, 1),
(@det_id, 'Poco claros.', 0, 0, 0, NULL, 0, 2, 1),
(@det_id, 'Medianamente claros.', 0, 0, 0, NULL, 0, 3, 1),
(@det_id, 'Muy claros.', 0, 0, 0, NULL, 0, 4, 1),
(@det_id, 'Totalmente claros.', 0, 0, 0, NULL, 0, 5, 1);

INSERT INTO encuesta_det (encuesta_id, tipo_id, enunciado, texto_descripcion, placeholder, opcional, orden, dep_encuesta_det_id, dep_encuesta_det_opcion_id)
VALUES (@encuesta_id, 9, '¿Cuáles beneficios de su nueva tarjeta le resultan más atractivos?', 'Seleccione hasta tres opciones.', NULL, 0, 130, NULL, NULL);
SET @det_id = SCOPE_IDENTITY();
INSERT INTO encuesta_det_opciones (encuesta_det_id, texto, otro, explicacion, bloquea_otras, correos, seleccionada, orden, activo) VALUES
(@det_id, 'Tasa de interés preferencial', 0, 0, 0, NULL, 0, 1, 1),
(@det_id, 'Acumulación de puntos', 0, 0, 0, NULL, 0, 2, 1),
(@det_id, 'Descuentos en comercios afiliados', 0, 0, 0, NULL, 0, 3, 1),
(@det_id, 'Promociones especiales', 0, 0, 0, NULL, 0, 4, 1),
(@det_id, 'Beneficios en alimentación', 0, 0, 0, NULL, 0, 5, 1),
(@det_id, 'Beneficios en salud', 0, 0, 0, NULL, 0, 6, 1),
(@det_id, 'Beneficios en turismo', 0, 0, 0, NULL, 0, 7, 1),
(@det_id, 'Beneficios en educación', 0, 0, 0, NULL, 0, 8, 1),
(@det_id, 'Beneficios en entretenimiento', 0, 0, 0, NULL, 0, 9, 1),
(@det_id, 'Seguridad y respaldo', 0, 0, 0, NULL, 0, 10, 1),
(@det_id, 'Otro', 1, 0, 0, NULL, 0, 11, 1);

INSERT INTO encuesta_det (encuesta_id, tipo_id, enunciado, texto_descripcion, placeholder, opcional, orden, dep_encuesta_det_id, dep_encuesta_det_opcion_id)
VALUES (@encuesta_id, 8, 'En general, ¿qué tan atractivos considera los beneficios que ofrece su nueva tarjeta?', NULL, NULL, 0, 140, NULL, NULL);
SET @det_id = SCOPE_IDENTITY();
INSERT INTO encuesta_det_opciones (encuesta_det_id, texto, otro, explicacion, bloquea_otras, correos, seleccionada, orden, activo) VALUES
(@det_id, 'Nada atractivos.', 0, 0, 0, NULL, 0, 1, 1),
(@det_id, 'Poco atractivos.', 0, 0, 0, NULL, 0, 2, 1),
(@det_id, 'Medianamente atractivos.', 0, 0, 0, NULL, 0, 3, 1),
(@det_id, 'Atractivos.', 0, 0, 0, NULL, 0, 4, 1),
(@det_id, 'Muy atractivos.', 0, 0, 0, NULL, 0, 5, 1);

INSERT INTO encuesta_det (encuesta_id, tipo_id, enunciado, texto_descripcion, placeholder, opcional, orden, dep_encuesta_det_id, dep_encuesta_det_opcion_id)
VALUES (@encuesta_id, 8, 'Comparando lo que conoce de esta tarjeta con otras tarjetas de crédito, ¿cómo considera su propuesta de beneficios?', NULL, NULL, 0, 150, NULL, NULL);
SET @det_id = SCOPE_IDENTITY();
INSERT INTO encuesta_det_opciones (encuesta_det_id, texto, otro, explicacion, bloquea_otras, correos, seleccionada, orden, activo) VALUES
(@det_id, 'Mucho menos atractiva', 0, 0, 0, NULL, 0, 1, 1),
(@det_id, 'Menos atractiva', 0, 0, 0, NULL, 0, 2, 1),
(@det_id, 'Similar', 0, 0, 0, NULL, 0, 3, 1),
(@det_id, 'Más atractiva', 0, 0, 0, NULL, 0, 4, 1),
(@det_id, 'Mucho más atractiva', 0, 0, 0, NULL, 0, 5, 1),
(@det_id, 'No tengo suficiente información para comparar', 0, 0, 0, NULL, 0, 6, 1);

/* Bloque: Intención de uso y recomendación */
INSERT INTO encuesta_det (encuesta_id, tipo_id, enunciado, texto_descripcion, placeholder, opcional, orden, dep_encuesta_det_id, dep_encuesta_det_opcion_id)
VALUES (@encuesta_id, 16, 'INTENCIÓN DE USO Y RECOMENDACIÓN', NULL, NULL, 1, 160, NULL, NULL);

INSERT INTO encuesta_det (encuesta_id, tipo_id, enunciado, texto_descripcion, placeholder, opcional, orden, dep_encuesta_det_id, dep_encuesta_det_opcion_id)
VALUES (@encuesta_id, 10, '¿Qué tan probable es que utilice esta tarjeta frecuentemente en sus compras?', '0 = Nada probable | 10 = Totalmente probable', NULL, 0, 170, NULL, NULL);
SET @det_id = SCOPE_IDENTITY();
INSERT INTO encuesta_det_meta (encuesta_det_id, llave, valor) VALUES
(@det_id, 'min', '0'),
(@det_id, 'max', '10');

INSERT INTO encuesta_det (encuesta_id, tipo_id, enunciado, texto_descripcion, placeholder, opcional, orden, dep_encuesta_det_id, dep_encuesta_det_opcion_id)
VALUES (@encuesta_id, 10, 'Considerando el proceso de solicitud, la entrega y lo que conoce hasta ahora de sus beneficios, ¿qué tan probable es que recomiende a otra persona accionista solicitar una tarjeta de crédito de Caja de ANDE?', '0 = Nada probable | 10 = Totalmente probable', NULL, 0, 180, NULL, NULL);
SET @det_id = SCOPE_IDENTITY();
INSERT INTO encuesta_det_meta (encuesta_det_id, llave, valor) VALUES
(@det_id, 'min', '0'),
(@det_id, 'max', '10');

/* Bloque: Comentarios finales */
INSERT INTO encuesta_det (encuesta_id, tipo_id, enunciado, texto_descripcion, placeholder, opcional, orden, dep_encuesta_det_id, dep_encuesta_det_opcion_id)
VALUES (@encuesta_id, 16, 'COMENTARIOS FINALES', NULL, NULL, 1, 190, NULL, NULL);

INSERT INTO encuesta_det (encuesta_id, tipo_id, enunciado, texto_descripcion, placeholder, opcional, orden, dep_encuesta_det_id, dep_encuesta_det_opcion_id)
VALUES (@encuesta_id, 6, '¿Qué fue lo que más valoró de su experiencia?', NULL, NULL, 1, 200, NULL, NULL);
SET @det_id = SCOPE_IDENTITY();
INSERT INTO encuesta_det_meta (encuesta_det_id, llave, valor)
VALUES (@det_id, 'max', '1000');

INSERT INTO encuesta_det (encuesta_id, tipo_id, enunciado, texto_descripcion, placeholder, opcional, orden, dep_encuesta_det_id, dep_encuesta_det_opcion_id)
VALUES (@encuesta_id, 6, '¿Qué deberíamos mejorar?', NULL, NULL, 1, 210, NULL, NULL);
SET @det_id = SCOPE_IDENTITY();
INSERT INTO encuesta_det_meta (encuesta_det_id, llave, valor)
VALUES (@det_id, 'max', '1000');

COMMIT TRANSACTION;

SELECT @encuesta_id AS encuesta_id_creada;
END TRY
BEGIN CATCH
    IF @@TRANCOUNT > 0
        ROLLBACK TRANSACTION;

    THROW;
END CATCH;


ALTER TABLE respuestas ADD
  origen_trafico varchar(50) NULL,
  utm_source varchar(100) NULL,
  utm_medium varchar(100) NULL,
  utm_campaign varchar(255) NULL,
  utm_content varchar(255) NULL,
  utm_term varchar(255) NULL,
  utm_id varchar(255) NULL,
  fbclid varchar(500) NULL,
  meta_campaign_id varchar(100) NULL,
  meta_adset_id varchar(100) NULL,
  meta_ad_id varchar(100) NULL,
  link_id varchar(100) NULL,
  referrer_url varchar(1000) NULL;