<?php

class TelegramBot
{
    const API_URL = 'https://api.telegram.org/bot';
    public $token;
    public $chatId;


    public function setToken($token)
    {
        $this->token = $token;
    }

    public function setWebhook($url)
    {
        return $this->request('setWebhook', [
            'url' => $url,
        ]);
    }

    public function sendMessage($message)
    {
        return $this->request('sendMessage', [
            'chat_id' => $this->chatId,
            'text' => $message,
        ]);
    }

    public function getData()
    {
        $data = json_decode(file_get_contents('php://input'));
        $this->chatId = $data->message->chat->id;
        return $data->message;
    }

    public function request($method, $posts)
    {
        $ch = curl_init();
        $url = self::API_URL . $this->token . '/' . $method;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($posts));
        curl_setopt($ch, CURLOPT_POST, 1);
        $headers = array();
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return $result;
    }

    public function getDotUsd(){
        $base = "https://api.btcturk.com";
        $now = strtotime(date("Y-m-d H:i:s"));
        $nowMinusTwo = strtotime(date("Y-m-d H:i:s")) - 60*60*2;
        $method = "/api/v2/ticker?pairSymbol=DOT_USDT&from=$nowMinusTwo&to=$now";
        $uri = $base.$method;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, "CURL_HTTP_VERSION_1_2");
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            print_r(curl_error($ch));
        }
        return json_decode($result);
    }
}

$telegram = new TelegramBot();
$telegram->setToken('1757316905:AAFPohIVReOYaM9I7mCwKlMajBz5wPedT1A');
$data = $telegram->getData();

// COMMANDS
if ($data->text == 'hello') {
    $telegram->sendMessage('SELAM');
}

if (isset($data->text)) {
    switch (trim($data->text, '/')) {
        case 'karzarar':
            $dotusd = $telegram->getDotUsd();
            $telegram->sendMessage("KAR ZARAR: Günlük 2 Saatlik: $dotusd->data->daily Günlük 2 Saatlik Yüzde: % $dotusd->data->dailyPercent");
            break;
        case 'durum':
            $telegram->sendMessage("DURUM");
            break;
        default:
            $telegram->sendMessage('Bilinmeyen Komut!');
            break;
    }
} else {
    $telegram->sendMessage("Lütfen doğru şekilde komut gönderin. Örneğin: /komut değeri");
}