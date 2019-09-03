<?php 
class TelegramAPI { 
    public $token;
    public $chatID_manafacture;
    public $chatID_tehpod;
    public $chatID_sale;
   
    function __construct()
        {
         $this -> token = "583056708:AAFzy7OX6VwV9SFllhMW9pUeY50MwAU89QI"; 
         $this -> token_support = "828383903:AAFJ5LQrGxt1qfTrqlv-TO_tLaFUj2UzjBg";
         $this -> chatID_manafacture = -249207066;
         $this -> chatID_tehpod = -232413504;
         $this -> chatID_sale = -240222867;
         $this -> chatID_test = -278080358;
        }
   
    public function sendNotify($departament,$message,$t_chat_id=0)
    {
       
        $botToken = $this ->token;
        if ($t_chat_id!=0) {$botToken =$this ->token_support; }
        
        switch ($departament) {
            case "manafacture":
                $chatId= $this -> chatID_manafacture;
                break;
            case "tehpod":
                $chatId= $this -> chatID_tehpod;
                break;
            case "sale":
                $chatId= $this -> chatID_sale;
                break;
            case "test":
                $chatId= $this -> chatID_test;
                break;
            case "client":
                $chatId=  $t_chat_id;
                break;    
        }
        
        $website="https://api.telegram.org/bot".$botToken;
         //Receiver Chat Id 
        $params=[
            'chat_id'=>$chatId,
            'text'=>$message,
        ];
        print_r($params);
        $ch = curl_init($website . '/sendMessage');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        return $this ->token;
    }
}

$telegramAPI = new TelegramAPI; 