<?php

require_once 'connect.php';
include 'Telegram.php';


function no_apostrof(string $satr=""): string
{
    for ($i = 0; $i < strlen($satr); $i++) {
        if ($satr[$i] == "'") {
            $satr[$i] = "`";
        }
    }
    return $satr;
}

$telegram = new Telegram('5601653365:AAGjIarcmGayfd54MBSvVf1Qznc2BoQlWPY');
$chat_id = $telegram->ChatID();
$text = $telegram->Text();

$e_message="";
try
{

if ($text == "/start") {
    $firstname = no_apostrof($telegram->FirstName());
    $lastname = no_apostrof($telegram->LastName());
    $username = $telegram->Username();
    $sql = "select * from users where chat_id='$chat_id'";
    $result = mysqli_query($conn, $sql);
    if ($result->num_rows == 0) {

        $sql = "insert into users (chat_id,firstname,lastname,username,page) values ('$chat_id','$firstname','$lastname','$username','')";
        $result = mysqli_query($conn, $sql);
    }

    chooseLanguage();
} else {
    switch (getPage($chat_id)){
        case 'start':
            if($text == "🇺🇿 O'zbek tili"){
                setLang($chat_id,'uz');
            }elseif ($text == "🇷🇺 Русский язык"){
                setLang($chat_id,'ru');
            }else{
                chooseButtons();
            }
            break;
    }
}

function chooseLanguage()
{
    global $telegram, $chat_id;
    setPage($chat_id, 'start');
    $options = [
        [$telegram->buildKeyboardButton("🇺🇿 O'zbek tili"), $telegram->buildKeyboardButton("🇷🇺 Русский язык")]

    ];
    $keyboard = $telegram->buildKeyBoard($options, true, true);
    $content = [
        'chat_id' => $chat_id,
        'reply_markup' => $keyboard,
        'text' => "Iltimos tilni tanlang: \nВыберите язык:"
    ];
    $telegram->sendMessage($content);
}

function chooseButtons(){
    global $chat_id,$telegram;
    $content=[
        'chat_id'=>$chat_id,
        'text'=>" Iltimos quyidagi tugmalardan birini tanlang 👇 \n Пожалуйста, выберите одну из кнопок ниже 👇"
    ];
    $telegram->sendMessage($content);
}

function setLang($chat_id, $lang)
{
    global $conn;
    $sql = "update users set language='$lang' where chat_id='$chat_id'";
    mysqli_query($conn, $sql);
}

function getLang($chat_id)
{
    global $conn;
    $sql = "select language from users where chat_id='$chat_id'";
    $result = mysqli_query($conn, $sql);
    $result = $result->fetch_assoc();
    return $result['language'];
}

function setPage($chat_id, $page)
{
    global $conn;
    $sql = "update users set page='$page' where chat_id='$chat_id'";
    mysqli_query($conn, $sql);
}

function getPage($chat_id)
{
    global $conn;
    $sql = "select page from users where chat_id='$chat_id'";
    $result = mysqli_query($conn, $sql);
    $result = $result->fetch_assoc();
    $s=$result['page'];
    if (is_null($s)){
        $s="";
    };
    return $s;
}

}
catch (\Exception $e)
{
    $e_message.=$e->getMessage();
    $telegram->sendMessage(['chat_id'=>$chat_id,'text'=>$e_message]);

}
catch (Throwable $e)
{
    $e_message.=$e->getMessage();
    $telegram->sendMessage(['chat_id'=>$chat_id,'text'=>$e_message]);

}
