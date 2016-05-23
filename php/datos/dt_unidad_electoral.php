<?php
class dt_unidad_electoral extends resultados_datos_tabla
{
	function get_listado()
	{
		$sql = "SELECT
			t_ue.id_nro_ue,
			t_ue.nombre,
			t_ue.cant_empadronados,
			t_ue.cant_empadronados_nd,
			t_ue.cant_empadronados_d,
			t_ue.cant_empadronados_g
		FROM
			unidad_electoral as t_ue
		ORDER BY nombre";
		return toba::db('resultados')->consultar($sql);
	}


	function get_descripciones($id = null)
	{
            $where = "";
            if(isset($id))
                $where = " WHERE id_nro_ue = $id ";
            $sql = "SELECT id_nro_ue, nombre, sigla FROM unidad_electoral $where ORDER BY nombre";
            return toba::db('resultados')->consultar($sql);
	}
	function get_descripciones_ponderados()
	{       
            $sql = "SELECT id_nro_ue, nombre, sigla FROM unidad_electoral where sigla not in ('ASMA','AUZA') ORDER BY nombre";
            return toba::db('resultados')->consultar($sql);
	}


        /*function get_nombre($id_nro_ue){
            $sql = "SELECT nombre FROM unidad_electoral WHERE id_nro_ue = $id_nro_ue";
            $ar = toba::db('gu_kena')->consultar($sql);
            //print_r($ar['nombre']);
            return $ar['nombre'];
        }*/

}
?>
