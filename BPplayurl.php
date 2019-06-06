<?php

require dirname(__FILE__)."/config.php";

function replaceUpOS($streams, $upos, $type) {
    foreach ($streams as &$stream) {
        $upos_found = FALSE;
        if(!isset($stream["backup_url"])) {continue;}
        if ($type === "dash") {
            $basestream = $stream["base_url"];
        } elseif ($type === "durl") {
            $basestream = $stream["url"];
        }
        if (!(substr($basestream, 0, 21 + strlen($upos)) === "http://upos-hz-mirror". $upos)) {
            foreach ($stream["backup_url"] as $backupstream) {
                if (substr($backupstream, 0, 21 + strlen($upos)) === "http://upos-hz-mirror". $upos) {
                    if ($type === "dash") {
                        $stream["base_url"] = $backupstream;
                        $stream["baseUrl"] = $backupstream;
                    } elseif ($type === "durl") {
                        $stream["url"] = $backupstream;
                    }
                    $upos_found = TRUE;
                    break;
                }
            }
        } else {
            $upos_found = TRUE;
        }
        if (!$upos_found && $upos != "") {return replaceUpOS($streams, "", $type);}
        unset($stream["backup_url"]);
        unset($stream["backupUrl"]);
    }
    return $streams;
}

if (!(isset($_COOKIE["bili-token"]) && (userEligibility($_COOKIE["bili-token"])))) {
    header("HTTP/1.1 200 OK");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: https://www.bilibili.com");
    header("Access-Control-Allow-Credentials: true");
    echo '{"code":-403,"message":"Restricted Area"}';
    exit();
}

if (isset($_GET["module"]) && $_GET["module"] === "bangumi") {
    $response = file_get_contents(APIENDPOINT."/pgc/player/web/playurl?access_key=".$_COOKIE["bili-token"]."&".$_SERVER["QUERY_STRING"], FALSE);
} else {
    $response = file_get_contents(APIENDPOINT."/x/player/playurl?access_key=".$_COOKIE["bili-token"]."&".$_SERVER["QUERY_STRING"], FALSE);
}
$result = json_decode($response, TRUE);
if ($result["code"] != 0) {
    if ($result["code"] === -10403) {
        $result["code"] = -403;
        header("HTTP/1.1 200 OK");
        header("Content-Type: application/json");
        header("Access-Control-Allow-Origin: https://www.bilibili.com");
        header("Access-Control-Allow-Credentials: true");
        echo json_encode($result, JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE);
        exit();
    } else {
        header("HTTP/1.1 500 Internal Server Error");
    }
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: https://www.bilibili.com");
    header("Access-Control-Allow-Credentials: true");
    
    echo json_encode($result, JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE);
    exit();
}

if (isset($_GET["module"]) && $_GET["module"] === "bangumi") {
    $data = $result["result"];
} else {
    $data = $result["data"];
}

$upos = "";
if (isset($_COOKIE["bili-upos"])) {$upos = $_COOKIE["bili-upos"];}
if (isset($data["dash"])) {
    $data["dash"]["video"] = replaceUpOS($data["dash"]["video"], $upos, "dash");
    $data["dash"]["audio"] = replaceUpOS($data["dash"]["audio"], $upos, "dash");
} elseif (isset($data["durl"])) {
    $data["durl"] = replaceUpOS($data["durl"], $upos, "durl");
}

ksort($data);
$data["flvjsType"] = "flv";

header("HTTP/1.1 200 OK");
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://www.bilibili.com");
header("Access-Control-Allow-Credentials: true");
echo json_encode($data, JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE);
