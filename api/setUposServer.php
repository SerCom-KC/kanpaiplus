<?php

require dirname(dirname(__FILE__))."/config.php";

if (isset($_GET["server"]) && !empty($_GET["server"])) {
    setcookie("bili-upos", $_GET["server"], 0, "/", COOKIEDOMAIN, TRUE);
} else {
    setcookie("bili-upos", "deleted", time() - 3600, "/", COOKIEDOMAIN, TRUE);
}

header("HTTP/1.1 200 OK");
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://www.bilibili.com");
header("Access-Control-Allow-Credentials: true");
echo '{"code":0}';
