<?php

  error_reporting(0);
  session_start();

  include_once('config.php');

  require_once(SP_DIR_CLASS . 'administracion/usuario.class.php');
  require_once(SP_DIR_COMPONENTES . '/xajax/xajax.inc.php');

if ($_REQUEST) {
    if (isset($_REQUEST['logout'])) {
        session_unset();
        session_destroy();
        session_regenerate_id(true);        
        header('Location: login.php');
        exit;
    }
}

$index = true;
if (isset($_SESSION['sys_usuario'])) {
    
    if (isset($_REQUEST)) {
        if (array_key_exists('xajax', $_REQUEST)) {
            $index = false;
        }
    }
    
    if ($index) {
       header('Location: index.php');
        exit;
    }
}

$xajax = new xajax("", "");
$xajax->decodeUTF8InputOn();


function loadSessiones($username){

    $dtaUsuario = new usuario();
      
    $data = $dtaUsuario->validarCuentaUser('3',$username);
    
    $_SESSION['sys_persona'] = $data['persona'];
    $_SESSION['sys_usuario'] = $username;
    $_SESSION['sys_nombres'] = $data['nombrecompleto'];
    $_SESSION['sys_numerodoc'] = $data['nrodoc'];
    $_SESSION['sys_codigo'] = $data['codigo'];
    $_SESSION['sys_auxiliar'] = $data['auxiliar'];

    /* los roles */
    $dataRoles = $dtaUsuario->validarCuentaUser('4', $_SESSION['sys_persona']);
    $_SESSION['sys_rol'] = $dataRoles['nomrol'];
    $_SESSION['sys_rolId'] = $dataRoles['rol'];


  
}


function validarUser($form) {

    $objResponse = new xajaxResponse("utf-8");
    $dtaUsuario = new usuario();

    $username = trim($form['username']);
    $password = trim($form['password']);  

    //$ldap = '0';

    if (trim($username) == "" || trim($password) == "") {

        $objResponse->addAlert('Debe ingresar el usuario y la contraseña correctamente.');

    } else {
        
        $ldap = 0;

        $parametroldap = $dtaUsuario->buscarvalorLdap('1','LDAPLGN');

        if($parametroldap["valor"] == '1'){ //Verfica si valor obtenido es igual a 1

            $dtaUsuario->validarCuentaUser('1',$username, $password, '');
        
        }else{ //Si valor es 0 se le asigna por defecto valor 1

            $ldap = '1';
        }


        if($ldap == '1'){


                $data = $dtaUsuario->validarCuentaUser('2', $username, $password);
            
            if ($dtaUsuario->getMsgErr() != '') {

                $objResponse->addAssign('msgUser-t', 'innerHTML', '<strong>Error.</strong>');
                $objResponse->addAssign('msgUser-msg', 'innerHTML', $dtaUsuario->getMsgErr());
                $objResponse->addAssign('msgUser', 'style.display', 'block');

            } else if (count($data) > 0) {
                    
                    $procede=1;

                    if($procede==1){

                            loadSessiones($username);
                            //$token = getToken();
                            $_SESSION['sys_session'] = '00000000000000300';

                            $objResponse->addRedirect(SP_INDEX);

                    }
              
            } else {
                $objResponse->addAssign('msgUser-t', 'innerHTML', '<strong>Cuenta Incorrecta</strong>');
                $objResponse->addAssign('msgUser-msg', 'innerHTML', 'El usuario o password que ha ingresado es incorrecto.');
                $objResponse->addAssign('msgUser', 'style.display', 'block');
            }

        }else{

            $objResponse->addAssign('msgUser-t', 'innerHTML', '<strong>Cuenta Incorrecta - LDAP</strong>');
            $objResponse->addAssign('msgUser-msg', 'innerHTML', 'La cuenta no esta disponible para este perfil.');
            $objResponse->addAssign('msgUser', 'style.display', 'block');
       }
        
    }

    return $objResponse;
}

$xajax->registerFunction("validarUser");
$xajax->processRequests();

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo SP_APP ?></title>
  
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  
  <link rel="stylesheet" href="<?php echo SP_DIR_CSS ?>/css/bootstrap.css">
  <link rel="stylesheet" href="<?php echo SP_DIR_CSS ?>/css/softpang.css">
  <link rel="stylesheet" href="<?php echo SP_DIR_COMPONENTES ?>/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo SP_DIR_CSS ?>/css/AdminLTE.css">

  <?php $xajax->printJavascript(SP_DIR_COMPONENTES . '/xajax/'); ?>
  <script type="text/javascript" src="js/functions.js" charset="utf-8"></script>

  <script type="text/javascript">

        function checkFormUser(){
            var u = document.getElementById('username').value;
            var k = document.getElementById('password').value;
            
            if(u.length==0 || k.length==0){
                alert('Ingrese los datos de la cuenta correctamente.');
                return false;
            }
            validarUser(xajax.getFormValues('frmlogin'));
        }
            window.setTimeout(function () {if(document.getElementById("msgUser")){document.getElementById("msgUser").style.display = "none"; }}, 2000);
    </script>

</head>
<body class="hold-transition login-page">

  <div class="login-box">

    <div id="msgUser" class="alert alert-error fade in" style="display:none; margin: 20px auto;">
      <div id="msgUser-t"></div>
      <div id="msgUser-msg"></div>
    </div>

    <div class="login-box-body">
      <p class="login-box-msg">Para acceder al sistema deberá ingresar un usuario y una contraseña valida.</p>

      <form id="frmlogin" name="frmlogin" action="javascript:void(0)" onSubmit="return(checkFormUser());">
        <div class="well-login">

            <div class="form-group has-feedback">
              <label>Usuario:</label>
              <input type="text" id="username" name="username" class="form-control" placeholder="Usuario">
              <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>

            <div class="form-group has-feedback">
              <label>Password:</label>
              <input type="password" id="password" name="password" class="form-control" placeholder="Password">
              <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>

            <div><p class="login-box-msg"><span></span></p></div>

            <div class="row">       
              <div class="col-lg-12">
                <button type="submit" class="btn btn-success btn-block btn-flat" style="background-image: inherit;">Aceptar</button>
              </div>
              
            </div>
        </div>

        <div id="footer_login">
              <a title="Click" href="#myModal" data-toggle="modal" class="tip-top">
              ¿Olvide mi contrase&ntilde;a?
              </a><br><br>
              &copy; 2021. Reservados Todos los Derechos.<br>
              <a href="<?php echo SP_COMPANY_PORTAL ?>"> SOFTPANG.</a>
          </div>
      </form>

    </div>

  </div>

  <div class="modal fade" id="myModal" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
           
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span></button>
                <h4>Recuperar contraseña</h4>
            </div>


            <div class="modal-body" id="myModalContenido">
                <form name="frm1" id="frm1" class="form-horizontal well" onSubmit="return false">
                    <fieldset>
                        <div class="control-group">
                            <div class="controls">
                                <p>Para recuperarla, ingresa tu usuario:</p>
                                <p><input type="text" class="form-control" name="txt_user" id="txt_user" value=""/></p>
                                <button class="btn btn-success login-btn" style="background-image: inherit;" type="submit" onClick="">Enviar</button>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
  </div>


<script src="<?php echo SP_DIR_CSS ?>/js/jquery.min.js"></script>
<script src="<?php echo SP_DIR_CSS ?>/js/bootstrap.min.js"></script>

</body>
</html>
