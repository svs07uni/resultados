------------------------------------------------------------
--[2000027]--  Historial - DR 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 2
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'resultados', --proyecto
	'2000027', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_datos_relacion', --clase
	'10000004', --punto_montaje
	NULL, --subclase
	NULL, --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'Historial - DR', --nombre
	NULL, --titulo
	NULL, --colapsable
	NULL, --descripcion
	'resultados', --fuente_datos_proyecto
	'resultados', --fuente_datos
	NULL, --solicitud_registrar
	NULL, --solicitud_obj_obs_tipo
	NULL, --solicitud_obj_observacion
	NULL, --parametro_a
	NULL, --parametro_b
	NULL, --parametro_c
	NULL, --parametro_d
	NULL, --parametro_e
	NULL, --parametro_f
	NULL, --usuario
	'2016-07-06 11:42:43', --creacion
	NULL  --posicion_botonera
);
--- FIN Grupo de desarrollo 2

------------------------------------------------------------
-- apex_objeto_datos_rel
------------------------------------------------------------
INSERT INTO apex_objeto_datos_rel (proyecto, objeto, debug, clave, ap, punto_montaje, ap_clase, ap_archivo, sinc_susp_constraints, sinc_orden_automatico, sinc_lock_optimista) VALUES (
	'resultados', --proyecto
	'2000027', --objeto
	'0', --debug
	NULL, --clave
	'2', --ap
	'10000004', --punto_montaje
	NULL, --ap_clase
	NULL, --ap_archivo
	'0', --sinc_susp_constraints
	'1', --sinc_orden_automatico
	'1'  --sinc_lock_optimista
);

------------------------------------------------------------
-- apex_objeto_dependencias
------------------------------------------------------------

--- INICIO Grupo de desarrollo 2
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'resultados', --proyecto
	'2000035', --dep_id
	'2000027', --objeto_consumidor
	'3622', --objeto_proveedor
	'claustro', --identificador
	'1', --parametros_a
	'1', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'1'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'resultados', --proyecto
	'2000039', --dep_id
	'2000027', --objeto_consumidor
	'10000265', --objeto_proveedor
	'mesa', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'2'  --orden
);
--- FIN Grupo de desarrollo 2
