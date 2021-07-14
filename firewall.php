<?php 

if (!isset($_SESSION['sys_usuario']) || !isset($_SESSION['sys_session'])) {
    if (isset($_REQUEST)) {
        if (array_key_exists('xajax', $_REQUEST)) {
            //header('Location:'.GL_DIR_WS_HTTP_APP.'error_access.php');  #SE COMENTO PORQUE SE USARA EL LOGIN AL TERMINAR SESION
        } else {
            header('Location: login.php?logout=1');
        }
    }
    else{
         header('Location: login.php?logout=1');
    }
}


 ?>