<?php

require dirname(__FILE__)."/config.php";

if (isset($_GET["act"])) {
    if (!(isset($_COOKIE["bili-token"]))) {
        header("HTTP/1.1 200 OK");
        echo "Not allowed.";
        exit();
    }
    if (!(userEligibility($_COOKIE["bili-token"]))) {
        header("HTTP/1.1 403 Forbidden");
        echo "不可以";
        exit();
    }
    header("HTTP/1.1 200 OK");

    switch ($_GET["act"]) {
        case "getlevel":
            echo "localStorage.levelTime=Date.now()";
            break;
        case "expiretime":
            echo "localStorage.oauthTime=Date.now()";
            break;
        case "logout":
            setcookie("bili-token", "deleted", time() - 3600, "/", COOKIEDOMAIN, TRUE);
            echo "<script>if(parent != window)parent.postMessage('BiliPlus-Logout-Success', '*');</script>";
            break;
        default:
            header("HTTP/1.1 400 Bad Request");
            echo "不懂啊";
    }
    exit();
}

if (!(isset($_GET["access_key"]) && isset($_GET["mid"]) && isset($_GET["uname"]) && isset($_GET["sign"]))) {
    header("HTTP/1.1 400 Bad Request");
    echo "太少了";
    exit();
}

$accesskey = $_GET["access_key"];
$mid = $_GET["mid"];
$uname = $_GET["uname"];

if ($_GET["sign"] != md5("access_key=".$accesskey."&mid=".$mid."&uname=".urlencode($uname).APPSECRET)) {
    header("HTTP/1.1 401 Unauthorized");
    echo "别装了";
    exit();
}

if (!(userEligibility($accesskey))) {
    header("HTTP/1.1 403 Forbidden");
    echo "不可以";
    exit();
}

$response = file_get_contents("https://passport.bilibili.com/api/oauth?access_key=".$accesskey, FALSE);
$result = json_decode($response, TRUE);
if ($result["code"] != 0) {
    header("HTTP/1.1 500 Internal Server Error");
    echo "爆炸了";
    exit();
}

setcookie("bili-token", $accesskey, (int)$result["access_info"]["expires"], "/", COOKIEDOMAIN, TRUE);
header("HTTP/1.1 200 OK");
echo "<script>if(parent != window)parent.postMessage('BiliPlus-Login-Success', '*');</script>";
