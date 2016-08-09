<?php
class ci_principal extends resultados_ci
{
    protected $s__unidad;
    protected $s__fecha;
    //-----------------------------------------------------------------------------------
	//---- form_unidad -----------------------------------------------------------
	//-----------------------------------------------------------------------------------

    function conf()
	{
            if(!isset($this->s__unidad))
                $this->s__unidad = 1;
            $this->s__fecha = toba::memoria() -> get_parametro('param');
                           
	}
    
	function evt__form_unidad__modificacion($datos)
	{
            $this->s__unidad = $datos['id_nro_ue'];
            
        }

	

}

?>
