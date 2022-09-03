<?php

include 'Telegram.php';

$telegram = new Telegram('5520195616:AAGp7GKgJOsaJa2rI-u_Tj-UfwOA8DB_qy0');
$chat_id = $telegram->ChatID();
$text = $telegram->Text();
$telegram->sendMessage(['chat_id'=>$chat_id,'text'=>$text]);
echo "ob-havo";
