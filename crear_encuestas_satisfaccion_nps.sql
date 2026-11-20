/*
  SQL Server - Encuestas de satisfacción NPS
  Fuente: Encuesta de satisfacción.docx (cinco preguntas y dos comentarios).
  Conserva las preguntas del documento; no agrega una escala NPS de 0 a 10.
  Ejecutar en la base del ambiente correspondiente. No fija IDs.
  Revisar @usuario_id, fechas y @correos antes de ejecutar.
  Sin datos personales ni avisos por correo por defecto.
  Si ya existe una encuesta con el mismo nombre, se cancela sin modificarla.
*/
USE [encuestas_db];
GO
SET NOCOUNT ON;
SET XACT_ABORT ON;

DECLARE @nombre NVARCHAR(200) = N'Encuestas de satisfacción NPS';
DECLARE @usuario_id INT = 1; -- Ajustar al usuario creador válido en este ambiente.
DECLARE @fecha_inicio DATE = CONVERT(DATE, GETDATE());
DECLARE @fecha_fin DATE = NULL; -- Sin fecha de cierre.
DECLARE @correos NVARCHAR(MAX) = NULL; -- Ejemplo: N'encargado@dominio.com'
DECLARE @encuesta_id INT;
DECLARE @det_id INT;

BEGIN TRY
    BEGIN TRANSACTION;

    IF EXISTS (
        SELECT 1 FROM dbo.encuestas WITH (UPDLOCK, HOLDLOCK)
        WHERE nombre = @nombre
    )
        THROW 50001, N'Ya existe una encuesta con este nombre. No se realizaron cambios.', 1;

    INSERT INTO dbo.encuestas (
        nombre, descripcion, fecha_inicio, fecha_fin,
        incluye_nombre, nombre_oblig,
        incluye_cedula, cedula_oblig,
        incluye_sucursal, sucursal_oblig,
        incluye_unidad, unidad_oblig,
        incluye_agente, agente_oblig,
        incluye_gestion, gestion_oblig,
        incluye_fecha_nac, fecha_nac_oblig,
        incluye_estado_emp, estado_emp_oblig,
        incluye_genero, genero_oblig,
        incluye_ubicacion, ubicacion_oblig,
        incluye_puesto, puesto_oblig,
        incluye_correo, correo_oblig,
        incluye_telefono, telefono_oblig,
        incluye_est_civil, est_civil_oblig,
        correos, mensaje_exito, mensaje_bloqueo,
        mostrar_num_pregunta, folder_id, nombre_csv,
        activo, creado_por, actualizado_por
    ) VALUES (
        @nombre,
        N'Su opinión es importante para mejorar futuras actividades de formación. Por favor, responda las siguientes preguntas según su experiencia. La información recopilada será utilizada para evaluar la calidad de la capacitación y del servicio brindado.',
        @fecha_inicio, @fecha_fin,
        0, 0, -- Nombre
        0, 0, -- Cédula
        0, 0, -- Sucursal
        0, 0, -- Unidad
        0, 0, -- Agente
        0, 0, -- Gestión
        0, 0, -- Fecha de nacimiento
        0, 0, -- Estado del colaborador
        0, 0, -- Género
        0, 0, -- Ubicación
        0, 0, -- Puesto
        0, 0, -- Correo
        0, 0, -- Teléfono
        0, 0, -- Estado civil
        @correos,
        N'¡Muchas gracias por dedicar tiempo a completar esta encuesta!',
        NULL, 1, NULL, N'encuestas_satisfaccion_nps',
        1, @usuario_id, @usuario_id
    );
    SET @encuesta_id = CONVERT(INT, SCOPE_IDENTITY());

    -- Tipos definidos en Variables.php: 16 subtítulo, 8 radio, 6 texto largo.
    INSERT INTO dbo.encuesta_det
        (encuesta_id, tipo_id, enunciado, texto_descripcion, placeholder, opcional, orden, dep_encuesta_det_id, dep_encuesta_det_opcion_id)
    VALUES (@encuesta_id, 16, N'Evaluación general', NULL, NULL, 1, 10, NULL, NULL);

    -- 1. Satisfacción con la capacitación (obligatoria).
    INSERT INTO dbo.encuesta_det
        (encuesta_id, tipo_id, enunciado, texto_descripcion, placeholder, opcional, orden, dep_encuesta_det_id, dep_encuesta_det_opcion_id)
    VALUES (@encuesta_id, 8, N'¿Qué tan satisfecho(a) está con la capacitación recibida?', NULL, NULL, 0, 20, NULL, NULL);
    SET @det_id = CONVERT(INT, SCOPE_IDENTITY());
    INSERT INTO dbo.encuesta_det_opciones
        (encuesta_det_id, texto, otro, explicacion, bloquea_otras, correos, seleccionada, orden, activo)
    VALUES
        (@det_id, N'Muy insatisfecho(a)', 0, 0, 0, NULL, 0, 1, 1),
        (@det_id, N'Insatisfecho(a)', 0, 0, 0, NULL, 0, 2, 1),
        (@det_id, N'Ni satisfecho(a) ni insatisfecho(a)', 0, 0, 0, NULL, 0, 3, 1),
        (@det_id, N'Satisfecho(a)', 0, 0, 0, NULL, 0, 4, 1),
        (@det_id, N'Muy satisfecho(a)', 0, 0, 0, NULL, 0, 5, 1);

    -- 2. Claridad de los contenidos (obligatoria).
    INSERT INTO dbo.encuesta_det
        (encuesta_id, tipo_id, enunciado, texto_descripcion, placeholder, opcional, orden, dep_encuesta_det_id, dep_encuesta_det_opcion_id)
    VALUES (@encuesta_id, 8, N'¿Considera que los contenidos fueron claros y fáciles de comprender?', NULL, NULL, 0, 30, NULL, NULL);
    SET @det_id = CONVERT(INT, SCOPE_IDENTITY());
    INSERT INTO dbo.encuesta_det_opciones
        (encuesta_det_id, texto, otro, explicacion, bloquea_otras, correos, seleccionada, orden, activo)
    VALUES
        (@det_id, N'Deficiente', 0, 0, 0, NULL, 0, 1, 1),
        (@det_id, N'Regular', 0, 0, 0, NULL, 0, 2, 1),
        (@det_id, N'Bueno', 0, 0, 0, NULL, 0, 3, 1),
        (@det_id, N'Muy bueno', 0, 0, 0, NULL, 0, 4, 1),
        (@det_id, N'Excelente', 0, 0, 0, NULL, 0, 5, 1);

    -- 3. Duración de la capacitación (obligatoria).
    INSERT INTO dbo.encuesta_det
        (encuesta_id, tipo_id, enunciado, texto_descripcion, placeholder, opcional, orden, dep_encuesta_det_id, dep_encuesta_det_opcion_id)
    VALUES (@encuesta_id, 8, N'¿La duración de la capacitación fue adecuada?', NULL, NULL, 0, 40, NULL, NULL);
    SET @det_id = CONVERT(INT, SCOPE_IDENTITY());
    INSERT INTO dbo.encuesta_det_opciones
        (encuesta_det_id, texto, otro, explicacion, bloquea_otras, correos, seleccionada, orden, activo)
    VALUES
        (@det_id, N'Sí', 0, 0, 0, NULL, 0, 1, 1),
        (@det_id, N'Parcialmente', 0, 0, 0, NULL, 0, 2, 1),
        (@det_id, N'No', 0, 0, 0, NULL, 0, 3, 1);

    -- 4. Desempeño de la persona facilitadora (obligatoria).
    INSERT INTO dbo.encuesta_det
        (encuesta_id, tipo_id, enunciado, texto_descripcion, placeholder, opcional, orden, dep_encuesta_det_id, dep_encuesta_det_opcion_id)
    VALUES (@encuesta_id, 8, N'¿Cómo califica el desempeño de la persona facilitadora?', NULL, NULL, 0, 50, NULL, NULL);
    SET @det_id = CONVERT(INT, SCOPE_IDENTITY());
    INSERT INTO dbo.encuesta_det_opciones
        (encuesta_det_id, texto, otro, explicacion, bloquea_otras, correos, seleccionada, orden, activo)
    VALUES
        (@det_id, N'Deficiente', 0, 0, 0, NULL, 0, 1, 1),
        (@det_id, N'Regular', 0, 0, 0, NULL, 0, 2, 1),
        (@det_id, N'Bueno', 0, 0, 0, NULL, 0, 3, 1),
        (@det_id, N'Muy bueno', 0, 0, 0, NULL, 0, 4, 1),
        (@det_id, N'Excelente', 0, 0, 0, NULL, 0, 5, 1);

    -- 5. Solicitud de tarjeta nueva (obligatoria).
    INSERT INTO dbo.encuesta_det
        (encuesta_id, tipo_id, enunciado, texto_descripcion, placeholder, opcional, orden, dep_encuesta_det_id, dep_encuesta_det_opcion_id)
    VALUES (@encuesta_id, 8, N'¿Cómo califica el proceso de la solicitud de una tarjeta nueva?', NULL, NULL, 0, 60, NULL, NULL);
    SET @det_id = CONVERT(INT, SCOPE_IDENTITY());
    INSERT INTO dbo.encuesta_det_opciones
        (encuesta_det_id, texto, otro, explicacion, bloquea_otras, correos, seleccionada, orden, activo)
    VALUES
        (@det_id, N'Deficiente', 0, 0, 0, NULL, 0, 1, 1),
        (@det_id, N'Regular', 0, 0, 0, NULL, 0, 2, 1),
        (@det_id, N'Bueno', 0, 0, 0, NULL, 0, 3, 1),
        (@det_id, N'Muy bueno', 0, 0, 0, NULL, 0, 4, 1),
        (@det_id, N'Excelente', 0, 0, 0, NULL, 0, 5, 1);

    INSERT INTO dbo.encuesta_det
        (encuesta_id, tipo_id, enunciado, texto_descripcion, placeholder, opcional, orden, dep_encuesta_det_id, dep_encuesta_det_opcion_id)
    VALUES (@encuesta_id, 16, N'Comentarios y sugerencias (Opcionales)', NULL, NULL, 1, 70, NULL, NULL);

    -- 6 y 7. Comentarios opcionales, sin límite adicional al del aplicativo/BD.
    INSERT INTO dbo.encuesta_det
        (encuesta_id, tipo_id, enunciado, texto_descripcion, placeholder, opcional, orden, dep_encuesta_det_id, dep_encuesta_det_opcion_id)
    VALUES
        (@encuesta_id, 6, N'¿Qué otros temas le gustarían que se abordaran en futuras capacitaciones?', NULL, NULL, 1, 80, NULL, NULL),
        (@encuesta_id, 6, N'Comentarios adicionales:', NULL, NULL, 1, 90, NULL, NULL);

    -- Comprobar la estructura antes de confirmar la transacción.
    IF (SELECT COUNT(*) FROM dbo.encuesta_det WHERE encuesta_id = @encuesta_id) <> 9
        THROW 50002, N'No se crearon todos los campos esperados.', 1;

    IF (SELECT COUNT(*) FROM dbo.encuesta_det_opciones o
        INNER JOIN dbo.encuesta_det d ON d.encuesta_det_id = o.encuesta_det_id
        WHERE d.encuesta_id = @encuesta_id) <> 23
        THROW 50003, N'No se crearon todas las opciones esperadas.', 1;

    COMMIT TRANSACTION;

    SELECT DB_NAME() AS base_de_datos, @encuesta_id AS encuesta_id_creada,
           @nombre AS nombre,
           N'index.php?id=' + CONVERT(NVARCHAR(20), @encuesta_id) AS ruta_formulario;

    SELECT encuesta_det_id, tipo_id, enunciado, opcional, orden
    FROM dbo.encuesta_det
    WHERE encuesta_id = @encuesta_id
    ORDER BY orden;
END TRY
BEGIN CATCH
    IF XACT_STATE() <> 0 ROLLBACK TRANSACTION;
    THROW;
END CATCH;
