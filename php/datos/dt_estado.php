<?php
class dt_estado extends resultados_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT id_estado, descripcion FROM estado ORDER BY descripcion";
		return toba::db('resultados')->consultar($sql);
	}



}
?>