<?php
class dt_claustro extends resultados_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT id, descripcion FROM claustro where id<>2 ORDER BY descripcion";
		return toba::db('resultados')->consultar($sql);
	}









        function get_descripcion($id_claustro){
            $sql = "SELECT descripcion FROM claustro WHERE id = $id_claustro";
            $ar = toba::db('resultados')->consultar($sql);
            return $ar[0]['descripcion'];
        }
}
?>
