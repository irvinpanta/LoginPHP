<?php 


require_once(SP_DIR_CLASS . 'connection.class.php');


class usuario extends connectdb {


	function buscarvalorLdap($flag,$criterio) {
       $data = array();
       
       $query = "CALL pa_consultarValorLdap('$flag', '$criterio')";
       $result = parent::query($query);
       
        if(!isset($result['error'])){
              foreach ($result as $row) {                    
                $data['valor'] = $row['valor'];                   
              }         
        } else {
            $this->setMsgErr($result['error']);
        }
        return $data;
    }


    function validarCuentaUser($flag,$user, $password='',$tipousuario='') {
        $data = array();

        $sql = "CALL pa_loginValidarUsuario ('" . $flag . "','" . $user . "','" . $password . "','" . $tipousuario . "')";
        #echo $sql; exit;
        $result = parent::query($sql);
        if(!isset($result['error'])){
            foreach ($result as $row) {
                $data = $row;
            }
        }else{
            $this->setMsgErr($result['error']);
        }
 
        return $data;
    }

    //ACTUALIZAR CONTRASENIA DE ACCESO AL SISTEMA
    function actualizaContrasenia($flag, $usuario, $cadena, $texto) {
        
        $rpta = '';

        $sql = "CALL pa_admMantenimientoUsuario('" . $flag . "','" . $usuario . "','" . $cadena . "','" . $texto . "')";

        $result = parent::query($sql);

        if(!isset($result['error'])){
            foreach ($result as $row){
                $rpta = $row['result'];
            }
        }else{
             $this->setMsgErr($result['error']);
        }

        return $rpta;
    }

     function consultarMisDatosPersonales($flag, $perfil, $persona) {

        $data = array();

        $sql = "CALL pa_loginConsultarMisDatosPersonales ('" . $flag . "','" . $perfil . "','" . $persona . "')";

        $result = parent::query($sql); 
        if(!isset($result['error'])){
            foreach ($result as $row) {
                $data[] = $row;
            }
        }else{
            $this->setMsgErr($result['error']);
        }

        return $data;
    }

}






?>