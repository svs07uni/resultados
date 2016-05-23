<?php

class dt_mesa extends resultados_datos_tabla {

    function get_listado_votos($wherefiltro = null) {
        $where = "WHERE t_ue.sigla not in ('ASMA','AUZA') ";
        if (isset($wherefiltro)) {
            $where .= " and $wherefiltro ";
        }
        $sql = "SELECT
                        t_c.descripcion as claustro,
                        t_ue.sigla as unidad,                        
                        t_s.sigla as sede,
			t_m.nro_mesa,
			t_m.cant_empadronados,
			t_m.id_mesa,
			acta.total_votos_blancos,
                        acta.total_votos_recurridos,
                        acta.total_votos_nulos,
                        t_vls.id_lista as lista,
                        t_vls.cant_votos as votos
		FROM
			mesa as t_m	
                        LEFT OUTER JOIN claustro as t_c ON (t_m.id_claustro = t_c.id)
			LEFT OUTER JOIN sede as t_s ON (t_m.id_sede = t_s.id_sede) 
                        LEFT OUTER JOIN unidad_electoral as t_ue ON (t_s.id_ue = t_ue.id_nro_ue)
                        left outer join acta on acta.para=t_m.id_mesa
                        left outer join voto_lista_csuperior t_vls on t_vls.id_acta=acta.id_acta
                         
                        $where
                        order by t_c.descripcion, t_ue.nombre, t_s.nombre, t_m.nro_mesa,t_vls.id_lista"
        ;
        $completo = toba::db('resultados')->consultar($sql);
        $unidad = null;
        $sede = null;
        $nro_mesa = null;
        $resumen = array();
        $filanueva = null;
        foreach ($completo as $key => $fila) {
            if ($fila['unidad'] != $unidad || $fila['sede'] != $sede || $fila['nro_mesa'] != $nro_mesa) {
               
                if (!is_null($filanueva)) {
                    $filanueva['total'] = $total+$filanueva['total_votos_blancos']+$filanueva['total_votos_nulos']+$filanueva['total_votos_recurridos'];
                    
                    $resumen[] = $filanueva;
                }
                $total = 0;
                $unidad = $fila['unidad'];
                $sede = $fila['sede'];
                $nro_mesa = $fila['nro_mesa'];

                $filanueva = $fila;

                unset($filanueva['lista']);
                unset($filanueva['votos']);
            }
            $filanueva[$fila['lista']] = $fila['votos'];
            $total+=$fila['votos'];
        }
        
        if (!is_null($filanueva)) {
            $filanueva['total'] = $total;
            $resumen[] = $filanueva;
        }
        return $resumen;
    }

    function get_descripciones($id_sede = null, $claustro = null, $id_mesa = null) {
        if (isset($id_sede) && isset($claustro)) {
            $where = " WHERE id_sede = $id_sede AND id_claustro = $claustro";
        } else {
            if (isset($id_mesa))
                $where = "WHERE id_mesa = $id_mesa";
            else
                $where = "";
        }
        $sql = "SELECT id_mesa, nro_mesa, cant_empadronados FROM mesa $where ORDER BY id_mesa";
        return toba::db('resultados')->consultar($sql);
    }

    function get_empadronados($id_mesa) {
        $sql = "SELECT cant_empadronados,nro_mesa FROM mesa "
                . "WHERE id_mesa = $id_mesa ";

        $ar = toba::db('resultados')->consultar($sql);
        return $ar[0];
    }

    function cant_empadronados($id_nro_ue, $id_claustro) {
        $sql = "SELECT sum(t_m.cant_empadronados) as cant FROM mesa t_m "
                . "INNER JOIN sede t_s ON t_s.id_sede = t_m.id_sede"
                . " INNER JOIN unidad_electoral t_ue ON t_ue.id_nro_ue = t_s.id_ue "
                . "WHERE t_ue.id_nro_ue = $id_nro_ue"
                . "AND t_m.id_claustro = $id_claustro"
                . "AND t_m.fecha = (SELECT max(fecha) FROM mesa)";
        $ar = toba::db('resultados')->consultar($sql);
        return $ar[0]['cant'];
    }

    function get_listado($id_mesa = null) {
        $where = "";
        if (isset($id_mesa)) {
            $where = "WHERE id_mesa = $id_mesa";
        }
        $sql = "SELECT
			t_m.nro_mesa,
			t_m.cant_empadronados,
			t_c.descripcion as id_claustro_nombre,
			t_m.id_mesa,
			t_s.nombre as id_sede_nombre,
			t_m.fecha,
			t_m.estado,
                        t_c.descripcion as claustro,
                        t_s.nombre as sede,
                        t_ue.nombre as unidad_electoral
                        
		FROM
			mesa as t_m	
                        LEFT OUTER JOIN claustro as t_c ON (t_m.id_claustro = t_c.id)
			LEFT OUTER JOIN sede as t_s ON (t_m.id_sede = t_s.id_sede) 
                        LEFT OUTER JOIN unidad_electoral as t_ue ON (t_s.id_ue = t_ue.id_nro_ue) 
                        $where";
        return toba::db('resultados')->consultar($sql);
    }

    //usado por ci_validar
    function get_ultimo_listado($id_mesa = null) {
        $where = "";
        if (isset($id_mesa)) {
            $where = "WHERE id_mesa = $id_mesa AND t_m.fecha = (SELECT max(fecha) FROM mesa )";
        } else
            $where = "WHERE t_m.fecha = (SELECT max(fecha) FROM mesa )";

        $sql = "SELECT
			t_m.nro_mesa,
			t_m.cant_empadronados,
			t_m.id_mesa,
			t_m.fecha,
			t_m.estado,
                        t_c.descripcion as claustro,
                        t_s.nombre as sede,
                        t_ue.sigla as unidad_electoral
                        
		FROM
			mesa as t_m	
                        LEFT OUTER JOIN claustro as t_c ON (t_m.id_claustro = t_c.id)
			LEFT OUTER JOIN sede as t_s ON (t_m.id_sede = t_s.id_sede) 
                        LEFT OUTER JOIN unidad_electoral as t_ue ON (t_s.id_ue = t_ue.id_nro_ue) 
                        $where ORDER BY t_s.id_sede";
        return toba::db('resultados')->consultar($sql);
    }

    function get_cant_cargadas($id_claustro) {
        $sql = "SELECT count(id_mesa) as porc FROM mesa "
                . "WHERE fecha = (SELECT max(fecha) FROM mesa) "
                . "AND estado > 1 "
                . "AND ficticio = false "
                . "AND id_claustro = $id_claustro";
        $ar = toba::db('resultados')->consultar($sql);
        return $ar[0]['porc'];
    }

    function get_cant_confirmadas($id_claustro) {
        $sql = "SELECT count(id_mesa) as porc FROM mesa "
                . "WHERE fecha = (SELECT max(fecha) FROM mesa) "
                . "AND estado >= 3 "
                . "AND ficticio = false "
                . "AND id_claustro = $id_claustro";
        $ar = toba::db('resultados')->consultar($sql);
        return $ar[0]['porc'];
    }

    function get_cant_definitivas($id_claustro) {
        $sql = "SELECT count(id_mesa) as porc FROM mesa "
                . "WHERE fecha = (SELECT max(fecha) FROM mesa) "
                . "AND estado = 4"
                . "AND ficticio = false "
                . "AND id_claustro = $id_claustro";
        $ar = toba::db('resultados')->consultar($sql);
        return $ar[0]['porc'];
    }

    function get_total_mesas($id_claustro) {
        $sql = "SELECT count(id_mesa) as total FROM mesa "
                . "WHERE fecha = (SELECT max(fecha) FROM mesa) "
                . "AND ficticio = false "
                . "AND id_claustro = $id_claustro";
        $ar = toba::db('resultados')->consultar($sql);
        return $ar[0]['total'];
    }

    function get_de_usr($usuario) {
        $sql = "SELECT id_mesa FROM mesa WHERE autoridad LIKE '$usuario'";
        return toba::db('resultados')->consultar($sql);
    }

    function get_ultimas_descripciones($filtro = null) {
        if (isset($filtro)) {
            $where = "";
            if (isset($filtro['unidad_electoral']))
                $where = " AND t_ude.id_nro_ue = " . $filtro['unidad_electoral']['valor'];
            if (isset($filtro['sede']))
                $where .= " AND t_sde.id_sede = " . $filtro['sede']['valor'];
            if (isset($filtro['claustro']))
                $where .= " AND t_m.id_claustro = " . $filtro['claustro']['valor'];
            if (isset($filtro['tipo']))
                $where .= " AND t_t.id_tipo = " . $filtro['tipo']['valor'];
            if (isset($filtro['estado']))
                $where .= " AND t_m.estado = " . $filtro['estado']['valor'];

            $sql = "SELECT t_m.nro_mesa, 
                                t_m.id_mesa,
                                t_sde.sigla as de, 
                                t_ude.sigla as unidad_electoral,
                                t_e.descripcion as estado, 
                                t_m.id_claustro
                            FROM mesa t_m
                            LEFT JOIN estado t_e ON (t_e.id_estado = t_m.estado)
                            LEFT JOIN sede t_sde ON (t_sde.id_sede = t_m.id_sede)
                            LEFT JOIN unidad_electoral t_ude ON (t_ude.id_nro_ue = t_sde.id_ue)
                            WHERE t_m.fecha = (SELECT max(fecha) FROM mesa ) 
                            AND t_m.ficticio = false 
                         $where ORDER BY t_ude.id_nro_ue";

            return toba::db('resultados')->consultar($sql);
        }
        else {

            $sql = "SELECT id_acta, "
                    . "total_votos_blancos, "
                    . "total_votos_nulos, "
                    . "total_votos_recurridos,"
                    . "t_a.id_tipo,"
                    . "t_t.descripcion as tipo,"
                    . "de,"
                    . "para "
                    . "FROM acta as t_a "
                    . "LEFT JOIN tipo as t_t ON (t_t.id_tipo = t_a.id_tipo) 
                        LEFT JOIN mesa t_de ON (t_de.id_mesa = t_a.de)
                        LEFT JOIN mesa t_para ON (t_para.id_mesa = t_a.para)
                        WHERE t_de.fecha = (SELECT max(fecha) FROM mesa )"
                    . " AND t_para.fecha = (SELECT max(fecha) FROM mesa )"
                    . "ORDER BY id_acta";

            return toba::db('resultados')->consultar($sql);
        }
    }

}

?>
