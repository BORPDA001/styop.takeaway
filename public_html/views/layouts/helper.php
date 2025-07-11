<?php
$default_route = "";
if($_SERVER["HTTPS"] == "on"){
    $default_route = "https://";
}
if($_SERVER["SERVER_NAME"] AND $_SERVER["SERVER_PORT"]){
    $default_route.= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"]."/";
}
?>