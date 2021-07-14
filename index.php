<?php

date_default_timezone_set('America/Lima');

error_reporting(0);
session_start();

include_once('config.php');
include_once('firewall.php');
include_once('modulos/functions.php');

require_once(SP_DIR_CLASS . 'administracion/men_actualizacionclaves.class.php');
require_once(SP_DIR_CLASS . 'administracion/usuario.class.php');


require_once(SP_DIR_COMPONENTES . '/xajax/xajax.inc.php');
$xajax = new xajax("");
$xajax->decodeUTF8InputOn();
$xajax->setCharEncoding('utf-8');


function _cambiarMiContrasena($clave, $claveActual = 'xxyyzz') {
  
  $rpta = new xajaxResponse("utf-8");

  $resultVal[0] = "1";

  if ($resultVal[0] == "1") {
      $resultVal = validatePassword($clave);
  }

  if ($resultVal[0] == "1") {

      $dta_usuario = new usuario();

      $result = $dta_usuario->actualizaContrasenia(1, $_SESSION['sys_persona'], $claveActual, $clave);


      if ($dta_usuario->getMsgErr() != '') {

        $rpta->addAlert($dta_usuario->getMsgErr());

      } else {

        if ($result == "1") {
                      
            $msg = '<p>Se actualizo su contrase&ntilde;a correctamente. Debe volver a iniciar sesi&oacute;n.</p>
                    <p>Espere mientras lo redireccionamos en <span id="reloadCounter">5</span> segundos o haga click <button class="btn btn-mini btn-primary" onclick="document.location.href=\'login.php?logout=0\';">aqu&iacute;</button> para ir directamente a la pagina de inicio de sesi&oacute;n.</p><br/>';
                      
            $rpta->addAssign("myModalmsg", "innerHTML", $msg);
                      
            $rpta->addScript('

                var reloadSec = 6;
                
                function reloadPage(){
                  
                  if(reloadSec>0){
                    reloadSec--;
                    document.getElementById("reloadCounter").innerHTML = reloadSec;
                  
                    if(reloadSec==0){
                      document.location.href = "login.php?logout=0";
                    }
                  }
                }

                setInterval(reloadPage, 1000);

                document.getElementById(\'btnClose\').disabled=true;

              ');

        } elseif ($result == "0") {

          $rpta->addAlert("Error.\n\nLa contraseña ingresada es incorrecta.");
                      //La contraseña es la misma, debe proporcionar otra constraseña.
        }

      }

  }else{
      $rpta->addAlert($resultVal[1]);  
  }

  return $rpta;
}




$xajax->registerFunction('_cambiarMiContrasena');
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
<link rel="stylesheet" href="<?php echo SP_DIR_CSS ?>/css/skin-blue.css">



<?php $xajax->printJavascript(SP_DIR_COMPONENTES . '/xajax/'); ?>

</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

<header class="main-header">
  
  <a href="<?php echo SP_INDEX ?>" class="logo">
    
    <span class="logo-mini"><b><?php echo SP_LOGO_MINI ?></b></span>
    <span class="logo-lg"><b><?php echo SP_LOGO ?></b></span>
  </a>
  
  <nav class="navbar navbar-static-top">
    
    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
      <span class="sr-only">Toggle navigation</span>
    </a>

    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">
        
        <li class="dropdown user user-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <img src="dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
            <span class="hidden-xs"><?php echo $_SESSION['sys_nombres'] ?></span>
          </a>

          <ul class="dropdown-menu">
      
            <li class="user-body">
              <div class="row">
                <div class="col-xs-12 text-center">
                  <form id="formrol" name="formrol" action="" method="POST" style="margin: 0px 0px 0px">
                      Usted está como:
                      <label><?php echo $_SESSION['sys_rol'] ?></label>
                  </form>
                </div>
              </div>
            </li>

            <li class="user-body">

              <div class="row">  

                <?php 

                  $dtaUsuario = new usuario();
                  $rptaDatos = $dtaUsuario->consultarMisDatosPersonales('1', $_SESSION['sys_rolId'], '');
                  $pwd = $rptaDatos[0]['pwd'];

                ?>

              <?php
                if($pwd=='1'){    
              ?>

                <div class="col-xs-12 text-center">
                  <a href="#myModal" data-toggle="modal"><i class="fa fa-unlock"></i><span>  Cambiar mi Contrase&ntilde;a</span></a>
                </div>

              <?php 
                } 
              ?>

              </div>
            </li>
            
            <li class="user-footer">
              <div class="pull-right">
                <a href="login.php?logout=0" class="btn btn-default btn-flat">Salir</a>
              </div>
            </li>
          </ul>
        </li>

        <li>
          <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
        </li>

      </ul>
    </div>
  </nav>
</header>


<aside class="main-sidebar">
  <section class="sidebar">

    <div class="user-panel">
      <div class="pull-left image">
        <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
      </div>
      <div class="pull-left info">
        <p><?php echo $_SESSION['sys_numerodoc'] ?></p>
        <a href="#"><i class="fa fa-circle text-success"></i> Bienvenido</a>
      </div>
    </div>

    <form class="sidebar-form">
      <div class="">
        <input type="text" name="q" class="form-control" placeholder="Menu" disabled="">
      </div>
    </form>

    <ul class="sidebar-menu" data-widget="tree">
      <li class="header">Menu</li>
      

      <li class="treeview">
        <a href="#">
          <i class="fa fa-dashboard"></i> <span>Menu</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><a href=""><i class="fa fa-caret-right"></i> Sub Menu</a></li>
        </ul>
      </li>

    </ul>
  </section>
</aside>

<div class="content-wrapper">

  <section class="content">

    <div class="box">
      <div class="box-body">

        <div id="container-fluid"> <!--Contenedor-->

          <div class="dashboard-widget">
            <div class="row-fluid">
              <ul class="breadcrumb">                            
                <li>M&oacute;dulos</li>
              </ul> 
            </div>
          </div>

          <div class="box box-danger">
            <div class="box-header with-border">

              <div class="box-tools pull-right">

                <span class="label label-success">Modulos disponibles:</span>
                <span class="label label-danger">2</span>
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>


                  <div id="container-secundario"></div>

              </div> 
            </div>
          </div>

            <?php 

                $dtaClaves = new men_actualizacionclaves();

                $dato = $dtaClaves->ConsultaActualizarClave('1');
                $resultado = $dato[0]['cambioclave']; //Obtiene valor de base de datos
            ?>

        </div> <!--Fin Contenedor-->
      </div>
    </div>

  </section>
  
</div>


<?php 

  if ($resultado == '1') { ?>

    <div class="modal fade" id="myModal" style="display: none;" data-backdrop="static">
      <div class="modal-dialog modal-md">
        <div class="modal-content">

          <div class="modal-header">
              <button type="button" id="btnClose" class="close" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span></button>
              <h4>Actualizar Contrase&ntilde;a de Acceso</h4>
          </div>

          <div class="modal-body" id="myModalmsg">
            <p>Para mayor seguridad, se recomienda emplear una combinaci&oacute;n entre letras y n&uacute;meros.<br/>Ingrese una nueva contrase&ntilde;a que tenga como m&aacute;ximo 12 caracteres.</p>

            <form name="frm1" id="frm1" class="form-horizontal well" onSubmit="return false">
              <fieldset>
                <div class="control-group">
                  <label class="control-label" for="txt_user">Usuario:</label>

                  <div class="controls">
                    <input type="text" class="form-control text-center" name="txt_user" id="txt_user" value="<?php echo $_SESSION['sys_usuario'] ?>" readonly/>
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="txt_clave">Nueva Contrase&ntilde;a:</label>
                  
                  <div class="controls">
                    <input type="password" class="form-control text-center" name="txt_clave" id="txt_clave" maxlength="12"/>
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="txt_clave2">Confirmar Contrase&ntilde;a:</label>
                  <div class="controls">
                    <input type="password" class="form-control text-center" name="txt_clave2" id="txt_clave2" maxlength="12"/>
                  </div>
                </div>
                <br>

                <div class="form-actions">
                  <button class="btn btn-primary" style="background-image: inherit;" type="submit" 
                    onClick="

                      var k1 = getElemento('txt_clave').value;
                      var k2 = getElemento('txt_clave2').value;
                                    
                      if (k1 == '' || k2 == '') {
                        alert('Por favor ingrese su nueva contraseña.');
                        return;
                      }

                      if (k1 != k2) {
                        
                        alert('La contraseña no coincide.')

                      } else {
                        xajax__cambiarMiContrasena(k1)
                      }">Actualizar Contrase&ntilde;a

                  </button>
                </div>

              </fieldset>
            </form>

          </div>
        
        </div>

      </div>
    </div>



  <?php 
  
    }

  ?>


<footer class="main-footer">
  <div class="pull-right hidden-xs">
    <b>Version</b> 2.4.18
  </div>
  <strong>Copyright &copy; 2014-2021 <a href="<?php echo SP_COMPANY_PORTAL ?>" target="_blank"><?php echo SP_COMPANY ?></a>.</strong> Todos los derechos reservaods.
</footer>

</div>


<script src="<?php echo SP_DIR_CSS ?>/js/jquery.min.js"></script>
<script src="<?php echo SP_DIR_CSS ?>/js/bootstrap.min.js"></script>
<script src="<?php echo SP_DIR_CSS ?>/js/adminlte.min.js"></script>


<script type="text/javascript">
$( document ).ready(function() {
  $('#myModal2').modal('toggle')
});
</script>

</body>
</html>
