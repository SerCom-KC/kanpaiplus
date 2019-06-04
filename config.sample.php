<?php
// 请将本文件改名为 config.php

define("APIENDPOINT", "https://api.bilibili.com"); // 如果你用到了特殊反代与 bilibili 通信的话可以修改此项
define("COOKIEDOMAIN", ".example.com"); // 按照你的域名对应修改
define("APPSECRET", "c2ed53a74eeefe3cf99fbd01d8c9c375"); // 应该是不用修改的

function userEligibility($token) {
    // 在这里放置用户使用资质检测的代码（否则所有人都可以使用你的服务器）
    // $token 即为 access_key
    // 使用 'return FALSE;' 拒绝用户
    return TRUE;
}
