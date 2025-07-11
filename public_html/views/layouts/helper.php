<?php
$default_route = "";

if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on") {
    $default_route = "https://";
} else {
    $default_route = "http://";
}

if (!empty($_SERVER["SERVER_NAME"])) {
    $default_route .= $_SERVER["SERVER_NAME"];
    if (
        ($_SERVER["HTTPS"] ?? '') === "on" && $_SERVER["SERVER_PORT"] != "443"
        ||
        ($_SERVER["HTTPS"] ?? '') !== "on" && $_SERVER["SERVER_PORT"] != "80"
    ) {
        $default_route .= ":" . $_SERVER["SERVER_PORT"];
    }
    $default_route .= "/styop/styop.takeaway/public_html/";
}


?>