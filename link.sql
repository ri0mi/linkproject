


/*Crear tabla alumnos*/

CREATE TABLE IF NOT EXISTS public.alumnos
(
    id_alumno integer NOT NULL,
    nombre text COLLATE pg_catalog."default" NOT NULL,
    contrasena character varying(8) COLLATE pg_catalog."default" NOT NULL,
    estado character varying(20) COLLATE pg_catalog."default" NOT NULL DEFAULT 'disponible'::character varying,
    correo character varying(254) COLLATE pg_catalog."default" NOT NULL,
    contacto character varying(15) COLLATE pg_catalog."default" NOT NULL,
    carrera text COLLATE pg_catalog."default" NOT NULL,
    foto text COLLATE pg_catalog."default",
    clave text COLLATE pg_catalog."default" NOT NULL,
    laboratorio text COLLATE pg_catalog."default" NOT NULL,
    horario text COLLATE pg_catalog."default" NOT NULL,
    habilidades text COLLATE pg_catalog."default" NOT NULL,
    CONSTRAINT pk_id_alumno PRIMARY KEY (id_alumno),
    CONSTRAINT alumnos_correo_key UNIQUE (correo),
    CONSTRAINT alumnos_estado_check CHECK (estado::text = ANY (ARRAY['disponible'::character varying, 'no disponible'::character varying]::text[]))
)



/*Crear tabla maestros*/


CREATE TABLE IF NOT EXISTS public.maestros
(
    id_maestro integer NOT NULL,
    nombre text COLLATE pg_catalog."default" NOT NULL,
    correo character varying(254) COLLATE pg_catalog."default" NOT NULL,
    contrasena character varying(8) COLLATE pg_catalog."default" NOT NULL,
    departamento text COLLATE pg_catalog."default" NOT NULL,
    foto text COLLATE pg_catalog."default",
    rol text COLLATE pg_catalog."default" DEFAULT 'asesor'::text,
    clave text COLLATE pg_catalog."default" NOT NULL,
    CONSTRAINT pk_id_maestro PRIMARY KEY (id_maestro),
    CONSTRAINT maestros_correo_key UNIQUE (correo),
    CONSTRAINT maestros_rol_check CHECK (rol = 'asesor'::text)
)




/*Crear tabla proyectos*/


CREATE TABLE IF NOT EXISTS public.proyectos
(
    id integer NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1 ),
    nombre text COLLATE pg_catalog."default" NOT NULL,
    descripcion text COLLATE pg_catalog."default" NOT NULL,
    areas text COLLATE pg_catalog."default" NOT NULL,
    cupos integer NOT NULL,
    asesor text COLLATE pg_catalog."default" NOT NULL,
    conocimientos text COLLATE pg_catalog."default" NOT NULL,
    nivel_innovacion text COLLATE pg_catalog."default" NOT NULL,
    logo text COLLATE pg_catalog."default",
    miembros text COLLATE pg_catalog."default",
    lider_id integer NOT NULL,
    CONSTRAINT proyectos_pkey PRIMARY KEY (id),
    CONSTRAINT proyectos_lider_id_fkey FOREIGN KEY (lider_id)
        REFERENCES public.alumnos (id_alumno) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)


/*Crear tabla equipos*/

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
        REFERENCES public.proyectos (id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT equipos_rol_check CHECK (rol = ANY (ARRAY['líder'::text, 'miembro'::text]))
)



/*Crear tabla solicitudes*/

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
        REFERENCES public.proyectos (id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT solicitudes_estado_check CHECK (estado = ANY (ARRAY['pendiente'::text, 'aceptada'::text, 'rechazada'::text]))
)


/*Crear tabla tareas*/

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
    CONSTRAINT tareas_pkey PRIMARY KEY (id),
    CONSTRAINT tareas_asesor_id_fkey FOREIGN KEY (asesor_id)
        REFERENCES public.maestros (id_maestro) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT tareas_proyecto_id_fkey FOREIGN KEY (proyecto_id)
        REFERENCES public.proyectos (id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT tareas_responsable_id_fkey FOREIGN KEY (responsable_id)
        REFERENCES public.alumnos (id_alumno) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)


