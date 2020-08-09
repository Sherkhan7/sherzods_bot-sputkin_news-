<?php
date_default_timezone_set('Asia/Tashkent');

require "menu/menu.php";
require "config.php";

use Telegram\Bot\Api;

$telegram = new Api('1323152064:AAENjtb_1bqm8vKPS_P5UV1QqgKUlRn1Od0');
//$conn_array = ['localhost', 'u83155_botdb', 'u83155_root', 'hpnotebook'];

$conn = connect('localhost', 'u83155_botdb', 'u83155_root', 'hpnotebook');
file_put_contents(__DIR__ . '/connection.txt' , json_encode($conn, JSON_PRETTY_PRINT));

$update = $telegram->getWebhookUpdates();
file_put_contents(__DIR__ . '/update.json' , json_encode($update, JSON_PRETTY_PRINT));

$chat_id = $update->getMessage()->getChat()->getId();
$message_id = $update->getMessage()->getMessageId();
$is_bot = $update->getMessage()->getFrom()->get('is_bot');
$first_name = $update->getMessage()->getChat()->getFirstName();
$last_name = $update->getMessage()->getChat()->getLastName();
$username = $update->getMessage()->getChat()->getUsername();
$text = $update->getMessage()->getText();
$date = $update->getMessage()->getDate();

$variables_arr = [
    'chat_id',
    'message_id',
    'is_bot',
    'first_name',
    'last_name',
    'username',
    'text',
    'date',
];

$variables = compact($variables_arr);
//file_put_contents(__DIR__ . '/variables.json', json_encode($variables, JSON_PRETTY_PRINT));
//$text_arr = explode(',', $text);
//$text_arr['weather_id'] = $text_arr[0];
//$text_arr['main'] = $text_arr[1];
//$text_arr['description'] = $text_arr[2];
//$text_arr['icon'] = $text_arr[3];
//unset($text_arr[0]);
//unset($text_arr[1]);
//unset($text_arr[2]);
//unset($text_arr[3]);
//
//$result = add_weather($conn, $text_arr);
//
//$telegram->sendMessage([
//    'chat_id' => $chat_id,
//    'text' => $result
//]);

if ($text == '/weather') {

    $reply_markup = $telegram->replyKeyboardMarkup([
        'keyboard' => $menu_1,
        'resize_keyboard' => true,
    ]);

    $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => 'Shaharni tanlang',
//    'reply_to_message_id' =>  $message_id,
        'reply_markup' => $reply_markup,
    ]);
}

if ($text == "Toshkent" || $text == "Denov" || $text == "Nukus" ||
    $text == "Samarqand" || $text == "Qarshi" || $text == "Buxoro" ||
    $text == "Boysun" || $text == "Yakkabog'") {

    if ($text == "Toshkent") $city_id = 1512569;
    if ($text == "Denov") $city_id = 1217474;
    if ($text == "Nukus") $city_id = 601294;
    if ($text == "Samarqand") $city_id = 1216265;
    if ($text == "Qarshi") $city_id = 1216311;
    if ($text == "Buxoro") $city_id = 1217662;
    if ($text == "Boysun") $city_id = 1217734;
    if ($text == "Yakkabog'") $city_id = 1346492;

    $url = 'https://api.openweathermap.org/data/2.5/weather';
    $options = [
        'id' => $city_id,
        'APPID' => '72071d64b00827b96c6f6e11d87ec995',
        'units' => 'metric',
        'lang' => 'eng'
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url . '?'. http_build_query($options));

    $weather = json_decode(curl_exec($ch), true);
    $weather_json = json_encode($weather, JSON_PRETTY_PRINT);
    file_put_contents(__DIR__ . '/weather.json', $weather_json);

    $weather_db = get_weather($conn, $weather['weather'][0]['id']);
    $reply = "__" . strtoupper($text) . " SHAHRI__" . ":\n*Hisoblangan vaqt:* " . date('d/m/Y, H:i:s', $weather['dt']) . "\n*Havo harorati:* " .
        $weather_db['icon'] . "\+" . round($weather['main']['temp']) . "\xE2\x84\x83\(" . $weather_db['description'] . "\)" .
    "\n*O\'xshaydi:* " . "\+". round($weather['main']['feels_like']) . "\xE2\x84\x83" .
    "\n*Minimum:* " . "\+" . round($weather['main']['temp_min']) . "\xE2\x84\x83" .
    "\n*Maksimum:* " . "\+" . round($weather['main']['temp_max']) . "\xE2\x84\x83".
    "\n*Namlik:* " . "\xF0\x9F\x92\xA7 ". $weather['main']['humidity'] . "\%".
    "\n*Shamol tezligi:* " . "\xF0\x9F\x92\xA8 ". round($weather['wind']['speed']) . " m/s".
    "\n*Bulutlilik:* " . "\xE2\x98\x81 ". $weather['clouds']['all'] . " \%".
    "\n*Havo bosimi:* " . round($weather['main']['pressure'] / 1.33322) . ' mmHg';;
    !is_null($weather['main']['sea_level']) ? $reply.="\n*Havo bosimi \(dengiz sathidan\)*:" . round($weather['main']['sea_level'] / 1.33322) . ' mmHg' : '';
    !is_null($weather['main']['grnd_level']) ? $reply.="\n*Havo bosimi \(yer sathidan\)*:" . round($weather['main']['grnd_level'] / 1.33322) . ' mmHg' : '';
    $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'parse_mode' => 'MarkdownV2']);

}

/****************************************************************
//const  TOKEN = '1038081989:AAHfMUWCUQnqXZX5e-JCztdHM-F0tprPYhA';
//const BASE_URL = 'https://api.telegram.org/bot' . TOKEN . '/';
//
//$update = file_get_contents("php://input");
//
//$update = json_decode(file_get_contents("php://input"), true);
//
//function sendRequest($method, $params = []) {
//    $url = BASE_URL . $method . '?' . http_build_query($params);
//
//    return json_decode(file_get_contents($url), true);
//}
//
//$chat_id = $update['message']['chat']['id'];
//$text = $update['message']['text'];
////
//$params = [
//    'chat_id' => $chat_id,
//    'text' => $text
//];
//
//sendRequest('sendmessage', $params);
*/