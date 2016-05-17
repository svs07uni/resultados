<?php
class dt_tipo extends resultados_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT id_tipo, descripcion FROM tipo ORDER BY descripcion";
		return toba::db('resultados')->consultar($sql);
	}



























}
?>
