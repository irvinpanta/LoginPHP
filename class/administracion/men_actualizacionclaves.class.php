<?php

require_once(SP_DIR_CLASS . 'connection.class.php');

class men_actualizacionclaves extends connectdb {
    
    function ConsultaActualizarClave($flag){
        $data = array();
       
        $query = "CALL pa_confClaveMantenimiento(
                        '".$flag."',
                        '',
                        '".$_SESSION['sys_persona']."'
          )";
        
        $result = parent::query($query);
       
        if(!isset($result['error'])){
              foreach ($result as $row) {
                    $data[] = $row;
              }			
        } else {
            $this->setMsgErr($result['error']);
        }
        return $data;
    }
}
?>