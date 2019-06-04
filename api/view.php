<?php

require dirname(dirname(__FILE__))."/config.php";

if (isset($_GET["id"])) {
    if (!(isset($_COOKIE["bili-token"]) && (userEligibility($_COOKIE["bili-token"])))) {
        header("HTTP/1.1 403 Forbidden");
        echo "不可以";
        exit();
    }

    $aid = $_GET["id"];
    $response = file_get_contents(APIENDPOINT."/x/web-interface/view?aid=".$aid, FALSE);
    $result = json_decode($response, TRUE);

    if ($result["code"] != 0) {
        header("HTTP/1.1 200 OK");
        header("Content-Type: application/json");
        header("Access-Control-Allow-Origin: https://www.bilibili.com");
        header("Access-Control-Allow-Credentials: true");
        echo $response;
        exit();
    }

    $data = array(
        "title" => $result["data"]["title"],
        "aid" => $result["data"]["aid"],
        "list" => $result["data"]["pages"]
    );

    if (strpos($result["data"]["redirect_url"], "bangumi") !== FALSE) {
        preg_match('/ep[0-9]*/', $result["data"]["redirect_url"], $epid);
        $epid = $epid[0];
        $epid = str_replace("ep", "", $epid);
        $response = file_get_contents(APIENDPOINT."/pgc/view/web/season?ep_id=".$epid, FALSE);
        $result = json_decode($response, TRUE);
        if ($result["code"] != 0) {
            header("HTTP/1.1 500 Internal Server Error");
            echo "爆炸了";
            exit();
        }
        $data["bangumi"] = array(
            "season_id" => $result["result"]["season_id"],
            "title" => $result["result"]["title"],
            "cover" => $result["result"]["cover"],
            "is_finish" => $result["result"]["publish"]["is_finish"],
            "newest_ep_id" => (string)$result["result"]["new_ep"]["id"],
            "total_count" => (string)$result["result"]["total"]
        );
    }

    header("HTTP/1.1 200 OK");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: https://www.bilibili.com");
    header("Access-Control-Allow-Credentials: true");
    echo json_encode($data, JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE);
} else {
    header("HTTP/1.1 400 Bad Request");
    echo "不懂啊";
}
