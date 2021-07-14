<?php 


function validatePassword($xpwd) {
    $data = array("1", "");

    $noPass = array
        (
        "123456", "654321", "111111", "222222", "333333",
        "444444", "555555", "666666", "777777", "888888",
        "999999", "000000", "121212", "131313", "141414",
        "151515", "161616", "171717", "181818", "191919");

    if (in_array($xpwd, $noPass)) {
        $data = array("2", "La contraseña no es segura. Por favor ingrese otra contraseña.");
    } elseif (strlen($xpwd) < 6 || strlen($xpwd) > 20) {
        $data = array("3", "La contraseña debe tener una longitud entre 6 y 20 caracteres. Por favor corrija.");
    }

    return $data;
}


?>