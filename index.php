<?php
//require_once 'connect.php';
include 'Telegram.php';
 $telegram = new Telegram('5601653365:AAGjIarcmGayfd54MBSvVf1Qznc2BoQlWPY');
$chat_id=$telegram->ChatID();
$text=$telegram->Text();
$telegram->sendMessage(['chat_id'=>$chat_id,'text'=>$text]);
echo "alhamdulillah";
