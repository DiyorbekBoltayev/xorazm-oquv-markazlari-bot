<?php
//require_once 'connect.php';
include 'Telegram.php';
 $telegram = new Telegram('5601653365:AAGjIarcmGayfd54MBSvVf1Qznc2BoQlWPY');
$chat_id=$telegram->ChatID();
$text=$telegram->Text();

if($text=="/start"){
    chooseLanguage();
}else{
    $telegram->sendMessage(['chat_id'=>$chat_id,'text'=>$text]);
}

function chooseLanguage(){
    global $telegram,$chat_id;
    $options=[
        [$telegram->buildKeyboardButton("🇺🇿 O'zbek tili")],
        [$telegram->buildKeyboardButton("🇷🇺 Русский язык")]
    ];
    $keyboard=$telegram->buildKeyBoard($options,true,true);
    $content=[
        'chat_id'=>$chat_id,
        'reply_markup'=>$keyboard,
        'text'=>"Iltimos tilni tanlang: \nВыберите язык:"
    ];
    $telegram->sendMessage($content);
}

