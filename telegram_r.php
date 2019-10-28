<?php
include 'include/class.inc.php';
require_once __DIR__ . "/vendor/autoload.php";

    use Telegram\Bot\Api; 
   
    

    $telegram = new Api('828383903:AAFJ5LQrGxt1qfTrqlv-TO_tLaFUj2UzjBg'); //Устанавливаем токен, полученный у BotFather
    $result = $telegram -> getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользователя
    $log = serialize($result);
    $telegramAPI->writeLog($log);

   // $telegram->addCommand(Telegram\Bot\Commands\HelpCommand::class);
    // $telegram->addCommand(Telegram\Bot\Commands\StartCommand::class);
    //$telegram->commandsHandler(true);
    
    $text = mb_strtolower($result["message"]["text"]); //Текст сообщения
    
    $chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор чата
    $name = $result["message"]["from"]["username"]; //Юзернейм пользователя
    $user_id = $result["message"]["from"]["id"]; //Юзернейм пользователя
    $message_id = $result["message"]["message_id"]; //Юзернейм пользователя
   // $keyboard_start_ru = [["Создать обращение"],["Информация по роботу"],["Документация"]]; //Клавиатура
    $keyboard_lang_en = [["Русский"],["English"]]; //Клавиатура

    
    $keyboard_start_en = [["Robot Information"],["Documentation"]]; //Клавиатура
    $keyboard_start_ru = [["Информация по роботам"],["Документация"]]; //Клавиатура
    
    $keyboard_doc_ru = [["Инструкция по Promobot V4"],["Инструкция по лингвистической базе Promobot"],["Сервис Motion Studio"],["Сервис телеприсутствия"],["Назад"]]; //Клавиатура
    $keyboard_doc_en = [["Instructions for Promobot V4"],["Instructions on the linguistic database Promobot"],["Motion Studio Service"],["Telepresence Service"],["Back"]]; //Клавиатура
    $string = json_encode($result["message"]);
    $titleChat = $result["message"]["chat"]["title"];
    //$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $string ]);
    if(isset($result["message"]['new_chat_member'])) {$telegramAPI->sendHello($chat_id);}
    if(isset($result["message"]['audio']) || isset($result["message"]['document']) || isset($result["message"]['photo']) || isset($result["message"]['video']) || isset($result["message"]['sticker']) || isset($result["message"]['voice']) || isset($result["message"]['sticker'])) $text = "Media";
    if($text){
        $param['chatid'] = $chat_id;
        $param['author'] = $user_id;
        $param['title'] = $titleChat;
        $param['message'] = $text;
        if ($titleChat != null) {
            $telegramAPI->writeMessageDb($param);
        }
        if ($text == "/start") {
            $reply = "Hello! Choose your language: ";
            $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $keyboard_lang_en, 'resize_keyboard' => true, 'one_time_keyboard' => true , 'selective' => true]);
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup, 'reply_to_message_id' => $message_id]);
        }elseif ($text == "русский") {
            $reply = "Приветствуем вас в чате технической поддержки Promobot.";
            $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard_start_ru, 'resize_keyboard' => true, 'one_time_keyboard' => true , 'selective' => true ]);
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup, 'reply_to_message_id' => $message_id ]);
        }elseif ($text == "english") {
            $reply = "Welcome to the Promobot technical support chat.";
            $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard_start_en, 'resize_keyboard' => true, 'one_time_keyboard' => true, 'selective' => true  ]);
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup, 'reply_to_message_id' => $message_id ]);
        }
        
         elseif ($text == "документация") {
            $reply = "Какая документация вас интересует?";
            $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard_doc_ru, 'resize_keyboard' => true, 'one_time_keyboard' => true, 'selective' => true  ]);
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup, 'reply_to_message_id' => $message_id ]);
        }elseif ($text == "documentation") {
            $reply = "What documentation interests you?";
            $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard_doc_en, 'resize_keyboard' => true, 'one_time_keyboard' => true, 'selective' => true  ]);
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup, 'reply_to_message_id' => $message_id ]);
        }
        
        elseif ($text == "инструкция по promobot v4") {
            $url = "files/userguide_preview_rus_11_01_2019.pdf";
            $telegram->sendDocument([ 'chat_id' => $chat_id, 'document' => $url, 'caption' => "Инструкция Promobot V4", 'reply_to_message_id' => $message_id ]);
        }elseif ($text == "instructions for promobot v4") {
            $url = "files/userguide_publication_eng_09_07_18.pdf";
            $telegram->sendDocument([ 'chat_id' => $chat_id, 'document' => $url, 'caption' => "Instructions for Promobot V4", 'reply_to_message_id' => $message_id ]);
        }
        
        elseif ($text == "инструкция по лингвистической базе promobot") {
            $url = "files/Instruktsia_dlya_vladeltsa_po_ispolzovaniyu_veb-interfeysa_Promobot_11_02_2019.pdf";
            $telegram->sendDocument([ 'chat_id' => $chat_id, 'document' => $url, 'caption' => "Инструкция по лингвистической базе Promobot", 'reply_to_message_id' => $message_id ]);
        }elseif ($text == "instructions on the linguistic database promobot") {
            $url = "files/Instructions_for_using_the_web_interface_of_Promobot_Owner.pdf";
            $telegram->sendDocument([ 'chat_id' => $chat_id, 'document' => $url, 'caption' => "Instructions on the linguistic database Promobot", 'reply_to_message_id' => $message_id ]);
        }
        
        elseif ($text == "сервис motion studio") {
            $url = "files/MSSGuideRus.pdf";
            $telegram->sendDocument([ 'chat_id' => $chat_id, 'document' => $url, 'caption' => "Инструкция по сервису Motion Studio", 'reply_to_message_id' => $message_id ]);
        }elseif ($text == "motion studio service") {
            $url = "files/MSSGuideEng.pdf";
            $telegram->sendDocument([ 'chat_id' => $chat_id, 'document' => $url, 'caption' => "Instructions on the Motion Studio Service", 'reply_to_message_id' => $message_id ]);
        }
        
        elseif ($text == "сервис телеприсутствия") {
            $url = "files/Teleop_Rus.pdf";
            $telegram->sendDocument([ 'chat_id' => $chat_id, 'document' => $url, 'caption' => "Инструкция по сервису телеприсутствия", 'reply_to_message_id' => $message_id ]);
        }elseif ($text == "telepresence service") {
            $url = "files/Teleop_Eng.pdf";
            $telegram->sendDocument([ 'chat_id' => $chat_id, 'document' => $url, 'caption' => "Instructions on the Telepresence Service", 'reply_to_message_id' => $message_id ]);
        }
        
        
        
        elseif ($text == "информация по роботам") {
            $arr_id = $robots->telegram_get_id($chat_id);
            $string = json_encode($arr_id);
           // $telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => $string]);
            $text = '';
            foreach ($arr_id as $key => $value) {
                $number = str_pad($value['number'], 4, "0", STR_PAD_LEFT);
                $version =  $value['version'];
                $hostname = "promobotv".$version."_".$number;
                $info_robot = $robots->get_db_info($hostname);
                //$info_robot = json_encode($info[$hostname]);

                $text .= "<b>Promobot ".$version.".".$number."</b>\n";
                $text .= "<b>Заряд аккумулятора: </b>".$info_robot[$hostname]['battery']."%\n";
                $text .= "<b>Версия ПО: </b>".$info_robot[$hostname]['soft']."\n";
                $text .= "<b>Ping до сервера: </b>".$info_robot[$hostname]['ping']."мс\n";
                $text .= "<b>Статус репликации: </b>".$info_robot[$hostname]['repl_status']."\n";
                $ticket_on_robot = $tickets->get_tickets($value['id'], 0, 0, "update_date", "DESC", 0,0,0,"P",0,1);
                $text .= "<b>Количество открытых тикетов: ".count($ticket_on_robot)."</b>\n";
                foreach ($ticket_on_robot as $key2 => $value2) {
                    $status = $tickets->get_info_status($value2['status']); 
                    $finish_date = "";
                    if ($value2['finish_date']!="" && $value2['finish_date']!="0000-00-00" ) {
                        $finish_date = new DateTime($value2['finish_date']);
                        $finish_date = $finish_date->format('d.m.Y');
                        $finish_date = " (".$finish_date.")";
                    }
                    $text .= "\xE2\x9E\xA1 ".$value2['description']." - <i>". $status['title'].$finish_date."</i>\n";
                }
                $text .= "\n";
            }
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => $text, 'reply_to_message_id' => $message_id ]);
        }
        
         elseif ($text == "robot information") {
            $arr_id = $robots->telegram_get_id($chat_id);
            $text = '';
            foreach ($arr_id as $key => $value) {
                $number = str_pad($value['number'], 4, "0", STR_PAD_LEFT);
                $version =  $value['version'];
                $hostname = "promobotv".$version."_".$number;
                $info_robot = $robots->get_db_info($hostname);
                
               $text .= "<b>Promobot ".$version.".".$number."</b>\n";
                $text .= "<b>Battery: </b>".$info_robot[$hostname]['battery']."%\n";
                $text .= "<b>Soft version: </b>".$info_robot[$hostname]['soft']."\n";
                $text .= "<b>Ping to server: </b>".$info_robot[$hostname]['ping']."ms\n";
                $text .= "<b>Replication status: </b>".$info_robot[$hostname]['repl_status']."\n";
                $ticket_on_robot = $tickets->get_tickets($value['id'], 0, 0, "update_date", "DESC", 0,0,0,"P",0,1);
                $text .= "<b>Open tickets: ".count($ticket_on_robot)."</b>\n";
                foreach ($ticket_on_robot as $key2 => $value2) {
                    $status = $tickets->get_info_status($value2['status']); 
                    $finish_date = "";
                    if ($value2['finish_date']!="") {
                        $finish_date = new DateTime($value2['finish_date']);
                        $finish_date = $finish_date->format('d.m.Y');
                        $finish_date = " (".$finish_date.")";
                    }
                    $text .= "\xE2\x9E\xA1 ".$value2['description']." - <i>". $status['title'].$finish_date."</i>\n";
                }
                $text .= "\n";
            }
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => $text, 'reply_to_message_id' => $message_id ]);
        }
  
        elseif ($text == "назад") {
           $reply = ".";
           $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard_start_ru, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
        }
        
        elseif ($text == "back") {
            $reply = ".";
           $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard_start_en, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
        }
            
       elseif ($text == "/chatid") {
           $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $chat_id ]);
        }elseif ($text == "/username") {
           $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $name ]);
        }else{
        	//$reply = "По запросу \"<b>".$text."</b>\" ничего не найдено.";
        	//$telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => $reply ]);
        }
    }else{
    	//$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => "Отправьте текстовое сообщение." ]);
    }