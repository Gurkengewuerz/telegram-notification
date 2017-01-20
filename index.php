<?php
/*
 * @AUTHOR: Gurkengewuerz <admin@gurkengewuerz.de>
 * @DESCRIPTION: Telegram Notification API
 * @REVIEW: 20.01.2017
 *
 * LINKS:
 *   https://de.wikipedia.org/wiki/HTTP-Statuscode
 *   https://github.com/Eleirbag89/TelegramBotPHP
 *
 * EXAMPLES:
 *   ?key=d01a4456-a9aa-4eff-942c-ceb2a7b76832&msg=This+is+a+cool+test
 *   ?key=d01a4456-a9aa-4eff-942c-ceb2a7b76832&update
 */

include("Telegram.php");
include("config.php");
date_default_timezone_set('Europe/Berlin');
header("Content-Type: application/json");

global $json;
$json = array();
$json["error"] = "OK";

function exitProgram($code, $message)
{
    http_response_code($code);
    $json["error"] = $message;
    echo json_encode($json);
    die();
}

function hasPermission($key, $permission)
{
    global $CONF;
    if (!array_key_exists($key, $CONF["API_TOKENS"])) exitProgram(403, "Forbidden");
    if (!is_array($CONF["API_TOKENS"][$key])) exitProgram(403, "Forbidden");
    $value = $CONF["API_TOKENS"][$key];
    if (!array_key_exists("permission", $value)) exitProgram(403, "Forbidden");
    if (in_array($permission, $value["permission"])) return true;
    exitProgram(403, "Forbidden");
    return false;
}

function respond()
{
    global $json;
    die(json_encode($json));
}

$key = trim(filter_input(INPUT_GET, "key"));
if (!array_key_exists($key, $CONF["API_TOKENS"])) exitProgram(401, "Unauthorized");

$telegram = new Telegram($CONF["BOT_TOKEN"]);

if (isset($_GET["update"]) && hasPermission($key, "update")) {
    $json["messages"] = array();
    $req = $telegram->getUpdates();
    for ($i = 0; $i < $telegram->UpdateCount(); $i++) {
        $telegram->serveUpdate($i);
        $text = $telegram->Text();
        $chat_id = $telegram->ChatID();
        $json["messages"][$chat_id] = $text;
    }
    respond();
}

$msg = trim(filter_input(INPUT_GET, "msg"));
if (isset($_GET["msg"]) && hasPermission($key, "message")) {
    if (empty($msg)) exitProgram(400, "Bad Request");
    $chat_list = array();
    if (is_array($CONF["API_TOKENS"][$key]["chat_id"])) $chat_list = $CONF["API_TOKENS"][$key]["chat_id"];
    if (isset($_GET["chat"]) && hasPermission($key, "custom_chat")) {
        $chatid = trim(filter_input(INPUT_GET, "chat"));
        if (empty($chatid) || !(is_numeric($chatid))) exitProgram(400, "Bad Request");
        $chat_list = array($chatid);
    }
    foreach ($chat_list as $chat) {
        if (empty($chat)) continue;
        $content = array('chat_id' => $chat, 'text' => $msg);
        $telegram->sendMessage($content);
    }
    respond();
}

exitProgram(404, "Not Found");