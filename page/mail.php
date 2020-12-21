<?php
$config['smtp_username'] = 'sklad@promo-bot.ru';  //Смените на адрес своего почтового ящика.
$config['smtp_port'] = '465'; // Порт работы.
$config['smtp_host'] =  'ssl://smtp.yandex.ru';  //сервер для отправки почты
$config['smtp_password'] = '1029384756';  //Измените пароль
$config['smtp_debug'] = true;  //Если Вы хотите видеть сообщения ошибок, укажите true вместо false
$config['smtp_charset'] = 'utf-8';	//кодировка сообщений. (windows-1251 или utf-8, итд)
$config['smtp_from'] = 'Promobot DB'; //Ваше имя - или имя Вашего сайта. Будет показывать при прочтении в поле "От кого"


class Mail
{

    function __construct()
    {

    }

    function init()
    {

    }

    public function send($to = '', $mail_to, $subject, $message, $headers = '', $my_config = [])
    {
        global $config;

        if ($my_config != []) {
            $config['smtp_username'] = (isset($my_config['smtp_username']) && !empty($my_config['smtp_username'])) ? $my_config['smtp_username'] : $config['smtp_username'];
            $config['smtp_port'] = (isset($my_config['smtp_port']) && !empty($my_config['smtp_port'])) ? $my_config['smtp_port'] : $config['smtp_port'];
            $config['smtp_host'] = (isset($my_config['smtp_host']) && !empty($my_config['smtp_host'])) ? $my_config['smtp_host'] : $config['smtp_host'];
            $config['smtp_password'] = (isset($my_config['smtp_password']) && !empty($my_config['smtp_password'])) ? $my_config['smtp_password'] : $config['smtp_password'];
            $config['smtp_debug'] = (isset($my_config['smtp_debug']) && !empty($my_config['smtp_debug'])) ? $my_config['smtp_debug'] : $config['smtp_debug'];
            $config['smtp_charset'] = (isset($my_config['smtp_charset']) && !empty($my_config['smtp_charset'])) ? $my_config['smtp_charset'] : $config['smtp_charset'];
            $config['smtp_from'] = (isset($my_config['smtp_from']) && !empty($my_config['smtp_from'])) ? $my_config['smtp_from'] : $config['smtp_from'];
        }

        $SEND = "Date: " . date("D, d M Y H:i:s") . " UT\r\n";
        $SEND .= 'Subject: =?' . $config['smtp_charset'] . '?B?' . base64_encode($subject) . "=?=\r\n";
        if ($headers) $SEND .= $headers . "\r\n\r\n";
        else {
            $SEND .= "Reply-To: " . $config['smtp_username'] . "\r\n";
            $SEND .= "To: \"=?" . $config['smtp_charset'] . "?B?" . base64_encode($to) . "=?=\" <$mail_to>\r\n";
            $SEND .= "MIME-Version: 1.0\r\n";
            $SEND .= "Content-Type: text/html; charset=\"" . $config['smtp_charset'] . "\"\r\n";
            $SEND .= "Content-Transfer-Encoding: 8bit\r\n";
            $SEND .= "From: \"=?" . $config['smtp_charset'] . "?B?" . base64_encode($config['smtp_from']) . "=?=\" <" . $config['smtp_username'] . ">\r\n";
            $SEND .= "X-Priority: 3\r\n\r\n";
        }
        $SEND .= $message . "\r\n";
        if (!$socket = fsockopen($config['smtp_host'], $config['smtp_port'], $errno, $errstr, 30)) {
            if ($config['smtp_debug']) echo $errno . "<br>" . $errstr;
            return false;
        }

        if (!$this->server_parse($socket, "220", __LINE__)) return false;

        fputs($socket, "HELO " . $config['smtp_host'] . "\r\n");
        if (!$this->server_parse($socket, "250", __LINE__)) {
            if ($config['smtp_debug']) echo '<p>Не могу отправить HELO!</p>';
            fclose($socket);
            return false;
        }
        fputs($socket, "AUTH LOGIN\r\n");
        if (!$this->server_parse($socket, "334", __LINE__)) {
            if ($config['smtp_debug']) echo '<p>Не могу найти ответ на запрос авторизаци.</p>';
            fclose($socket);
            return false;
        }
        fputs($socket, base64_encode($config['smtp_username']) . "\r\n");
        if (!$this->server_parse($socket, "334", __LINE__)) {
            if ($config['smtp_debug']) echo '<p>Логин авторизации не был принят сервером!</p>';
            fclose($socket);
            return false;
        }
        fputs($socket, base64_encode($config['smtp_password']) . "\r\n");
        if (!$this->server_parse($socket, "235", __LINE__)) {
            if ($config['smtp_debug']) echo '<p>Пароль не был принят сервером как верный! Ошибка авторизации!</p>';
            fclose($socket);
            return false;
        }
        fputs($socket, "MAIL FROM: <" . $config['smtp_username'] . ">\r\n");
        if (!$this->server_parse($socket, "250", __LINE__)) {
            if ($config['smtp_debug']) echo '<p>Не могу отправить комманду MAIL FROM: </p>';
            fclose($socket);
            return false;
        }
        fputs($socket, "RCPT TO: <" . $mail_to . ">\r\n");

        if (!$this->server_parse($socket, "250", __LINE__)) {
            if ($config['smtp_debug']) echo '<p>Не могу отправить комманду RCPT TO: </p>';
            fclose($socket);
            return false;
        }
        fputs($socket, "DATA\r\n");

        if (!$this->server_parse($socket, "354", __LINE__)) {
            if ($config['smtp_debug']) echo '<p>Не могу отправить комманду DATA</p>';
            fclose($socket);
            return false;
        }
        fputs($socket, $SEND . "\r\n.\r\n");

        if (!$this->server_parse($socket, "250", __LINE__)) {
            if ($config['smtp_debug']) echo '<p>Не смог отправить тело письма. Письмо не было отправленно!</p>';
            fclose($socket);
            return false;
        }
        fputs($socket, "QUIT\r\n");
        fclose($socket);
        return TRUE;
    }

    function server_parse($socket, $response, $line = __LINE__)
    {
        global $config;
        while (@substr($server_response, 3, 1) != ' ') {
            if (!($server_response = fgets($socket, 256))) {
                if ($config['smtp_debug']) echo "<p>Проблемы с отправкой почты!</p>$response<br>$line<br>";
                return false;
            }
        }
        if (!(substr($server_response, 0, 3) == $response)) {
            if ($config['smtp_debug']) echo "<p>Проблемы с отправкой почты!</p>$response<br>$line<br>";
            return false;
        }
        return true;
    }
}