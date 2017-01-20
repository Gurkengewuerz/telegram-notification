<?php
/*
 * @AUTHOR: Gurkengewuerz <admin@gurkengewuerz.de>
 * @DESCRIPTION: Telegram Notification API
 * @REVIEW: 20.01.2017
 *
 * LINKS:
 *   https://codepen.io/corenominal/pen/rxOmMJ
 */

$CONF = array();
$CONF["BOT_TOKEN"] = "1361058:6AagsngNY4HrS8MrK8NvcMVcuSRf4kpZLp7";

/*
 * https://codepen.io/corenominal/pen/rxOmMJ
 *
 * PERMISSIONS:
 *   update, message, custom_chat
 *
 */
$CONF["API_TOKENS"] = array(
    "2227686f-d38..." => array(
        "permission" => array("update", "message", "custom_chat"),
        "chat_id" => array("-19739629")
    ),
    "f092165d-10a..." => array(
        "permission" => array("message", "custom_chat") // This key needs custom_chat because it has not a list of chats
    )
);