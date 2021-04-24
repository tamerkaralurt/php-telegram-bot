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

}

$telegram = new TelegramBot();
$telegram->setToken('1757316905:AAFPohIVReOYaM9I7mCwKlMajBz5wPedT1A');
$data = $telegram->getData();
if ($data->text == 'hello') {
    $telegram->sendMessage('SELAM');
}
preg_match('@\/(karzarar|durum)$@', $data->text, $match);
if (isset($match[1])) {
    if (!isset($match[2])) {
        $telegram->sendMessage("Lütfen $match[1] ile ilgili değerinizi yazın. Örneğin /$match[1] değeri");
        return false;
    }
    switch ($match[1]) {
        case 'karzarar':

            break;
        case 'durum':

            break;
        default:
            $telegram->sendMessage('Bilinmeyen Komut!');
            break;
    }
} else {
    $telegram->sendMessage("Lütfen doğru şekilde komut gönderin. Örneğin: /komut değeri");
}