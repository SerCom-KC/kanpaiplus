<?php

require dirname(dirname(__FILE__))."/config.php";

if (isset($_GET["season"])) {
    if (!(isset($_COOKIE["bili-token"]) && (userEligibility($_COOKIE["bili-token"])))) {
      header("HTTP/1.1 403 Forbidden");
      echo "不可以";
      exit();
    }

    $ssid = $_GET["season"];
    $response = file_get_contents(APIENDPOINT."/pgc/view/web/season?season_id=".$ssid, FALSE);
    $result = json_decode($response, TRUE);

    if ($result["code"] != 0) {
        header("HTTP/1.1 500 Internal Server Error");
        echo "爆炸了";
        exit();
    }

    foreach ($result["result"]["episodes"] as &$episode) {
        $episode["av_id"] = $episode["aid"];
        $episode["episode_id"] = $episode["id"];
        $episode["danmaku"] = $episode["cid"];
    }

    header("HTTP/1.1 200 OK");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: https://www.bilibili.com");
    header("Access-Control-Allow-Credentials: true");
    echo json_encode($result, JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE);
} else {
    header("HTTP/1.1 400 Bad Request");
    echo "不懂啊";
}
