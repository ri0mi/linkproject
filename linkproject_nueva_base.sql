-- Table: public.alumnos

-- DROP TABLE IF EXISTS public.alumnos;

CREATE TABLE IF NOT EXISTS public.alumnos
(
    id_alumno integer NOT NULL,
    nombre text COLLATE pg_catalog."default" NOT NULL,
    contrasena character varying(8) COLLATE pg_catalog."default" NOT NULL,
    estado character varying(20) COLLATE pg_catalog."default" NOT NULL DEFAULT 'disponible'::character varying,
    correo character varying(254) COLLATE pg_catalog."default" NOT NULL,
    contacto character varying(15) COLLATE pg_catalog."default",
    carrera text COLLATE pg_catalog."default",
    foto text COLLATE pg_catalog."default",
    clave text COLLATE pg_catalog."default",
    laboratorio text COLLATE pg_catalog."default",
    horario text COLLATE pg_catalog."default",
    habilidades text COLLATE pg_catalog."default",
    codigo_verificacion character varying(20) COLLATE pg_catalog."default" DEFAULT 'no_verificado'::character varying,
    CONSTRAINT pk_id_alumno PRIMARY KEY (id_alumno),
    CONSTRAINT alumnos_correo_key UNIQUE (correo),
    CONSTRAINT alumnos_estado_check CHECK (estado::text = ANY (ARRAY['disponible'::character varying, 'no disponible'::character varying]::text[]))
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.alumnos
    OWNER to postgres;


-- Table: public.comentarios

-- DROP TABLE IF EXISTS public.comentarios;

CREATE TABLE IF NOT EXISTS public.comentarios
(
    id_comentario integer NOT NULL DEFAULT nextval('comentarios_id_comentario_seq'::regclass),
    proyecto_id integer NOT NULL,
    autor_id integer NOT NULL,
    contenido text COLLATE pg_catalog."default" NOT NULL,
    fecha timestamp without time zone DEFAULT now(),
    CONSTRAINT comentarios_pkey PRIMARY KEY (id_comentario),
    CONSTRAINT comentarios_autor_id_fkey FOREIGN KEY (autor_id)
        REFERENCES public.maestros (id_maestro) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE CASCADE,
    CONSTRAINT comentarios_proyecto_id_fkey FOREIGN KEY (proyecto_id)
        REFERENCES public.proyectos (id_proyecto) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE CASCADE
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.comentarios
    OWNER to postgres;



-- Table: public.equipos

-- DROP TABLE IF EXISTS public.equipos;

CREATE TABLE IF NOT EXISTS public.equipos
(
    id integer NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1 ),
    proyecto_id integer NOT NULL,
    miembro_id integer NOT NULL,
    rol text COLLATE pg_catalog."default" NOT NULL,
    CONSTRAINT equipos_pkey PRIMARY KEY (id),
    CONSTRAINT equipos_miembro_id_fkey FOREIGN KEY (miembro_id)
        REFERENCES public.alumnos (id_alumno) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT equipos_proyecto_id_fkey FOREIGN KEY (proyecto_id)
        REFERENCES public.proyectos (id_proyecto) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT equipos_rol_check CHECK (rol = ANY (ARRAY['líder'::text, 'miembro'::text, 'asesor'::text]))
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.equipos
    OWNER to postgres;



-- Table: public.invitaciones

-- DROP TABLE IF EXISTS public.invitaciones;

CREATE TABLE IF NOT EXISTS public.invitaciones
(
    id_invitacion integer NOT NULL DEFAULT nextval('invitaciones_id_invitacion_seq'::regclass),
    proyecto_id integer NOT NULL,
    invitado_id integer NOT NULL,
    estado_invitacion text COLLATE pg_catalog."default" DEFAULT 'pendiente'::text,
    fecha_invitacion timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT invitaciones_pkey PRIMARY KEY (id_invitacion),
    CONSTRAINT fk_invitado FOREIGN KEY (invitado_id)
        REFERENCES public.alumnos (id_alumno) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT fk_proyecto FOREIGN KEY (proyecto_id)
        REFERENCES public.proyectos (id_proyecto) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT invitaciones_estado_invitacion_check CHECK (estado_invitacion = ANY (ARRAY['pendiente'::text, 'aceptada'::text, 'rechazada'::text]))
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.invitaciones
    OWNER to postgres;


-- Table: public.invitaciones_maestros

-- DROP TABLE IF EXISTS public.invitaciones_maestros;

CREATE TABLE IF NOT EXISTS public.invitaciones_maestros
(
    id_invitacion integer NOT NULL DEFAULT nextval('invitaciones_id_invitacion_seq'::regclass),
    proyecto_id integer NOT NULL,
    invitado_id integer NOT NULL,
    estado_invitacion text COLLATE pg_catalog."default" DEFAULT 'pendiente'::text,
    fecha_invitacion timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT invitaciones_maestros_pkey PRIMARY KEY (id_invitacion),
    CONSTRAINT fk_invitado_maestro FOREIGN KEY (invitado_id)
        REFERENCES public.maestros (id_maestro) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT fk_proyecto FOREIGN KEY (proyecto_id)
        REFERENCES public.proyectos (id_proyecto) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT invitaciones_estado_invitacion_check CHECK (estado_invitacion = ANY (ARRAY['pendiente'::text, 'aceptada'::text, 'rechazada'::text]))
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.invitaciones_maestros
    OWNER to postgres;



-- Table: public.maestros

-- DROP TABLE IF EXISTS public.maestros;

CREATE TABLE IF NOT EXISTS public.maestros
(
    id_maestro integer NOT NULL,
    nombre text COLLATE pg_catalog."default" NOT NULL,
    correo character varying(254) COLLATE pg_catalog."default" NOT NULL,
    contrasena character varying(8) COLLATE pg_catalog."default" NOT NULL,
    departamento text COLLATE pg_catalog."default",
    foto text COLLATE pg_catalog."default",
    rol text COLLATE pg_catalog."default" DEFAULT 'asesor'::text,
    clave text COLLATE pg_catalog."default",
    codigo_verificacion character varying(20) COLLATE pg_catalog."default" DEFAULT 'no_verificado'::character varying,
    CONSTRAINT pk_id_maestro PRIMARY KEY (id_maestro),
    CONSTRAINT maestros_correo_key UNIQUE (correo),
    CONSTRAINT maestros_rol_check CHECK (rol = 'asesor'::text)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.maestros
    OWNER to postgres;



-- Table: public.proyectos

-- DROP TABLE IF EXISTS public.proyectos;

CREATE TABLE IF NOT EXISTS public.proyectos
(
    id_proyecto integer NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1 ),
    nombre text COLLATE pg_catalog."default" NOT NULL,
    descripcion text COLLATE pg_catalog."default" NOT NULL,
    areas text COLLATE pg_catalog."default" NOT NULL,
    cupos integer NOT NULL DEFAULT 0,
    asesor text COLLATE pg_catalog."default" NOT NULL,
    conocimientos text COLLATE pg_catalog."default" NOT NULL,
    nivel_innovacion text COLLATE pg_catalog."default" NOT NULL,
    logo text COLLATE pg_catalog."default",
    miembros integer DEFAULT 0,
    lider_id integer NOT NULL,
    maestro_id integer,
    CONSTRAINT proyectos_pkey PRIMARY KEY (id_proyecto),
    CONSTRAINT fk_maestro_id FOREIGN KEY (maestro_id)
        REFERENCES public.maestros (id_maestro) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT proyectos_lider_id_fkey FOREIGN KEY (lider_id)
        REFERENCES public.alumnos (id_alumno) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.proyectos
    OWNER to postgres;



-- Table: public.solicitudes

-- DROP TABLE IF EXISTS public.solicitudes;

CREATE TABLE IF NOT EXISTS public.solicitudes
(
    id integer NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1 ),
    alumno_id integer NOT NULL,
    proyecto_id integer NOT NULL,
    estado text COLLATE pg_catalog."default" DEFAULT 'pendiente'::text,
    fecha_solicitud timestamp with time zone DEFAULT now(),
    CONSTRAINT solicitudes_pkey PRIMARY KEY (id),
    CONSTRAINT solicitudes_alumno_id_fkey FOREIGN KEY (alumno_id)
        REFERENCES public.alumnos (id_alumno) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT solicitudes_proyecto_id_fkey FOREIGN KEY (proyecto_id)
        REFERENCES public.proyectos (id_proyecto) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT solicitudes_estado_check CHECK (estado = ANY (ARRAY['pendiente'::text, 'aceptada'::text, 'rechazada'::text]))
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.solicitudes
    OWNER to postgres;



-- Table: public.tareas

-- DROP TABLE IF EXISTS public.tareas;

CREATE TABLE IF NOT EXISTS public.tareas
(
    id integer NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1 ),
    nombre text COLLATE pg_catalog."default" NOT NULL,
    proyecto_id integer NOT NULL,
    responsable_id integer NOT NULL,
    fecha_inicio date NOT NULL,
    fecha_fin date NOT NULL,
    avances text COLLATE pg_catalog."default",
    asesor_id integer,
    retroalimentacion text COLLATE pg_catalog."default",
    estado text COLLATE pg_catalog."default" DEFAULT 'pendiente'::text,
    equipo_id integer,
    descripcion text COLLATE pg_catalog."default",
    CONSTRAINT tareas_pkey PRIMARY KEY (id),
    CONSTRAINT tareas_asesor_id_fkey FOREIGN KEY (asesor_id)
        REFERENCES public.maestros (id_maestro) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT tareas_equipo_id_fkey FOREIGN KEY (equipo_id)
        REFERENCES public.equipos (id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT tareas_proyecto_id_fkey FOREIGN KEY (proyecto_id)
        REFERENCES public.proyectos (id_proyecto) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT tareas_responsable_id_fkey FOREIGN KEY (responsable_id)
        REFERENCES public.alumnos (id_alumno) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT estado CHECK (estado = ANY (ARRAY['pendiente'::text, 'progreso'::text, 'completo'::text]))
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.tareas
    OWNER to postgres;
