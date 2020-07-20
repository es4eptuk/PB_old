<?php
class TelegramAPI {
    public $status;
    public $token;
    public $token_support;
    public $chatID_manafacture;
    public $chatID_tehpod;
    public $chatID_sale;
    public $chatID_test;
    private $query;
    private $pdo;

    function __construct()
    {
        global $database_server, $database_user, $database_password, $dbase, $dbconnect;
        $dsn = "mysql:host=$database_server;dbname=$dbase;charset=utf8";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        //$this->pdo = new PDO($dsn, $database_user, $database_password, $opt);
        $this->pdo = &$dbconnect->pdo;
    }

    function init()
    {
        global $telegram_settings;

        $this -> status = $telegram_settings['status'];
        $this -> token = $telegram_settings['token'];
        $this -> token_support = $telegram_settings['token_support'];
        $this -> chatID_manafacture = $telegram_settings['chatID_manafacture'];
        $this -> chatID_tehpod = $telegram_settings['chatID_tehpod'];
        $this -> chatID_sale = $telegram_settings['chatID_sale'];
        $this -> chatID_test = $telegram_settings['chatID_test'];
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
            'parse_mode'=>'HTML'
        ];
        // print_r($params);
        if ($this -> status) {
            $ch = curl_init($website . '/sendMessage');
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $result = curl_exec($ch);
            curl_close($ch);
        }
        return $this ->token;
    }

    public function writeMessageDb($param) {
        $log = serialize($param);
        //$this->writeLog($log);
        $responseMinutes = 0;
        $responseSeconds = 0;
        $chatId = $param['chatid'];
        $author = $param['author'];
        $user = $this->userIsEmployee($author);
        $isEmployee = $user['isEmployee'];
        $userId = $user['id'];
        $message = $this->pdo->quote($param['message']);
        $date    = date("Y-m-d H:i:s");
        $title = $param['title'];
        $this->query  = "SELECT * FROM `bot_message` WHERE `chatId` LIKE '$chatId' ORDER BY createDate DESC LIMIT 1";
        $result = $this->pdo->query($this->query);
        $line = $result->fetch();
        $idLastMesssage = 0;
        $setNotify = false;
        if ($line){
            $idLastMesssage = $line['id'];
            if($line['isEmployee']) {
                $responseMinutes = 0;
            } else {
                $d1_ts = strtotime($date);
                $d2_ts = strtotime($line['createDate']);
                $seconds = abs($d1_ts - $d2_ts);
                $responseMinutes = floor($seconds / 60);
                $responseSeconds = floor($seconds);
            }
        }
        if($isEmployee){
            $this->query  = "UPDATE `bot_message` SET `notification` = '1' WHERE `chatId` = $chatId";
            $result2 = $this->pdo->query($this->query);
            $setNotify = true;
        }
        $curDateStr = date("d.m.Y");
        $isWeekend = $this->isWeekend($curDateStr);
        $isHoliday = $this->isHolidays($curDateStr);
        $isNight = $this->isNight($date);
        $this->query  = "INSERT INTO `bot_message` (`id`, `chatId`,  `author`, `chatTitle`, `isEmployee`, `userId`, `message`, `createDate`,`responseMinutes`, `responseSeconds`,`isWeekend`,`isHoliday`,`isNight`,`notification`,`violation`) VALUES (NULL, '$chatId', '$author', '$title','$isEmployee', '$userId', $message, '$date',$responseMinutes,$responseSeconds,'$isWeekend','$isHoliday','$isNight', '$setNotify', 0)";
        $this->sendNotify('client', $this->query,  123622748);

        $result = $this->pdo->query($this->query);


        return $result;
    }

    public function userIsEmployee($userId) {
        $isEmployee = false;
        $this->query  = "SELECT * FROM `users` WHERE `telegramId` = '$userId'";
        $result = $this->pdo->query($this->query);
        $number_of_rows = $result->fetchColumn();
        if ($number_of_rows>0) {$isEmployee = true;}
        $line = $result->fetch();
        $user['isEmployee'] = $isEmployee;
        $user['id'] = $number_of_rows;
        return $user;
    }

    public function isHolidays($date) {
        //на локалке убраны дни выходных из за пандемии
        $year = date('Y');
        if ($year == 2020) {
            $calendar = simplexml_load_file(PATCH_DIR.'/date/'.$year.'/calendar.xml');
        } else {
            $calendar = simplexml_load_file('http://xmlcalendar.ru/data/ru/'.$year.'/calendar.xml');
        }
        /*$calendar = simplexml_load_file('http://xmlcalendar.ru/data/ru/'.date('Y').'/calendar.xml');*/
        $calendar =  $calendar->days->day;
        foreach( $calendar as $day ){
            $d = (array)$day->attributes()->d;
            $d = $d[0];
            $d = substr($d, 3, 2).'.'.substr($d, 0, 2).'.'.date('Y');
            if( $day->attributes()->t == 1 ) $arHolidays[] = $d;
        }
        if (array_search($date, $arHolidays) !== false) {return true;} else {return false;}
    }

    public function isWeekend($date) {
        setlocale(LC_TIME, 'ru_RU.utf8');
        return (date('N', strtotime($date)) >= 6);
    }


    public function isNight($date) {
        $workPeriodStart = "09:00:00";
        $workPeriodStop = "21:00:00";
        $workPeriodStartWeekend = "12:00:00";
        $workPeriodStopWeekend = "21:00:00";
        $workPeriodStartDateTime = strtotime(date('Y-m-d')  ." ". $workPeriodStart);
        $workPeriodStopDateTime = strtotime(date('Y-m-d')  ." ". $workPeriodStop);
        $workPeriodStartWeekendDateTime = strtotime(date('Y-m-d')  ." ". $workPeriodStartWeekend);
        $workPeriodStopWeekendDateTime = strtotime(date('Y-m-d')  ." ". $workPeriodStopWeekend);
        $time   = strtotime($date);
        $isHight = false;
        if ($this->isHolidays($date) || $this->isWeekend($date)) {
            if($time > $workPeriodStopWeekendDateTime || $time < $workPeriodStartWeekendDateTime) $isHight = true;
        } else {
            if($time > $workPeriodStopDateTime || $time < $workPeriodStartDateTime) $isHight = true;
        }
        return $isHight;
    }

    public function getUnanswered() {
        $query  = "SELECT * FROM `bot_message` WHERE `notification` = 0 AND `isEmployee` = 0 AND `chatId` != '-399291922' GROUP BY `chatId`";
        $result = $this->pdo->query($query);
        $curDate    = date("Y-m-d H:i:s");
        $isHight = $this->isNight($curDate);
        $isViolation = false;
        $log = print_r('true', true);
        file_put_contents( 'log__unanswer.txt', $log . PHP_EOL, FILE_APPEND);
        while ($line = $result->fetch()) {
            $date_message = $line['createDate'];
            $idMessage = $line['id'];
            $chatId = $line['chatId'];
            $msgIsNight = $this->isNight($date_message);

/*
            $params = [];
            $params['time'] = date('Y-m-d H:i:s');
            $params['idMessage'] = $idMessage;
            $params['isHight'] = $isHight;
            $params['msgIsNight'] = $msgIsNight;
            $params['raznica'] = intval((strtotime($curDate) - strtotime($date_message))/60);
            $log = print_r($params, true);
            file_put_contents( 'log__unanswer.txt', $log . PHP_EOL, FILE_APPEND);
*/

            if(!$isHight) {
                $d1_ts = strtotime($curDate);
                $d2_ts = strtotime($date_message);
                $seconds = abs($d1_ts - $d2_ts);
                $responseMinutes = floor($seconds / 60);
                if($responseMinutes>15) {
                    //echo $responseMinutes." - ".$line['message']."<br>";
                    $icon         = '😡😡😡 ';
                    $comment      = "Внимание! Не отвеченные сообщения в чате: ";
                    $comment .= "- ".$line['chatTitle']."\r\n";
                    $telegram_str = $icon . " " . $comment;
                    $this->sendNotify('client', $telegram_str,  -232413504);
                    if (!$msgIsNight) {$isViolation = true;}
                    $query  = "UPDATE `bot_message` SET `notification` = '1' WHERE `chatId` = '$chatId'";
                    $result2 = $this->pdo->query($query);
                    $isViolation = ($isViolation) ? 1 : 0;
                    $query  = "UPDATE `bot_message` SET `violation` = $isViolation WHERE `id` = $idMessage";
                    $result2 = $this->pdo->query($query);
                } else {


                    if($responseMinutes>10) {
                        //echo $responseMinutes." - ".$line['message']."<br>";
                        $icon         = 'ℹ️ ';
                        $comment      = "Внимание! Ожидание ответа более 10 минут: ";
                        $comment .= "- ".$line['chatTitle']."\r\n";
                        $telegram_str = $icon . " " . $comment;
                        $this->sendNotify('client', $telegram_str,  -232413504);

                    }
                }


            }
        }
    }


    public function sendHello($chat_id) {
        $str = "Thank you for contacting Promobot Tech Support team. We are happy to help you with any robot-related issues you may be experiencing. Our working hours are <b>(GMT+5) 9am - 9 pm (weekdays) and 12 noon - 9pm (weekends). If you contact us outside of the normal business hours, we will get back to you as soon as possible.</b> Рады приветствовать вас в чате <b>технической</b> поддержки компании Промобот. Тут вы можете задавать вопросы связанные с <b>работой робота</b>. Мы постараемся оперативно их решать в <b>часы работы</b> службы. <b>(Будние дни с 7:00 до 19:00 МСК, Выходные дни с 10:00 до 19:00) МСК</b>.";
        $this->sendNotify('client', $str,  $chat_id);
    }

    public function writeLog($str) {
        $file = fopen('log_telegram.txt', 'a');
        fwrite($file, $str . "\n");
        fclose($file);
    }
}

