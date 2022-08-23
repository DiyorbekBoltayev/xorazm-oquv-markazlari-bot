<?php

include 'Telegram.php';
require_once 'User.php';


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
$firstname = $telegram->FirstName();
$lastname = $telegram->LastName();
$username = $telegram->Username();
if ($firstname != null) {
    $firstname = no_apostrof($firstname);
}
if ($lastname != null) {
    $lastname = no_apostrof($lastname);
}
$e_message = "";
try {

$user=new User($chat_id,$firstname,$lastname,$username);
if ($text == "/start") {
    chooseLanguage();
} else {
    switch ($user->getPage()) {
        case 'start':
            if ($text == "🇺🇿 O'zbek tili") {
                $user->setLang('uz');
                showMainPage();
            } elseif ($text == "🇷🇺 Русский язык") {
                $user->setLang('ru');
                showMainPage();
            } else {
                chooseButtons();
            }
            break;
        case 'main':
            switch ($text) {
                case "🏫 " . $user->getTexts('btn_markaz_tanlash'):
                    showDistricts();
                    break;
                case "📜 " . $user->getTexts('btn_markazlar_royhati'):
                    //TODO xd
                    break;
                case "🇺🇿♻️🇷🇺" . $user->getTexts('btn_til'):
                    $user->changeLang();
                    showMainPage();
                    break;
            }
            break;
        case 'districts':

                switch ($text) {
                    case "⬅️ " . $user->getTexts('orqaga'):
                    case "⏮ " . $user->getTexts('menu'):
                        showMainPage();
                        break;
                    default:
                        if (in_array(substr($text, 5), $user->getDistricts())) {
                            $user->setDist( substr($text, 5));
                            showSubjects();
                        } else {
                            chooseButtons();
                        }
                }

                break;
            case
                'subjects':
                switch ($text) {
                    case "⬅️ " . $user->getTexts('orqaga'):
                        showDistricts();
                        break;
                    case "⏮ " . $user->getTexts('menu'):
                        showMainPage();
                        break;
                    default:
                        if (in_array(substr($text, 5), $user->getSubjects())) {
                            $user->setSubj( substr($text, 5));
                        } else {
                            chooseButtons();
                        }
                }
                break;
        }
    }

} catch (\Exception $e) {
    $e_message .= $e->getMessage();
    $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $e_message]);

} catch (Throwable $e) {
    $e_message .= $e->getMessage();
    $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $e_message]);

}

    function chooseLanguage()
    {
        global $telegram, $chat_id,$user;
        $user->setPage('start');
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
        global $telegram,$user,$chat_id;
        $user->setPage( 'main');
        $text = $user->getTexts('yonalish_tanlang');
        $text .= " 👇";
        $options = [
            [
                $telegram->buildKeyboardButton("🏫 " . $user->getTexts('btn_markaz_tanlash')),
                $telegram->buildKeyboardButton("📜 " . $user->getTexts('btn_markazlar_royhati'))
            ],
            [$telegram->buildKeyboardButton("🇺🇿♻️🇷🇺" . $user->getTexts('btn_til'))]
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
        global $chat_id,$user;
        $user->setPage( 'districts');
        $text = $user->getTexts('tuman_tanlang');
        $text .= " 👇";
        $tumanlar = $user->getDistricts();
        $icon = "🔰 ";
        sendTextWithKeyboard($tumanlar, $text, $icon);
    }

    function showSubjects()
    {
        global $chat_id,$user;
        $user->setPage( 'subjects');
        $text = $user->getTexts('fan_tanlang');
        $text .= " 👇";
        $fanlar = $user->getSubjects();
        $icon = "📚 ";
        sendTextWithKeyboard($fanlar, $text, $icon);
    }

    function sendTextWithKeyboard($buttons, $text, $icon)
    {
        global $telegram, $chat_id,$user;
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
            $telegram->buildKeyboardButton("⬅️ " . $user->getTexts('orqaga')),
            $telegram->buildKeyboardButton("⏮ " . $user->getTexts('menu')),

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






