<?php


class BitrixForm
{
    const IM_TYPE = [
        'OTHER' => 'OTHER',
        'ДРУГОЙ' => 'OTHER',
        'CALL' => 'PHONE',
        'ЗВОНОК' => 'PHONE',
        'TELEFON' => 'PHONE',
        'TELEFONE' => 'PHONE',
        'PHONE' => 'PHONE',
        'هاتف' => 'PHONE', //هاتف
        'VIBER' => 'VIBER',
        'TELEGRAM' => 'TELEGRAM',
        'VK' => 'VK',
        'INSTAGRAM' => 'INSTAGRAM',
        'SKYPE' => 'SKYPE',
        'FACEBOOK' => 'FACEBOOK',
    ];
    const EMAIL_USER = [
        1 => 'info@promo-bot.ru',
        2 => 'a.baidin@promo-bot.ru',
        3 => 'a.baidin@promo-bot.ru',
        4 => 'a.baidin@promo-bot.ru',
    ];
    const MAIL_CONFIG = [
        'smtp_username' => 'team@promo-bot.ru',       //Смените на адрес своего почтового ящика.
        'smtp_port' => '465',                         //Порт работы.
        'smtp_host' => 'ssl://smtp.yandex.ru',        //сервер для отправки почты
        'smtp_password' => 'ZPBZrvHpUmvKNpS0',        //Измените пароль
        'smtp_debug' => true,                         //Если Вы хотите видеть сообщения ошибок, укажите true вместо false
        'smtp_charset' => 'utf-8',	                  //кодировка сообщений. (windows-1251 или utf-8, итд)
        'smtp_from' => 'From Site (обработчик форм)', //Ваше имя - или имя Вашего сайта. Будет показывать при прочтении в поле "От кого"
    ];
    const HANDLER_TYPE = [
        1 => 'TILDA',
        2 => 'GITLAB',
        3 => 'LINKODIUM',
    ];
    const SCRIPT_TYPE = [
        1 => 'LEAD',
        2 => 'EMAIL_HR',
        3 => 'EMAIL_SUBSCRIPTION',
        4 => 'EMAIL_EVENT',
    ];
    const STATUS = [
        'ACTIVE' => 1,
        'NOTACTIVE' => 2,
    ];
    //надо удалять
    const DISTRIBUTION_BY_DIRECTION = [
        'No' => 0,
        'Devices' => 27,
        'Robots' => 26,
        'USA' => 28,
    ];
    //
    //надо удалять
    const DISTRIBUTION_BY_COUNTRY = [
        'No' => 0,
        'R&SNG' => 29,
        'World' => 30,
        'USA' => 651,
    ];
    //
    const SETTINGS = [
        'ASSIGNED' => 1097,
        'TYPE' => 1350,
        'CONNECT' => 1271,
        'URL' => 'https://team.promo-bot.ru/rest/1097/7kxtvn9s9u811vm1',
        'EMAIL' => 'a.baidin@promo-bot.ru',
    ];
    /*const __DIRECTION_BY = [
        27 => 33,
        26 => 16,
    ];*/
    const DIRECTION_BY = [
        27 => 1711,
        26 => 1710,
    ];
    const DIRECTION = [
        1710 => 'Robots',
        1711 => 'Devices',
    ];
    const COUNTRY = [
        29 => [36,51,54,61,68,105,124,139,221,224,225],
        651 => [37,],
    ];


    private $pdo;
    private $bitrixAPI;
    private $mail;
    public $params;
    public $_params;
    public $form;
    public $date;
    public $getListHandlers;
    public $getListScripts;
    public $getListStatuses;
    public $getListDirectionsBy; //надо удалять
    public $getListDirections;
    public $getListCountry;
    public $getListCountryBy; //надо удалять

    function __construct()
    {
        global $dbconnect;
        $this->pdo = &$dbconnect->pdo;
    }

    function init()
    {
        global $mail, $bitrixAPI;
        $this->bitrixAPI = $bitrixAPI;
        $this->mail = $mail;

        $this->params = [];
        $this->_params = [];
        $this->form = [];
        foreach (self::HANDLER_TYPE as $id => $name) {
            $this->getListHandlers[$id] = $name;
        }
        foreach (self::SCRIPT_TYPE as $id => $name) {
            $this->getListScripts[$id] = $name;
        }
        foreach (self::STATUS as $id => $name) {
            $this->getListStatuses[$name] = $id;
        }
        //надо удалять
        foreach (self::DISTRIBUTION_BY_DIRECTION as $id => $name) {
            $this->getListDirectionsBy[$name] = $id;
        }
        //
        //надо удалять
        foreach (self::DISTRIBUTION_BY_COUNTRY as $id => $name) {
            $this->getListCountryBy[$name] = $id;
        }
        //
        $this->getListCountry = $this->get_list_country();
        $this->getListDirections = $this->get_list_directions();
    }

    function resending($id_row)
    {
        $log = $this->get_log_form($id_row);
        $result = false;
        if ($log != null) {
            $params = json_decode($log['params'], false, 512, JSON_UNESCAPED_UNICODE);
            $form_id = $log['form_id'];
            $result = $this->action($form_id, (array)$params);
        }
        return $result;
    }

    function action($id, $params = [])
    {
        $this->_params = $params;
        $params = $this->prepare_keys_params($params);
        $this->form = $this->get_info_form($id);
        $this->date = date('d-m-Y H:i:s');
        if ($this->form == [] || $params == []) {
            return false;
        }
        $result = false;
        if ($this->form['status'] == self::STATUS['ACTIVE']) {
            switch ($this->form['handler']) {
                case 1:
                    $this->handler_tilda($params);
                    break;
                case 2:
                    $this->handler_gitlab($params);
                    break;
                case 3:
                    $this->handler_linkodium($params);
                    break;
            }
            if ($this->params != []) {
                switch ($this->form['script']) {
                    case 1:
                        $result = $this->script_add_lead();
                        break;
                    case 2:
                        $result = $this->script_send_email();
                        break;
                }
            }
        } else {
            $result = true;
        }
        return $result;
    }

    public function get_country_by_name($name)
    {
        $query = "SELECT * FROM `bitrix_country` WHERE `name_ru` LIKE '%$name%' OR `name_en` LIKE '%$name%'";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $info[] = $line;
        }
        return (isset($info)) ? $info['0'] : null;
    }

    public function get_country_by_code_t($code)
    {
        $code = substr($code, 1);
        $query = "SELECT * FROM `bitrix_country` WHERE `code_t` LIKE '$code'";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $info[] = $line;
        }
        return (isset($info)) ? $info['0'] : null;
    }

    public function get_list_country()
    {
        $query = "SELECT * FROM `bitrix_country`";
        $result = $this->pdo->query($query);
        $info[0] = [
            'key' => 0,
            'name_ru' => 'нет',
            'name_en' => 'no',
        ];
        while ($line = $result->fetch()) {
            $info[$line['id']] = $line;
        }
        return $info;
    }

    public function get_list_directions()
    {
        $info[0] = 'no';
        foreach (self::DIRECTION as $id => $name) {
            $info[$id] = $name;
        }
        return $info;
    }

    public function get_info_form($id)
    {
        $query = "SELECT * FROM `bitrix_form` WHERE `id`='$id'";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $info[] = $line;
        }
        return (isset($info)) ? $info['0'] : [];
    }

    public function get_info_form_by_key($key)
    {
        $query = "SELECT * FROM `bitrix_form` WHERE `key`='$key'";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $info[] = $line;
        }
        return (isset($info)) ? $info['0'] : null;
    }

    function get_list_forms()
    {
        $query = 'SELECT * FROM `bitrix_form`';
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $list[$line['id']] = $line;
        }
        return (isset($list)) ? $list : [];
    }

    function get_list_log_forms()
    {
        $query = 'SELECT *, `bitrix_form_log`.`id` AS `log_id` FROM `bitrix_form_log` JOIN `bitrix_form` ON `bitrix_form_log`.`form_id` = `bitrix_form`.`id` ORDER BY `bitrix_form_log`.`date` DESC LIMIT 1000';
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $list[] = $line;
        }
        return (isset($list)) ? $list : [];
    }

    function get_log_form($id_row)
    {
        $query = "SELECT * FROM `bitrix_form_log` WHERE `id` = $id_row";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $list[] = $line;
        }
        return (isset($list)) ? $list[0] : null;
    }

    function add_log_forms($id_form, $params, $result)
    {
        $date = date("Y-m-d H:i:s");;
        $query = "INSERT INTO `bitrix_form_log` (`id`, `form_id`, `params`, `result`, `date`) VALUES (NULL, $id_form, '$params', '$result', '$date')";
        $result = $this->pdo->query($query);
        return ($result) ? true : false;
    }

    function create_form($key, $url, $name, $handler, $script, $status, $directionBy, $countryBy, $direction, $country)
    {
        $query = "INSERT INTO `bitrix_form` (`id`, `key`, `url`, `name`, `handler`, `script`, `status`, `directionBy`, `countryBy`, `direction`, `country`) VALUES (NULL, '$key', '$url', '$name', '$handler', '$script', '$status', '$directionBy', '$countryBy', '$direction', '$country')";
        $result = $this->pdo->query($query);
        return ($result) ? true : false;
    }

    function update_form($id, $key, $url, $name, $handler, $script, $status, $directionBy, $countryBy, $direction, $country)
    {
        $query = "UPDATE `bitrix_form` SET `key` = '$key', `url` = '$url', `name` = '$name', `handler` = '$handler', `script` = '$script', `status` = '$status', `directionBy` = '$directionBy', `countryBy` = '$countryBy', `direction` = '$direction', `country` = '$country' WHERE `id` = $id";
        $result = $this->pdo->query($query);
        return ($result) ? true : false;
    }

    function delete_form($id)
    {
        $query = "DELETE FROM `bitrix_form` WHERE `id` = $id";
        $result = $this->pdo->query($query);
        return ($result) ? true : false;
    }

    function handler_tilda($params = [])
    {
        if ($params != []) {
            $comment = '';
            if (isset($params['textarea']) && !empty($params['textarea'])) {
                $comment .= "Textarea:\n".urldecode($params['textarea'])."<br>";
            }
            if (isset($params['text']) && !empty($params['text'])) {
                $comment .= "Text:\n".urldecode($params['text'])."<br>";
            }
            if (isset($params['comments']) && !empty($params['comments'])) {
                $comment .= "Comments:\n".urldecode($params['comments'])."<br>";
            }
            if (isset($params['comments_2']) && !empty($params['comments_2'])) {
                $comment .= "Comments_2:\n".urldecode($params['comments_2'])."<br>";
            }
            if (isset($params['comment_form']) && !empty($params['comment_form'])) {
                $comment .= "Comment_form:\n".urldecode($params['comment_form'])."<br>";
            }
            if (isset($params['comment']) && !empty($params['comment'])) {
                $comment .= "comment:\n".urldecode($params['comment'])."<br>";
            }
            if (isset($params['country']) && !empty($params['country'])) {
                $comment .= "Country:\n".urldecode($params['country'])."<br>";
            }
            $phone = [];
            $communication = [];
            if (isset($params['phone']) && !empty($params['phone'])) {
                $phone[] = [
                    "VALUE" => urldecode($params['phone']),
                    "VALUE_TYPE" => "OTHER",
                ];
                if (isset($params['communication']) && !empty($params['communication'])) {
                    $key_communication = mb_strtoupper($params['communication']);
                    $comment .= "Предпочтительный способ связи: ".urldecode($key_communication)."<br>"; //
                    $type = $this->getTypeIm($key_communication);
                    if ($type != self::IM_TYPE['CALL']) {
                        $communication[] = [
                            "VALUE" => urldecode($params['phone']),
                            "VALUE_TYPE" => $type,
                        ];
                        //$phone = [];
                    }
                }
            }
            if (isset($params['phone_mobile']) && !empty($params['phone_mobile'])) {
                $phone[] = [
                    "VALUE" => urldecode($params['phone_mobile']),
                    "VALUE_TYPE" => "MOBILE",
                ];
            }
            $mail = [];
            if (isset($params['email']) && !empty($params['email'])) {
                $mail[] = [
                    "VALUE" => urldecode($params['email']),
                    "VALUE_TYPE" => "OTHER",
                ];
            }
            $country = null;
            if (isset($params['country']) && !empty($params['country'])) {
                $db_country = $this->get_country_by_name(mb_strtolower($params['country']));
                if ($db_country != null) {
                    $country = $db_country['key'];
                }
            }
            if ($country == null && isset($params['country_code']) && !empty($params['country_code'])) {
                $db_country = $this->get_country_by_code_t($params['country_code']);
                if ($db_country != null) {
                    $country = $db_country['key'];
                }
            }

            $cookies = (isset($params['cookies']) && !empty($params['cookies'])) ? $this->prepare_cookies_params($params['cookies']) : [];
            $domen = explode( '/', $this->form['url'] );
            $this->params = [
                "TITLE" => $this->form['url'].' / '.$this->form['name'].' / '.$this->date,
                "NAME" => (isset($params['name']) && !empty($params['name'])) ? urldecode($params['name']) : "",
                "SECOND_NAME" => (isset($params['second_name']) && !empty($params['second_name'])) ? urldecode($params['second_name']) : "",
                "LAST_NAME" => (isset($params['last_name']) && !empty($params['last_name'])) ? urldecode($params['last_name']) : "",
                "COMPANY_TITLE" => (isset($params['company_title']) && !empty($params['company_title'])) ? urldecode($params['company_title']) : "",
                "STATUS_ID" => "NEW",
                "ADDRESS" => (isset($params['address']) && !empty($params['address'])) ? urldecode($params['address']) : "",
                "OPENED" => "Y",
                "ASSIGNED_BY_ID" => self::SETTINGS['ASSIGNED'],
                "COMMENTS" => $comment,
                "PHONE" => $phone,
                "IM" => $communication,
                "EMAIL" => $mail,
                "UTM_SOURCE" => (isset($params['utm_source']) && !empty($params['utm_source'])) ? urldecode($params['utm_source']) : "",
                "UTM_MEDIUM" => (isset($params['utm_medium']) && !empty($params['utm_medium'])) ? urldecode($params['utm_medium']) : "",
                "UTM_CAMPAIGN" => (isset($params['utm_campaign']) && !empty($params['utm_campaign'])) ? urldecode($params['utm_campaign']) : "",
                "UTM_CONTENT" => (isset($params['utm_content']) && !empty($params['utm_content'])) ? urldecode($params['utm_content']) : "",
                "UTM_TERM" => (isset($params['utm_term']) && !empty($params['utm_term'])) ? urldecode($params['utm_term']) : "",
                "UF_CRM_1608101741558" => TRUE,
                "UF_CRM_1608720560227" => self::SETTINGS['CONNECT'],
                "UF_CRM_1608875925351" => self::SETTINGS['TYPE'],
                //"UF_CRM_1617622300" => (array_key_exists('roistat_visit', $cookies)) ? $cookies['roistat_visit'] : "",
                "UF_CRM_1617622300" => (isset($cookies['roistat_visit']) && !empty($cookies['roistat_visit'])) ? $cookies['roistat_visit'] : "",
                "UF_CRM_1620729038" => (isset($params['timezone']) && !empty($params['timezone'])) ? urldecode($params['timezone']) : "",
                "UF_CRM_1622448857864" => $this->form['key'],
                "UF_CRM_1622449617181" => $domen[0],
                "UF_CRM_1622448780833" => 'https://'.$this->form['url'],
            ];

            if ($this->form['direction'] != 0) {
                $this->params['UF_CRM_1611301769946'] = $this->form['direction'];
            }
            if ($this->form['country'] != 0) {
                $this->params['UF_CRM_1607933607'] = $this->getListCountry[$this->form['country']]['key'];
            }
            if ($country != null) {
                $this->params['UF_CRM_1607933607'] = $country;
            }

            /*
            if ($this->form['direction'] != 0) {
                $this->params['UF_CRM_1607588088964'] = $this->form['direction'];
                if (array_key_exists($this->form['direction'], self::DIRECTION_BY)) {
                    //$this->params['UF_CRM_1607948122'] = self::__DIRECTION_BY[$this->form['direction']];
                    $this->params['UF_CRM_1611301769946'] = self::DIRECTION_BY[$this->form['direction']];
                }
            }
            if ($this->form['country'] != 0) {
                $this->params['UF_CRM_1607589066228'] = $this->form['country'];
            }
            if ($country != null) {
                $this->params['UF_CRM_1607933607'] = $country;
                $id_country = self::DISTRIBUTION_BY_COUNTRY['World'];
                foreach (self::COUNTRY as $id_distribution_by_country => $array) {
                    if (in_array($country, $array)) {
                        $id_country = $id_distribution_by_country;
                    }
                }
                if ($id_country == self::DISTRIBUTION_BY_COUNTRY['USA']) {
                    $this->params['UF_CRM_1607588088964'] = self::DISTRIBUTION_BY_DIRECTION['USA'];
                    $this->params['UF_CRM_1607589066228'] = self::DISTRIBUTION_BY_COUNTRY['USA'];
                } else {
                    $this->params['UF_CRM_1607589066228'] = $id_country;
                }
            }
            */
        }
    }

    function handler_gitlab($params = [])
    {
        if ($params != []) {
            $comment = '';
            if (isset($params['message']) && !empty($params['message'])) {
                $comment .= "Message:\n".urldecode($params['message'])."<br>";
            }
            if (isset($params['country']) && !empty($params['country'])) {
                $comment .= "Country:\n".urldecode($params['country'])."<br>";
            }
            if (isset($params['stations']) && !empty($params['stations'])) {
                $comment .= "Stations:\n".urldecode($params['stations'])."<br>";
            }
            $phone = [];
            if (isset($params['phone']) && !empty($params['phone'])) {
                $phone[] = [
                    "VALUE" => urldecode($params['phone']),
                    "VALUE_TYPE" => "OTHER",
                ];
            }
            $mail = [];
            if (isset($params['email']) && !empty($params['email'])) {
                $mail[] = [
                    "VALUE" => urldecode($params['email']),
                    "VALUE_TYPE" => "OTHER",
                ];
            }
            $country = null;
            if (isset($params['country']) && !empty($params['country'])) {
                $db_country = $this->get_country_by_name(mb_strtolower($params['country']));
                if ($db_country != null) {
                    $country = $db_country['key'];
                }
            }
            if ($country == null && isset($params['country_code']) && !empty($params['country_code'])) {
                $db_country = $this->get_country_by_code_t($params['country_code']);
                if ($db_country != null) {
                    $country = $db_country['key'];
                }
            }
            $url = '';
            if (isset($params['url']) && !empty($params['url'])) {
                $url = $params['url'];
                $utm_output = [];
                $parts = parse_url($params['url'], PHP_URL_QUERY);
                parse_str($parts, $utm_output);
            }
            $domen = explode( '/', $this->form['url'] );
            $this->params = [
                "TITLE" => $this->form['url'].' / '.$this->form['name'].' / '.$this->date,
                "NAME" => (isset($params['name']) && !empty($params['name'])) ? urldecode($params['name']) : "",
                "SECOND_NAME" => (isset($params['second_name']) && !empty($params['second_name'])) ? urldecode($params['second_name']) : "",
                "LAST_NAME" => (isset($params['last_name']) && !empty($params['last_name'])) ? urldecode($params['last_name']) : "",
                "COMPANY_TITLE" => (isset($params['company']) && !empty($params['company'])) ? urldecode($params['company']) : "",
                "STATUS_ID" => "NEW",
                "ADDRESS" => (isset($params['address']) && !empty($params['address'])) ? urldecode($params['address']) : "",
                "OPENED" => "Y",
                "ASSIGNED_BY_ID" => self::SETTINGS['ASSIGNED'],
                "COMMENTS" => $comment,
                "PHONE" => $phone,
                "EMAIL" => $mail,
                "UTM_SOURCE" => (isset($utm_output['utm_source']) && !empty($utm_output['utm_source'])) ? urldecode($utm_output['utm_source']) : "",
                "UTM_MEDIUM" => (isset($utm_output['utm_medium']) && !empty($utm_output['utm_medium'])) ? urldecode($utm_output['utm_medium']) : "",
                "UTM_CAMPAIGN" => (isset($utm_output['utm_campaign']) && !empty($utm_output['utm_campaign'])) ? urldecode($utm_output['utm_campaign']) : "",
                "UTM_CONTENT" => (isset($utm_output['utm_content']) && !empty($utm_output['utm_content'])) ? urldecode($utm_output['utm_content']) : "",
                "UTM_TERM" => (isset($utm_output['utm_term']) && !empty($utm_output['utm_term'])) ? urldecode($utm_output['utm_term']) : "",
                "UF_CRM_1608101741558" => TRUE,
                "UF_CRM_1608720560227" => self::SETTINGS['CONNECT'],
                "UF_CRM_1608875925351" => self::SETTINGS['TYPE'],
                "UF_CRM_1617622300" => (isset($params['roistat_visit']) && !empty($params['roistat_visit'])) ? $params['roistat_visit'] : "",
                "UF_CRM_1622448857864" => $this->form['key'],
                "UF_CRM_1622449617181" => $domen[0],
                "UF_CRM_1622448780833" => $url,
            ];

            if ($this->form['direction'] != 0) {
                $this->params['UF_CRM_1611301769946'] = $this->form['direction'];
            }
            if ($this->form['country'] != 0) {
                $this->params['UF_CRM_1607933607'] = $this->getListCountry[$this->form['country']]['key'];
            }
            if ($country != null) {
                $this->params['UF_CRM_1607933607'] = $country;
            }

            /*
            if ($this->form['direction'] != 0) {
                $this->params['UF_CRM_1607588088964'] = $this->form['direction'];
                if (array_key_exists($this->form['direction'], self::DIRECTION_BY)) {
                    //$this->params['UF_CRM_1607948122'] = self::__DIRECTION_BY[$this->form['direction']];
                    $this->params['UF_CRM_1611301769946'] = self::DIRECTION_BY[$this->form['direction']];
                }
            }
            if ($this->form['country'] != 0) {
                $this->params['UF_CRM_1607589066228'] = $this->form['country'];
            }
            if ($country != null) {
                $this->params['UF_CRM_1607933607'] = $country;
                $id_country = self::DISTRIBUTION_BY_COUNTRY['World'];
                foreach (self::COUNTRY as $id_distribution_by_country => $array) {
                    if (in_array($country, $array)) {
                        $id_country = $id_distribution_by_country;
                    }
                }
                if ($id_country == self::DISTRIBUTION_BY_COUNTRY['USA']) {
                    $this->params['UF_CRM_1607588088964'] = self::DISTRIBUTION_BY_DIRECTION['USA'];
                    $this->params['UF_CRM_1607589066228'] = self::DISTRIBUTION_BY_COUNTRY['USA'];
                } else {
                    $this->params['UF_CRM_1607589066228'] = $id_country;
                }
            }
            */
        }
    }

    function handler_linkodium($params = [])
    {
        if ($params != []) {
            $comment = '';
            if (isset($params['message']) && !empty($params['message'])) {
                $comment .= "Message:\n".urldecode($params['message'])."<br>";
            }
            if (isset($params['country']) && !empty($params['country'])) {
                $comment .= "Country:\n".urldecode($params['country'])."<br>";
            }
            if (isset($params['whatsapp']) && !empty($params['whatsapp'])) {
                $comment .= "Whatsapp:\n".urldecode($params['whatsapp'])."<br>";
            }
            if (isset($params['robot']) && !empty($params['robot'])) {
                $comment .= "Для чего планируется приобретение робота?:\n".urldecode($params['robot'])."<br>";
            }
            if (isset($params['howfind']) && !empty($params['howfind'])) {
                $comment .= "Откуда о нас узнали?:\n".urldecode($params['howfind'])."<br>";
            }
            if (isset($params['where']) && !empty($params['where'])) {
                $comment .= "Откуда о нас узнали?:\n".urldecode($params['where'])."<br>";
            }
            //не для CRM bitrix
            if (isset($params['position']) && !empty($params['position'])) {
                $comment .= "Должность:\n".urldecode($params['position'])."<br>";
            }
            if (isset($params['organization']) && !empty($params['organization'])) {
                $comment .= "Название компании:\n".urldecode($params['organization'])."<br>";
            }
            if (isset($params['city']) && !empty($params['city'])) {
                $comment .= "Город:\n".urldecode($params['city'])."<br>";
            }
            if (isset($params['website']) && !empty($params['website'])) {
                $comment .= "Website:\n".urldecode($params['website'])."<br>";
            }
            if (isset($params['product']) && !empty($params['product'])) {
                $comment .= "Product:\n".urldecode($params['product'])."<br>";
            }
            $phone = [];
            if (isset($params['phone']) && !empty($params['phone'])) {
                $phone[] = [
                    "VALUE" => urldecode($params['phone']),
                    "VALUE_TYPE" => "OTHER",
                ];
            }
            $mail = [];
            if (isset($params['email']) && !empty($params['email'])) {
                $mail[] = [
                    "VALUE" => urldecode($params['email']),
                    "VALUE_TYPE" => "OTHER",
                ];
            }
            $country = null;
            if (isset($params['country']) && !empty($params['country'])) {
                $db_country = $this->get_country_by_name(mb_strtolower($params['country']));
                if ($db_country != null) {
                    $country = $db_country['key'];
                }
            }
            if ($country == null && isset($params['country_code']) && !empty($params['country_code'])) {
                $db_country = $this->get_country_by_code_t($params['country_code']);
                if ($db_country != null) {
                    $country = $db_country['key'];
                }
            }
            $utm_output = [];
            if (isset($params['start_url']) && !empty($params['start_url'])) {
                $utm_output_start_url = [];
                $parts = parse_url($params['start_url'], PHP_URL_QUERY);
                parse_str($parts, $utm_output_start_url);
                if (isset($utm_output_start_url['utm_source']) && !empty($utm_output_start_url['utm_source'])) {
                    $utm_output['utm_source'] = $utm_output_start_url['utm_source'];
                }
                if (isset($utm_output_start_url['utm_medium']) && !empty($utm_output_start_url['utm_medium'])) {
                    $utm_output['utm_medium'] = $utm_output_start_url['utm_medium'];
                }
                if (isset($utm_output_start_url['utm_campaign']) && !empty($utm_output_start_url['utm_campaign'])) {
                    $utm_output['utm_campaign'] = $utm_output_start_url['utm_campaign'];
                }
                if (isset($utm_output_start_url['utm_content']) && !empty($utm_output_start_url['utm_content'])) {
                    $utm_output['utm_content'] = $utm_output_start_url['utm_content'];
                }
                if (isset($utm_output_start_url['utm_term']) && !empty($utm_output_start_url['utm_term'])) {
                    $utm_output['utm_term'] = $utm_output_start_url['utm_term'];
                }
            }
            $url = '';
            if (isset($params['order_url']) && !empty($params['order_url'])) {
                $url = $params['order_url'];
                $utm_output_order_url = [];
                $parts = parse_url($params['order_url'], PHP_URL_QUERY);
                parse_str($parts, $utm_output_order_url);
                if (isset($utm_output_order_url['utm_source']) && !empty($utm_output_order_url['utm_source']) && !(isset($utm_output['utm_source']) && !empty($utm_output['utm_source']))) {
                    $utm_output['utm_source'] = $utm_output_order_url['utm_source'];
                }
                if (isset($utm_output_order_url['utm_medium']) && !empty($utm_output_order_url['utm_medium']) && !(isset($utm_output['utm_medium']) && !empty($utm_output['utm_medium']))) {
                    $utm_output['utm_medium'] = $utm_output_order_url['utm_medium'];
                }
                if (isset($utm_output_order_url['utm_campaign']) && !empty($utm_output_order_url['utm_campaign']) && !(isset($utm_output['utm_campaign']) && !empty($utm_output['utm_campaign']))) {
                    $utm_output['utm_campaign'] = $utm_output_order_url['utm_campaign'];
                }
                if (isset($utm_output_order_url['utm_content']) && !empty($utm_output_order_url['utm_content']) && !(isset($utm_output['utm_content']) && !empty($utm_output['utm_content']))) {
                    $utm_output['utm_content'] = $utm_output_order_url['utm_content'];
                }
                if (isset($utm_output_order_url['utm_term']) && !empty($utm_output_order_url['utm_term']) && !(isset($utm_output['utm_term']) && !empty($utm_output['utm_term']))) {
                    $utm_output['utm_term'] = $utm_output_order_url['utm_term'];
                }
            }
            if (isset($params['submit_url']) && !empty($params['submit_url'])) {
                if ($url == '') {
                    $url = $params['submit_url'];
                }
                $utm_output_submit_url = [];
                $parts = parse_url($params['submit_url'], PHP_URL_QUERY);
                parse_str($parts, $utm_output_submit_url);
                if (isset($utm_output_submit_url['utm_source']) && !empty($utm_output_submit_url['utm_source']) && !(isset($utm_output['utm_source']) && !empty($utm_output['utm_source']))) {
                    $utm_output['utm_source'] = $utm_output_submit_url['utm_source'];
                }
                if (isset($utm_output_submit_url['utm_medium']) && !empty($utm_output_submit_url['utm_medium']) && !(isset($utm_output['utm_medium']) && !empty($utm_output['utm_medium']))) {
                    $utm_output['utm_medium'] = $utm_output_submit_url['utm_medium'];
                }
                if (isset($utm_output_submit_url['utm_campaign']) && !empty($utm_output_submit_url['utm_campaign']) && !(isset($utm_output['utm_campaign']) && !empty($utm_output['utm_campaign']))) {
                    $utm_output['utm_campaign'] = $utm_output_submit_url['utm_campaign'];
                }
                if (isset($utm_output_submit_url['utm_content']) && !empty($utm_output_submit_url['utm_content']) && !(isset($utm_output['utm_content']) && !empty($utm_output['utm_content']))) {
                    $utm_output['utm_content'] = $utm_output_submit_url['utm_content'];
                }
                if (isset($utm_output_submit_url['utm_term']) && !empty($utm_output_submit_url['utm_term']) && !(isset($utm_output['utm_term']) && !empty($utm_output['utm_term']))) {
                    $utm_output['utm_term'] = $utm_output_submit_url['utm_term'];
                }
            }
            $domen = explode( '/', $this->form['url'] );
            $this->params = [
                "TITLE" => $this->form['url'].' / '.$this->form['name'].' / '.$this->date,
                "NAME" => (isset($params['name']) && !empty($params['name'])) ? urldecode($params['name']) : "",
                "SECOND_NAME" => (isset($params['second_name']) && !empty($params['second_name'])) ? urldecode($params['second_name']) : "",
                "LAST_NAME" => (isset($params['last_name']) && !empty($params['last_name'])) ? urldecode($params['last_name']) : "",
                "COMPANY_TITLE" => (isset($params['company']) && !empty($params['company'])) ? urldecode($params['company']) : "",
                "STATUS_ID" => "NEW",
                "ADDRESS" => (isset($params['address']) && !empty($params['address'])) ? urldecode($params['address']) : "",
                "OPENED" => "Y",
                "ASSIGNED_BY_ID" => self::SETTINGS['ASSIGNED'],
                "COMMENTS" => $comment,
                "PHONE" => $phone,
                "EMAIL" => $mail,
                "UTM_SOURCE" => (isset($utm_output['utm_source']) && !empty($utm_output['utm_source'])) ? urldecode($utm_output['utm_source']) : "",
                "UTM_MEDIUM" => (isset($utm_output['utm_medium']) && !empty($utm_output['utm_medium'])) ? urldecode($utm_output['utm_medium']) : "",
                "UTM_CAMPAIGN" => (isset($utm_output['utm_campaign']) && !empty($utm_output['utm_campaign'])) ? urldecode($utm_output['utm_campaign']) : "",
                "UTM_CONTENT" => (isset($utm_output['utm_content']) && !empty($utm_output['utm_content'])) ? urldecode($utm_output['utm_content']) : "",
                "UTM_TERM" => (isset($utm_output['utm_term']) && !empty($utm_output['utm_term'])) ? urldecode($utm_output['utm_term']) : "",
                "UF_CRM_1608101741558" => TRUE,
                "UF_CRM_1608720560227" => self::SETTINGS['CONNECT'],
                "UF_CRM_1608875925351" => self::SETTINGS['TYPE'],
                "UF_CRM_1617622300" => (isset($params['roistat_visit']) && !empty($params['roistat_visit'])) ? $params['roistat_visit'] : "",
                "UF_CRM_1622448857864" => $this->form['key'],
                "UF_CRM_1622449617181" => $domen[0],
                "UF_CRM_1622448780833" => $url,
            ];

            if ($this->form['direction'] != 0) {
                $this->params['UF_CRM_1611301769946'] = $this->form['direction'];
            }
            if ($this->form['country'] != 0) {
                $this->params['UF_CRM_1607933607'] = $this->getListCountry[$this->form['country']]['key'];
            }
            if ($country != null) {
                $this->params['UF_CRM_1607933607'] = $country;
            }

            /*
            if ($this->form['direction'] != 0) {
                $this->params['UF_CRM_1607588088964'] = $this->form['direction'];
                if (array_key_exists($this->form['direction'], self::DIRECTION_BY)) {
                    //$this->params['UF_CRM_1607948122'] = self::__DIRECTION_BY[$this->form['direction']];
                    $this->params['UF_CRM_1611301769946'] = self::DIRECTION_BY[$this->form['direction']];
                }
            }
            if ($this->form['country'] != 0) {
                $this->params['UF_CRM_1607589066228'] = $this->form['country'];
            }
            if ($country != null) {
                $this->params['UF_CRM_1607933607'] = $country;
                $id_country = self::DISTRIBUTION_BY_COUNTRY['World'];
                foreach (self::COUNTRY as $id_distribution_by_country => $array) {
                    if (in_array($country, $array)) {
                        $id_country = $id_distribution_by_country;
                    }
                }
                if ($id_country == self::DISTRIBUTION_BY_COUNTRY['USA']) {
                    $this->params['UF_CRM_1607588088964'] = self::DISTRIBUTION_BY_DIRECTION['USA'];
                    $this->params['UF_CRM_1607589066228'] = self::DISTRIBUTION_BY_COUNTRY['USA'];
                } else {
                    $this->params['UF_CRM_1607589066228'] = $id_country;
                }
            }
            */
        }
    }

    function script_add_lead()
    {
        //$email_result = $this->send_email($this->_params);
        $api_result = $this->add_lead($this->params);
        $_params = json_encode($this->_params, JSON_UNESCAPED_UNICODE);
        $api_result_js = json_encode($api_result, JSON_UNESCAPED_UNICODE);
        $log_result = $this->add_log_forms($this->form['id'], $_params, $api_result_js);
        return $api_result['status'];
        /*

        return ($api_result && $email_result) ? true : false;
        */
    }

    function script_send_email()
    {
        return $this->add_log_forms($this->form['id'], $this->params, 'Ok');
        /*
        $email = '';
        $email_result = $this->send_email($email, $params);
        return ($email_result) ? true : false;
        */
    }

    function add_lead($fields = [])
    {
        $params['fields'] = $fields;
        $result = $this->bitrixAPI->send('crm.lead.add', $params, self::SETTINGS['URL']);
        if (isset($result['error']) && !empty($result['error'])) {
            return ['status' => false, 'result' => $result];
        } else {
            return ['status' => true, 'result' => $result];
        }
    }

    function send_email($params = [])
    {
        $email = self::EMAIL_USER[$this->form['script']];
        $subject = $this->form['url'].'/'.$this->form['name'].'/'.$this->date;
        $message = $this->mapped_implode($params);
        $result = $this->mail->send('Обработчик форм', $email, $subject, $message, '', self::MAIL_CONFIG);
        return $result;
    }

    function mapped_implode($array) {
        return implode(PHP_EOL, array_map(
                function($k, $v) {
                    return $k . ": " . $v ."<br>";//."\r\n";
                },
                array_keys($array),
                array_values($array)
            )
        );
    }

    function prepare_keys_params($params) {
        $result = [];
        foreach ($params as $key => $value) {
            $k = mb_strtolower($key);
            $result[$k] = $value;
        }
        return $result;
    }

    function prepare_cookies_params($cookies) {
        $array = array_filter(array_map('trim', explode(';', $cookies)), 'strlen');
        $result = [];
        foreach ($array as $param) {
            $n = strstr($param, '=');
            $result[stristr($param, '=', true)] = mb_substr($n, 1);
        }
        return $result;
    }

    function getTypeIm($value) {
        if (array_key_exists($value, self::IM_TYPE)) {
            return self::IM_TYPE[$value];
        } else {
            return self::IM_TYPE['OTHER'];
        }
    }

    function __destruct()
    {

    }

}


