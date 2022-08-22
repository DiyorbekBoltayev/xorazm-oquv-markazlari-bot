<?php

require_once "texts.php";
include 'Telegram.php';


function no_apostrof(string $satr = ""): string
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


if ($text == "/start") {
    $firstname = $telegram->FirstName();
    $lastname = $telegram->LastName();
    $username = $telegram->Username();
    if ($firstname != null) {
        $firstname = no_apostrof($firstname);
    }
    if ($lastname != null) {
        $lastname = no_apostrof($lastname);
    }
    $sql = "select * from users where chat_id='$chat_id'";
    $result = mysqli_query($conn, $sql);
    if ($result->num_rows == 0) {
        $sql = "insert into users (chat_id,firstname,lastname,username,page) values ('$chat_id','$firstname','$lastname','$username','')";
        $result = mysqli_query($conn, $sql);
    }

    chooseLanguage();
} else {
    switch (getPage($chat_id)) {
        case 'start':
            if ($text == "🇺🇿 O'zbek tili") {
                setLang($chat_id, 'uz');
                showMainPage();
            } elseif ($text == "🇷🇺 Русский язык") {
                setLang($chat_id, 'ru');
                showMainPage();
            } else {
                chooseButtons();
            }
            break;
        case 'main':
            switch ($text) {
                case "🏫 " . getTexts('btn_markaz_tanlash', $chat_id):
                    showDistricts();
                    break;
                case "📜 " . getTexts('btn_markazlar_royhati', $chat_id):
                    //TODO xd
                    break;
                case "🇺🇿♻️🇷🇺" . getTexts('btn_til', $chat_id):
                    changeLang($chat_id);
                    showMainPage();
                    break;
            }
            break;
        case 'districts':

                switch ($text) {
                    case "⬅️ " . getTexts('orqaga', $chat_id):
                    case "⏮ " . getTexts('menu', $chat_id):
                        showMainPage();
                        break;
                    default:
                        if (in_array(substr($text, 5), getDistricts($chat_id))) {
                            setDist($chat_id, substr($text, 5));
                            showSubjects();
                        } else {
                            chooseButtons();
                        }
                }

                break;
            case
                'subjects':
                switch ($text) {
                    case "⬅️ " . getTexts('orqaga', $chat_id):
                        showDistricts();
                        break;
                    case "⏮ " . getTexts('menu', $chat_id):
                        showMainPage();
                        break;
                    default:
                        if (in_array(substr($text, 5), getSubjects($chat_id))) {
                            setSubj($chat_id, substr($text, 5));
                        } else {
                            chooseButtons();
                        }
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

    function showMainPage()
    {
        global $telegram, $chat_id;
        setPage($chat_id, 'main');
        $text = getTexts('yonalish_tanlang', $chat_id);
        $text .= " 👇";
        $options = [
            [
                $telegram->buildKeyboardButton("🏫 " . getTexts('btn_markaz_tanlash', $chat_id)),
                $telegram->buildKeyboardButton("📜 " . getTexts('btn_markazlar_royhati', $chat_id))
            ],
            [$telegram->buildKeyboardButton("🇺🇿♻️🇷🇺" . getTexts('btn_til', $chat_id))]
        ];
        $keyboard = $telegram->buildKeyBoard($options, false, true);
        $content = [
            'chat_id' => $chat_id,
            'reply_markup' => $keyboard,
            'text' => $text
        ];
        $telegram->sendMessage($content);
    }

    function showDistricts()
    {
        global $chat_id;
        setPage($chat_id, 'districts');
        $text = getTexts('tuman_tanlang', $chat_id);
        $text .= " 👇";
        $tumanlar = getDistricts($chat_id);
        $icon = "🔰 ";
        sendTextWithKeyboard($tumanlar, $text, $icon);
    }

    function showSubjects()
    {
        global $chat_id;
        setPage($chat_id, 'subjects');
        $text = getTexts('fan_tanlang', $chat_id);
        $text .= " 👇";
        $fanlar = getSubjects($chat_id);
        $icon = "📚 ";
        sendTextWithKeyboard($fanlar, $text, $icon);
    }

    function sendTextWithKeyboard($buttons, $text, $icon)
    {
        global $telegram, $chat_id;
        $options = [];
        for ($i = 0; $i < count($buttons); $i += 2) {
            if ($i + 2 <= count($buttons)) {
                $options[] = [
                    $telegram->buildKeyboardButton($icon . $buttons[$i]),
                    $telegram->buildKeyboardButton($icon . $buttons[$i + 1])];
            }

        }
        if (count($buttons) % 2 == 1) {
            $options[] = [$telegram->buildKeyboardButton($icon . $buttons[count($buttons) - 1])];
        }
        $options[] = [
            $telegram->buildKeyboardButton("⬅️ " . getTexts('orqaga', $chat_id)),
            $telegram->buildKeyboardButton("⏮ " . getTexts('menu', $chat_id)),

        ];
        $keyboard = $telegram->buildKeyBoard($options, false, true);
        $content = [
            'chat_id' => $chat_id,
            'reply_markup' => $keyboard,
            'text' => $text
        ];
        $telegram->sendMessage($content);
    }

    function chooseButtons()
    {
        global $chat_id, $telegram;
        $content = [
            'chat_id' => $chat_id,
            'text' => " Iltimos quyidagi tugmalardan birini tanlang 👇 \nПожалуйста, выберите одну из кнопок ниже 👇"
        ];
        $telegram->sendMessage($content);
    }

    function sendMessage($text)
    {
        global $chat_id, $telegram;
        $content = [
            'chat_id' => $chat_id,
            'text' => $text
        ];
        $telegram->sendMessage($content);
    }

//$e_message = "";
//try {



//} catch (\Exception $e) {
//    $e_message .= $e->getMessage();
//    $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $e_message]);
//
//} catch (Throwable $e) {
//    $e_message .= $e->getMessage();
//    $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $e_message]);
//
//}

