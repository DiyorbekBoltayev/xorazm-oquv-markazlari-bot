<?php

include 'Telegram.php';

$telegram = new Telegram('5520195616:AAGp7GKgJOsaJa2rI-u_Tj-UfwOA8DB_qy0');
$chat_id = $telegram->ChatID();
$text = $telegram->Text();
$message=$telegram->getData();
$message=$message['message'];
if($text=='/start'){
    showStart();
}
elseif ($text=='⌨️ Shahar nomini kiritish'){
    askCity();
}
elseif (file_get_contents('obhavo.txt')=='ask'){
    findWeather();
}elseif ($message['location']['lalitude'] != ""){
    $request="https://api.openweathermap.org/data/2.5/weather?lat=".$message["location"]["latitude"]."&lon=".$message['location']["longitude"]."&appid=253468cdc02c66c0ccb7393c8d3ce4e7";
    $data=json_decode(file_get_contents($request),true);
    if($data['cod']==200){

        file_put_contents('obhavo.txt','topildi');
        $text=matnTayyor($data);
        $options=[

            [$telegram->buildKeyboardButton('🔻 Joylashuvdagi ob-havo',false,true)],
            [$telegram->buildKeyboardButton('⌨️ Shahar nomini kiritish')]
        ];
        $keyb=$telegram->buildKeyBoard($options,false,true);
        $content=[
            'chat_id'=>$chat_id,
            'reply_markup'=>$keyb,
            'text'=>$text
        ];
        $telegram->sendMessage($content);



    }else{
        $content=[
            'chat_id'=>$chat_id,
            'text'=>"😢 Bunday shahar topilmadi, Iltimos qayta urunib ko'ring"
        ];
        $telegram->sendMessage($content);
        askCity();
    }
}

function showStart(){
    global  $telegram,$chat_id;
    $text="Assalomu alaykum. Ob-havoni bilish uchun quyidagi tugmalardan birini tanlang 👇";
    $options=[

      [$telegram->buildKeyboardButton('🔻 Joylashuvdagi ob-havo',false,true)],
        [$telegram->buildKeyboardButton('⌨️ Shahar nomini kiritish')]
    ];
    $keyb=$telegram->buildKeyBoard($options,false,true);
    $content=[
        'chat_id'=>$chat_id,
        'reply_markup'=>$keyb,
        'text'=>$text
    ];
    $telegram->sendMessage($content);
}

function askCity(){
    global $telegram,$chat_id;
    file_put_contents('obhavo.txt','ask');
    $content=[
        'chat_id'=>$chat_id,
        'text'=>'🔆 Ob-havoni bilish uchun Shahar nomini yozib yuboring.'
    ];
    $telegram->sendMessage($content);
}
function findWeather(){
    global $telegram,$chat_id,$text;

    $request="https://api.openweathermap.org/data/2.5/weather?q=".$text."&appid=253468cdc02c66c0ccb7393c8d3ce4e7";
    $data=json_decode(file_get_contents($request),true);
    if($data['cod']==200){

        file_put_contents('obhavo.txt','topildi');
        $text=matnTayyor($data);
        $options=[

            [$telegram->buildKeyboardButton('🔻 Joylashuvdagi ob-havo',false,true)],
            [$telegram->buildKeyboardButton('⌨️ Shahar nomini kiritish')]
        ];
        $keyb=$telegram->buildKeyBoard($options,false,true);
        $content=[
            'chat_id'=>$chat_id,
            'reply_markup'=>$keyb,
            'text'=>$text
        ];
        $telegram->sendMessage($content);



    }else{
        $content=[
            'chat_id'=>$chat_id,
            'text'=>"😢 Bunday shahar topilmadi, Iltimos qayta urunib ko'ring"
        ];
        $telegram->sendMessage($content);
        askCity();
    }

}
function matnTayyor($data):string{
    $javob="💭  Ob-havo ma'lumoti:\n\n";
    $javob.="🌆  Shahar: ".$data['name']." \n\n";
    $javob.="🌡  Harorat: ".($data['main']["temp"]-273)." \n\n";
    $javob.="💨  Shamol tezligi: ".$data['wind']["speed"]." m/s \n\n";
    return $javob;
}
