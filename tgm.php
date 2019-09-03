<?php 

$token = "583056708:AAFzy7OX6VwV9SFllhMW9pUeY50MwAU89QI"; 
$token_in = "8737fh2946h295:7shjsmu73ys:l49djhsk937djh"; 
   
    function sendMsg($chatId,$message)
    {
        $botToken = "583056708:AAFzy7OX6VwV9SFllhMW9pUeY50MwAU89QI"; 
        $website="https://api.telegram.org/bot".$botToken;
         //Receiver Chat Id 
        $params=[
            'chat_id'=>$chatId,
            'text'=>$message,
        ];
        $ch = curl_init($website . '/sendMessage');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        return $botToken;
    }
    

if (isset($_POST['message']) && $_POST['token']==$token_in) {
    $text = $_POST['message'];
    $chatId = $_POST['chatId'];
    sendMsg($chatId,$text);
}

