<?php
/**
 * Esta clase fue y será generada automáticamente. NO EDITAR A MANO.
 * @ignore
 */
class resultados_autoload 
{
	static function existe_clase($nombre)
	{
		return isset(self::$clases[$nombre]);
	}

	static function cargar($nombre)
	{
		if (self::existe_clase($nombre)) { 
			 require_once(dirname(__FILE__) .'/'. self::$clases[$nombre]); 
		}
	}

	static protected $clases = array(
		'resultados_ci' => 'extension_toba/componentes/resultados_ci.php',
		'resultados_cn' => 'extension_toba/componentes/resultados_cn.php',
		'resultados_datos_relacion' => 'extension_toba/componentes/resultados_datos_relacion.php',
		'resultados_datos_tabla' => 'extension_toba/componentes/resultados_datos_tabla.php',
		'resultados_ei_arbol' => 'extension_toba/componentes/resultados_ei_arbol.php',
		'resultados_ei_archivos' => 'extension_toba/componentes/resultados_ei_archivos.php',
		'resultados_ei_calendario' => 'extension_toba/componentes/resultados_ei_calendario.php',
		'resultados_ei_codigo' => 'extension_toba/componentes/resultados_ei_codigo.php',
		'resultados_ei_cuadro' => 'extension_toba/componentes/resultados_ei_cuadro.php',
		'resultados_ei_esquema' => 'extension_toba/componentes/resultados_ei_esquema.php',
		'resultados_ei_filtro' => 'extension_toba/componentes/resultados_ei_filtro.php',
		'resultados_ei_firma' => 'extension_toba/componentes/resultados_ei_firma.php',
		'resultados_ei_formulario' => 'extension_toba/componentes/resultados_ei_formulario.php',
		'resultados_ei_formulario_ml' => 'extension_toba/componentes/resultados_ei_formulario_ml.php',
		'resultados_ei_grafico' => 'extension_toba/componentes/resultados_ei_grafico.php',
		'resultados_ei_mapa' => 'extension_toba/componentes/resultados_ei_mapa.php',
		'resultados_servicio_web' => 'extension_toba/componentes/resultados_servicio_web.php',
		'resultados_comando' => 'extension_toba/resultados_comando.php',
		'resultados_modelo' => 'extension_toba/resultados_modelo.php',
	);
}
?>