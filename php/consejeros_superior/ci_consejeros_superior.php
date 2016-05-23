<?php
class ci_consejeros_superior extends toba_ci
{
    protected $s__votos_e;
    protected $s__votos_g;
    protected $s__votos_nd;
    protected $s__votos_d;
    
    protected $s__total_emp;
    
    //---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro_superior_e(resultados_ei_cuadro $cuadro)
	{
            $this->dep('cuadro_superior_e')->colapsar();//No se muestra el cuadro en un principio
            
            $unidades = $this->dep('datos')->tabla('unidad_electoral')->get_descripciones_ponderados();
            
            //Cargar la cantidad de empadronados para el claustro estudiantes=3
            // en cada unidad
            $ar = $this->cargar_cant_empadronados($unidades, 3);
            
            //Ante ultima fila carga los votos totales de cada lista
            $pos = sizeof($ar);
            $ar[$pos]['sigla'] ="<span style='color:blue'>TOTAL</span>";
            $ar[$pos]['cant_empadronados'] = $this->s__total_emp;
            
            //Ultima fila carga los votos ponderados de cada lista
            $pos = sizeof($ar);
            $ar[$pos]['sigla'] = "<span style='color:red'>PONDERADOS</span>";
                        
            //Obtener las listas del claustro estudiantes=3
            $listas = $this->dep('datos')->tabla('lista_csuperior')->get_listas_actuales(3); 
            
            //Agregar las etiquetas de todas las listas
            $i = 1;
            foreach($listas as $lista){
                $l['clave'] = $lista['id_nro_lista'];
                $l['titulo'] = $lista['nombre'];
                $l['estilo'] = 'col-cuadro-resultados';
                $l['estilo_titulo'] = 'tit-cuadro-resultados';
                //$l['permitir_html'] = true;
                
                $grupo[$i] = $lista['id_nro_lista'];
                
                $columnas[$i] = $l;
                $this->dep('cuadro_superior_e')->agregar_columnas($columnas);
                
                //Cargar la cantidad de votos para cada lista de claustro estudiantes=3 
                //en cada unidad
                $ar = $this->cargar_cant_votos($lista['id_nro_lista'], $ar, 3);
                
                //Cargar los votos totales/ponderados para cada lista agregado como ante/última fila
                //para claustro estudiantes=3
                $ar[$pos-1][$lista['id_nro_lista']] = 0;
                $ar[$pos][$lista['id_nro_lista']] = 0;
                $ar = $this->cargar_votos_totales_ponderados($lista['id_nro_lista'], $ar, 3);
                
                $i++;
            }
            $this->dep('cuadro_superior_e')->set_grupo_columnas('Listas',$grupo);
              
            $this->s__votos_e = $ar;//Guardar los votos para el calculo dhondt
            
            //Agregar datos totales de blancos, nulos y recurridos
            $b['clave'] = 'total_votos_blancos';
            $b['titulo'] = 'Blancos';
            $b['estilo'] = 'col-cuadro-resultados';
            $bnr[0] = $b;
            
            $n['clave'] = 'total_votos_nulos';
            $n['titulo'] = 'Nulos';
            $n['estilo'] = 'col-cuadro-resultados';
            $bnr[1] = $n;
            
            $r['clave'] = 'total_votos_recurridos';
            $r['titulo'] = 'Recurridos';
            $r['estilo'] = 'col-cuadro-resultados';
            $bnr[2] = $r;
            
            $this->dep('cuadro_superior_e')->agregar_columnas($bnr);
            
            
            $ar = $this->cargar_cant_b_n_r($ar, 3);
            
            return $ar;
        }
        
        //Metodo responsable de cargar los votos blancos, nulos y recurridos de cada unidad electoral
        function cargar_cant_b_n_r($unidades, $id_claustro){
            $p = sizeof($unidades)-2;
            //Inicializo para realizar la sumatoria
            $unidades[$p]['total_votos_blancos'] = 0;
            $unidades[$p]['total_votos_nulos'] = 0;
            $unidades[$p]['total_votos_recurridos'] = 0;
            for($i=0; $i<$p; $i++){//Recorro las unidades
                //Agrega la cantidad de votos blancos,nulos y recurridos calculado en acta para cada unidad con claustro y tipo superior=1
                $ar = $this->dep('datos')->tabla('acta')->cant_b_n_r($unidades[$i]['id_nro_ue'], $id_claustro, 1);
                if(sizeof($ar)>0){
                    $unidades[$i]['total_votos_blancos'] = $ar[0]['blancos'];
                    $unidades[$i]['total_votos_nulos'] = $ar[0]['nulos'];
                    $unidades[$i]['total_votos_recurridos'] = $ar[0]['recurridos'];
                    
                    //Agrego en la anteultima fila la sumatoria total
                    
                    $unidades[$p]['total_votos_blancos'] += $ar[0]['blancos'];
                    $unidades[$p]['total_votos_nulos'] += $ar[0]['nulos'];
                    $unidades[$p]['total_votos_recurridos'] += $ar[0]['recurridos'];
                }
            }
            return $unidades;
        }
        
        //Metodo responsable de cargar la segunda columna con la cantidad de empadronados
        // en cada unidad electoral
        function cargar_cant_empadronados($unidades, $id_claustro){
            $p = sizeof($unidades)-2;
            $this->s__total_emp = 0;
            for($i=0; $i<sizeof($unidades); $i++){//Recorro las unidades
                //Agrega la cantidad de empadronados calculado en acta para cada unidad con claustro 
                $unidades[$i]['cant_empadronados'] = $this->dep('datos')->tabla('mesa')->cant_empadronados($unidades[$i]['id_nro_ue'], $id_claustro);
                $this->s__total_emp += $unidades[$i]['cant_empadronados'];
            }
            return $unidades;
        }
        
        function cargar_cant_votos($id_lista, $unidades, $id_claustro){
            for($i=0; $i<sizeof($unidades)-2; $i++){//Recorro las unidades
                //Agrega la cantidad de empadronados calculado en acta para cada unidad con claustro  y tipo 'superior'
                $unidades[$i][$id_lista] = $this->dep('datos')->tabla('voto_lista_csuperior')->cant_votos($id_lista, $unidades[$i]['id_nro_ue'], $id_claustro);                
            }
            return $unidades;
        }
        
        function cargar_votos_totales_ponderados($id_lista, $unidades, $id_claustro){
            $pos_total = sizeof($unidades) -2;//Fila que contiene los votos totales
            $pos_pond = sizeof($unidades)-1;//Fila que contiene los votos ponderados
            
            //Recorro las unidades exluyendo las dos últimas filas que tiene los votos totales y ponderados
            for($i=0; $i<$pos_total; $i++){
                if(isset($unidades[$i][$id_lista]) && isset($unidades[$i]['cant_empadronados'])){
                    //Suma el cociente entre cant de votos de la 
                    //lista en la UEn / cant empadronados del claustro en la UEn
                    $cociente = $unidades[$i][$id_lista]/$unidades[$i]['cant_empadronados'];
                    
                    $unidades[$pos_pond][$id_lista] += $cociente;
                }
                
                if(isset($unidades[$i][$id_lista])){
                    //Suma los votos 
                    $unidades[$pos_total][$id_lista] += $unidades[$i][$id_lista];
                }
            }
            
            return $unidades;
        }

	function evt__cuadro_superior_e__seleccion($datos)
	{
		
	}
        
        //-----------------------------------------------------------------------------------
	//---- cuadro_dhondt_e --------------------------------------------------------------
	//-----------------------------------------------------------------------------------
        function conf__cuadro_dhondt_e(resultados_ei_cuadro $cuadro)
	{
            $cargos = 10;//print_r($this->s__votos_e);
            //En $s__votos_e tengo todos los datos de los votos ponderados
            
            //Obtener las listas del claustro estudiantes=3
            $listas = $this->dep('datos')->tabla('lista_csuperior')->get_listas_actuales(3); 
            
            $ar = array();
            foreach($listas as $pos=>$lista){
                //Obtengo los votos ponderados * 10000 segun ordenanza
                $listas[$pos]['votos'] = $this->s__votos_e[sizeof($this->s__votos_e)-1][$listas[$pos]['id_nro_lista']] *10000; 
            
                //Calcula el cociente para cada cargo
                for($i=1; $i<=$cargos; $i++){
                    //  Cant votos ponderados / numero de cargo
                    $x = $listas[$pos]['votos'] / $i;
                    array_push($ar, $x);
                    $listas[$pos][$i] = $x;
                }
            }
            //Ordeno las listas en base a sus votos ponderados, descendente
            usort($listas, function($a, $b) {return $b['votos'] - $a['votos'];});
//          Ordeno el arreglo de cocientes para resaltar los mayores
            array_multisort($ar, SORT_DESC);
            
            //Resalta los resultados mayores
            for($i=0; $i<$cargos; $i++){//Recorro el arreglo de valores ordenados
                   
                foreach($listas as $pos=>$lista){
                    //Agrego la cant de escaños obtenidos para esta lista
                    // cant de votos obtenidos / menor cociente
                    if($ar[$cargos-1] == 0)//División por cero
                        $c = 0;
                    else
                        $c = $lista['votos'] / $ar[$cargos-1];
                    $listas[$pos]['final'] = floor($c);
                    $this->s__salida_excel[$pos] = $listas[$pos]; 
            
                    //Resalta los mayores
                    $p = array_search($ar[$i], $lista, TRUE);
                        if($p != null){//Encontro el valor en esta fila
                            if(strcmp($p, "votos")==0){//Encontro que esta en el campo 'votos' entonces hay que resaltar n°votos/1
                                $valor = "<span style='color:red'>".$listas[$pos][1]."</span>";
                                $listas[$pos][1] = $valor;
                            }
                            else{
                                $valor = "<span style='color:red'>".$listas[$pos][$p]."</span>";
                                $listas[$pos][$p] = $valor;
                            }  
                        }                        
                    }
                }
            $this->s__titulo_excel = 'Estudiantes';
            
            return $listas;
	}

        
	//-----------------------------------------------------------------------------------
	//---- cuadro_superior_g ------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_superior_g(resultados_ei_cuadro $cuadro)
	{
            $this->dep('cuadro_superior_g')->colapsar();//No se muestra el cuadro en un principio
            
            $unidades = $this->dep('datos')->tabla('unidad_electoral')->get_descripciones_ponderados();
            
            //Cargar la cantidad de empadronados para el claustro graduados=4
            // en cada unidad
            $ar = $this->cargar_cant_empadronados($unidades, 4);
            
            //Ante ultima fila carga los votos totales de cada lista
            $pos = sizeof($ar);
            $ar[$pos]['sigla'] ="<span style='color:blue'>TOTAL</span>";
            $ar[$pos]['cant_empadronados'] = $this->s__total_emp;
            
            //Ultima fila carga los votos ponderados de cada lista
            $pos = sizeof($ar);
            $ar[$pos]['sigla'] = "<span style='color:red'>PONDERADOS</span>";
                          
            //Obtener las listas del claustro graduados=4
            $listas = $this->dep('datos')->tabla('lista_csuperior')->get_listas_actuales(4); 
            
            //Agregar las etiquetas de todas las listas
            $i = 1;
            foreach($listas as $lista){
                $l['clave'] = $lista['id_nro_lista'];
                $l['titulo'] = $lista['nombre'];
                $l['estilo'] = 'col-cuadro-resultados';
                $l['estilo_titulo'] = 'tit-cuadro-resultados';
                //$l['permitir_html'] = true;
                
                $grupo[$i] = $lista['id_nro_lista'];
                
                $columnas[$i] = $l;
                $this->dep('cuadro_superior_g')->agregar_columnas($columnas);
                
                //Cargar la cantidad de votos para cada lista de claustro graduados=4 
                //en cada unidad
                $ar = $this->cargar_cant_votos($lista['id_nro_lista'], $ar, 4);
                
                //Cargar los votos totales/ponderados para cada lista agregado como ante/última fila
                //para claustro graduados=4
                $ar[$pos-1][$lista['id_nro_lista']] = 0;
                $ar[$pos][$lista['id_nro_lista']] = 0;
                $ar = $this->cargar_votos_totales_ponderados($lista['id_nro_lista'], $ar, 4);
                
                $i++;
            }
            
            if(isset($grupo))
                $this->dep('cuadro_superior_g')->set_grupo_columnas('Listas',$grupo);
              
            $this->s__votos_g = $ar;//Guardar los votos para el calculo dhondt
            
            //Agregar datos totales de blancos, nulos y recurridos
            $b['clave'] = 'total_votos_blancos';
            $b['titulo'] = 'Blancos';
            $b['estilo'] = 'col-cuadro-resultados';
            $bnr[0] = $b;
            
            $n['clave'] = 'total_votos_nulos';
            $n['titulo'] = 'Nulos';
            $n['estilo'] = 'col-cuadro-resultados';
            $bnr[1] = $n;
            
            $r['clave'] = 'total_votos_recurridos';
            $r['titulo'] = 'Recurridos';
            $r['estilo'] = 'col-cuadro-resultados';
            $bnr[2] = $r;
            
            $this->dep('cuadro_superior_g')->agregar_columnas($bnr);
            
            $p = sizeof($ar)-2;
            //Inicializo para realizar la sumatoria
            $ar[$p]['total_votos_blancos'] = 0;
            $ar[$p]['total_votos_nulos'] = 0;
            $ar[$p]['total_votos_recurridos'] = 0;
            $ar = $this->cargar_cant_b_n_r($ar, 4);
            
            return $ar;
        }

	function evt__cuadro_superior_g__seleccion($seleccion)
	{
	}
        
        //-----------------------------------------------------------------------------------
	//---- cuadro_dhondt_g --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_dhondt_g(resultados_ei_cuadro $cuadro)
	{
            $cargos = 4;//print_r($this->s__votos_e);
            //En $s__votos_e tengo todos los datos de los votos ponderados
            
            //Obtener las listas del claustro estudiantes=3
            $listas = $this->dep('datos')->tabla('lista_csuperior')->get_listas_actuales(4); 
            
            $ar = array();
            foreach($listas as $pos=>$lista){
                //Obtengo los votos ponderados * 10000 segun ordenanza
                $listas[$pos]['votos'] = $this->s__votos_g[sizeof($this->s__votos_g)-1][$listas[$pos]['id_nro_lista']] *10000; 
            
                //Calcula el cociente para cada cargo
                for($i=1; $i<=$cargos; $i++){
                    //  Cant votos ponderados / numero de cargo
                    $x = $listas[$pos]['votos'] / $i;
                    array_push($ar, $x);
                    $listas[$pos][$i] = $x;
                }
            }
            //Ordeno las listas en base a sus votos ponderados, descendente
            usort($listas, function($a, $b) {return $b['votos'] - $a['votos'];});
//          Ordeno el arreglo de cocientes para resaltar los mayores
            array_multisort($ar, SORT_DESC);
            
            //Resalta los resultados mayores
            for($i=0; $i<$cargos; $i++){//Recorro el arreglo de valores ordenados
                   
                foreach($listas as $pos=>$lista){
                    //Agrego la cant de escaños obtenidos para esta lista
                    // cant de votos obtenidos / menor cociente
                    if($ar[$cargos-1] == 0)//División por cero
                        $c = 0;
                    else
                        $c = $lista['votos'] / $ar[$cargos-1];
                    $listas[$pos]['final'] = floor($c);
                    $this->s__salida_excel[$pos] = $listas[$pos];            
            
                    //Resalta los mayores
                    $p = array_search($ar[$i], $lista, TRUE);
                        if($p != null){//Encontro el valor en esta fila
                            if(strcmp($p, "votos")==0){//Encontro que esta en el campo 'votos' entonces hay que resaltar n°votos/1
                                $valor = "<span style='color:red'>".$listas[$pos][1]."</span>";
                                $listas[$pos][1] = $valor;
                            }
                            else{
                                $valor = "<span style='color:red'>".$listas[$pos][$p]."</span>";
                                $listas[$pos][$p] = $valor;
                            }  
                        }                        
                    }
                }
                
            $this->s__titulo_excel = 'Graduados';
            return $listas;
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro_superior_nd -----------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_superior_nd(resultados_ei_cuadro $cuadro)
	{
            $this->dep('cuadro_superior_nd')->colapsar();//No se muestra el cuadro en un principio
            
            $unidades = $this->dep('datos')->tabla('unidad_electoral')->get_descripciones_ponderados();
            
            //Cargar la cantidad de empadronados para el claustro no docente = 1
            // en cada unidad
            $ar = $this->cargar_cant_empadronados($unidades, 1);
            
            //Ante ultima fila carga los votos totales de cada lista
            $pos = sizeof($ar);
            $ar[$pos]['sigla'] ="<span style='color:blue'>TOTAL</span>";
            $ar[$pos]['cant_empadronados'] = $this->s__total_emp;
            
            //Ultima fila carga los votos ponderados de cada lista
            $pos = sizeof($ar);
            $ar[$pos]['sigla'] = "<span style='color:red'>PONDERADOS</span>";
                       
            //Obtener las listas del claustro no docente = 1
            $listas = $this->dep('datos')->tabla('lista_csuperior')->get_listas_actuales(1); 
            
            //Agregar las etiquetas de todas las listas
            $i = 1;
            foreach($listas as $lista){
                $l['clave'] = $lista['id_nro_lista'];
                $l['titulo'] = $lista['nombre'];
                $l['estilo'] = 'col-cuadro-resultados';
                $l['estilo_titulo'] = 'tit-cuadro-resultados';
                //$l['permitir_html'] = true;
                
                $grupo[$i] = $lista['id_nro_lista'];
                
                $columnas[$i] = $l;
                $this->dep('cuadro_superior_nd')->agregar_columnas($columnas);
                
                //Cargar la cantidad de votos para cada lista de claustro no docente = 1 
                //en cada unidad
                $ar = $this->cargar_cant_votos($lista['id_nro_lista'], $ar, 1);
                
                //Cargar los votos totales/ponderados para cada lista agregado como ante/última fila
                //para claustro no docente = 1
                $ar[$pos-1][$lista['id_nro_lista']] = 0;
                $ar[$pos][$lista['id_nro_lista']] = 0;
                $ar = $this->cargar_votos_totales_ponderados($lista['id_nro_lista'], $ar, 1);
                
                $i++;
            }
            if(isset($grupo))
                $this->dep('cuadro_superior_nd')->set_grupo_columnas('Listas',$grupo);
              
            $this->s__votos_nd = $ar;//Guardar los votos para el calculo dhondt
            
            //Agregar datos totales de blancos, nulos y recurridos
            $b['clave'] = 'total_votos_blancos';
            $b['titulo'] = 'Blancos';
            $b['estilo'] = 'col-cuadro-resultados';
            $bnr[0] = $b;
            
            $n['clave'] = 'total_votos_nulos';
            $n['titulo'] = 'Nulos';
            $n['estilo'] = 'col-cuadro-resultados';
            $bnr[1] = $n;
            
            $r['clave'] = 'total_votos_recurridos';
            $r['titulo'] = 'Recurridos';
            $r['estilo'] = 'col-cuadro-resultados';
            $bnr[2] = $r;
            
            $this->dep('cuadro_superior_nd')->agregar_columnas($bnr);
            
            $p = sizeof($ar)-2;
            //Inicializo para realizar la sumatoria
            $ar[$p]['total_votos_blancos'] = 0;
            $ar[$p]['total_votos_nulos'] = 0;
            $ar[$p]['total_votos_recurridos'] = 0;
            $ar = $this->cargar_cant_b_n_r($ar, 1);
            
            return $ar;
	}

	function evt__cuadro_superior_nd__seleccion($seleccion)
	{
	}
	
	//-----------------------------------------------------------------------------------
	//---- cuadro_dhondt_nd -------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_dhondt_nd(resultados_ei_cuadro $cuadro)
	{
            $cargos = 10;//print_r($this->s__votos_e);
            //En $s__votos_e tengo todos los datos de los votos ponderados
            
            //Obtener las listas del claustro no docente = 1
            $listas = $this->dep('datos')->tabla('lista_csuperior')->get_listas_actuales(1); 
            
            $ar = array();
            foreach($listas as $pos=>$lista){
                //Obtengo los votos ponderados * 10000 segun ordenanza
                $listas[$pos]['votos'] = $this->s__votos_nd[sizeof($this->s__votos_nd)-1][$listas[$pos]['id_nro_lista']] *10000; 
            
                //Calcula el cociente para cada cargo
                for($i=1; $i<=$cargos; $i++){
                    //  Cant votos ponderados / numero de cargo
                    $x = $listas[$pos]['votos'] / $i;
                    array_push($ar, $x);
                    $listas[$pos][$i] = $x;
                }
            }
            //Ordeno las listas en base a sus votos ponderados, descendente
            usort($listas, function($a, $b) {return $b['votos'] - $a['votos'];});
//          Ordeno el arreglo de cocientes para resaltar los mayores
            array_multisort($ar, SORT_DESC);
            
            //Resalta los resultados mayores
            for($i=0; $i<$cargos; $i++){//Recorro el arreglo de valores ordenados
                   
                foreach($listas as $pos=>$lista){
                    //Agrego la cant de escaños obtenidos para esta lista
                    // cant de votos obtenidos / menor cociente
                    if($ar[$cargos-1] == 0)//División por cero
                        $c = 0;
                    else
                        $c = $lista['votos'] / $ar[$cargos-1];
                    $listas[$pos]['final'] = floor($c);
                    $this->s__salida_excel[$pos] = $listas[$pos];            
            
                    //Resalta los mayores
                    $p = array_search($ar[$i], $lista, TRUE);
                        if($p != null){//Encontro el valor en esta fila
                            if(strcmp($p, "votos")==0){//Encontro que esta en el campo 'votos' entonces hay que resaltar n°votos/1
                                $valor = "<span style='color:red'>".$listas[$pos][1]."</span>";
                                $listas[$pos][1] = $valor;
                            }
                            else{
                                $valor = "<span style='color:red'>".$listas[$pos][$p]."</span>";
                                $listas[$pos][$p] = $valor;
                            }  
                        }                        
                    }
                }
                
            $this->s__titulo_excel = 'No Docentes';
            return $listas;
	}
        
        //-----------------------------------------------------------------------------------
	//---- cuadro_superior_d ------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_superior_d(resultados_ei_cuadro $cuadro)
	{
            $cuadro->set_datos($this->dep('datos')->tabla('unidad_electoral')->get_descripciones());
	}

	function evt__cuadro_superior_d__seleccion($seleccion)
	{
	}

        //-----------------------------------------------------------------------------------
	//---- cuadro_dhondt_d --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_dhondt_d(resultados_ei_cuadro $cuadro)
	{
	}

        //-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf()
	{
            $this->pantalla()->tab('pant_docente')->ocultar();
	}

        //-----------------------------------------------------------------------------------
	//---- formulario que muestra datos de mesas enviadas, confirmadas y definitivas -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	//---- form_mesas_e -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_mesas_e(resultados_ei_formulario $form)
	{
            $cargadas = $this->dep('datos')->tabla('mesa')->get_cant_cargadas(3);
            $confirmadas = $this->dep('datos')->tabla('mesa')->get_cant_confirmadas(3);
            $definitivas = $this->dep('datos')->tabla('mesa')->get_cant_definitivas(3);
            
            $total = $this->dep('datos')->tabla('mesa')->get_total_mesas(3);
            
            $datos['cargadas'] = ($cargadas * 100 / $total);
            $datos['cargadas'] = round($datos['cargadas'], 2). " % ($cargadas de $total)";
            $datos['confirmadas'] = ($confirmadas * 100 / $total);
            $datos['confirmadas'] = round($datos['confirmadas'],2). " % ($confirmadas de $total)";
            $datos['definitivas'] = ($definitivas * 100 / $total);
            $datos['definitivas'] = round($datos['definitivas'],2). " % ($definitivas de $total)";
            
            return $datos;
	}

	//-----------------------------------------------------------------------------------
	//---- form_mesas_g -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_mesas_g(resultados_ei_formulario $form)
	{
            $cargadas = $this->dep('datos')->tabla('mesa')->get_cant_cargadas(4);
            $confirmadas = $this->dep('datos')->tabla('mesa')->get_cant_confirmadas(4);
            $definitivas = $this->dep('datos')->tabla('mesa')->get_cant_definitivas(4);
            
            $total = $this->dep('datos')->tabla('mesa')->get_total_mesas(4);
            
            $datos['cargadas'] = ($cargadas * 100 / $total);
            $datos['cargadas'] = round($datos['cargadas'], 2). " % ($cargadas de $total)";
            $datos['confirmadas'] = ($confirmadas * 100 / $total);
            $datos['confirmadas'] = round($datos['confirmadas'],2). " % ($confirmadas de $total)";
            $datos['definitivas'] = ($definitivas * 100 / $total);
            $datos['definitivas'] = round($datos['definitivas'],2). " % ($definitivas de $total)";
            
            return $datos;
	}

	//-----------------------------------------------------------------------------------
	//---- form_mesas_nd ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_mesas_nd(resultados_ei_formulario $form)
	{
            $cargadas = $this->dep('datos')->tabla('mesa')->get_cant_cargadas(1);
            $confirmadas = $this->dep('datos')->tabla('mesa')->get_cant_confirmadas(1);
            $definitivas = $this->dep('datos')->tabla('mesa')->get_cant_definitivas(1);
            
            $total = $this->dep('datos')->tabla('mesa')->get_total_mesas(1);
            
            $datos['cargadas'] = ($cargadas * 100 / $total);
            $datos['cargadas'] = round($datos['cargadas'], 2). " % ($cargadas de $total)";
            $datos['confirmadas'] = ($confirmadas * 100 / $total);
            $datos['confirmadas'] = round($datos['confirmadas'],2). " % ($confirmadas de $total)";
            $datos['definitivas'] = ($definitivas * 100 / $total);
            $datos['definitivas'] = round($datos['definitivas'],2). " % ($definitivas de $total)";
            
            return $datos;
	}
        
        //-----------------------------------------------------------------------------------
	//---- EXPORTACION EXCEL ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------
        function vista_excel(toba_vista_excel $salida){
            $salida->set_nombre_archivo("EscrutinioSuperior.xls");
            $excel = $salida->get_excel();
            
            
            $this->dependencia('cuadro_superior_e')->vista_excel($salida);
            $salida->separacion(3);
            $this->dependencia('cuadro_dhondt_e')->vista_excel($salida);
            $salida->set_hoja_nombre("Estudiantes");
            
            $salida->crear_hoja();
            $this->dependencia('cuadro_superior_g')->vista_excel($salida);
            $salida->separacion(3);
            $this->dependencia('cuadro_dhondt_g')->vista_excel($salida);
            $salida->set_hoja_nombre("Graduados");
            
            $salida->crear_hoja();
            $this->dependencia('cuadro_superior_nd')->vista_excel($salida);
            $salida->separacion(3);
            $this->dependencia('cuadro_dhondt_nd')->vista_excel($salida);
            $salida->set_hoja_nombre("No Docente");
//            $excel->getActiveSheet()->setTitle('Parte de Novedades');
//            $excel->getActiveSheet()->getStyle('A5')->getFill()->applyFromArray(array(
//        'type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array( 'rgb' => 'F28A8C' ) ));
        }
}
?>
