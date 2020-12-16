<?php


class BitrixForm
{
    const EMAIL_USER = [
        'LEAD' => 'a.baidin@promo-bot.ru',
        'HR' => 'a.baidin@promo-bot.ru',
    ];
    const HANDLER_TYPE = [
        1 => 'TILDA',
        2 => 'GITLAB',
        3 => 'LINKODIUM',
    ];
    const SCRIPT_TYPE = [
        1 => 'LEAD',
        2 => 'EMAIL',
    ];
    const STATUS = [
        'ACTIVE' => 1,
        'NOTACTIVE' => 2,
    ];
    const DISTRIBUTION_BY_DIRECTION = [
        'No' => 0,
        'Devices' => 27,
        'Robots' => 26,
        'USA' => 28,
    ];
    const DISTRIBUTION_BY_COUNTRY = [
        'No' => 0,
        'R&SNG' => 29,
        'World' => 30,
        'USA' => 651,
    ];
    const SETTINGS = [
        'ASSIGNED' => 1097,
        'URL' => 'https://team.promo-bot.ru/rest/1097/xh3yrqn4yj4pq0ir',
        'EMAIL' => 'a.baidin@promo-bot.ru',
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
    public $getListDirections;
    public $getListCountry;

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
        foreach (self::DISTRIBUTION_BY_DIRECTION as $id => $name) {
            $this->getListDirections[$name] = $id;
        }
        foreach (self::DISTRIBUTION_BY_COUNTRY as $id => $name) {
            $this->getListCountry[$name] = $id;
        }
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
        }
        return $result;
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
        $query = 'SELECT * FROM `bitrix_form_log` JOIN `bitrix_form` ON `bitrix_form_log`.`form_id` = `bitrix_form`.`id` ORDER BY `bitrix_form_log`.`date` DESC';
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $list[] = $line;
        }
        return (isset($list)) ? $list : [];
    }

    function add_log_forms($id_form, $params, $result)
    {
        $date = date("Y-m-d H:i:s");;
        $query = "INSERT INTO `bitrix_form_log` (`id`, `form_id`, `params`, `result`, `date`) VALUES (NULL, $id_form, '$params', '$result', '$date')";
        $result = $this->pdo->query($query);
        return ($result) ? true : false;
    }

    function create_form($key, $url, $name, $handler, $script, $status, $direction, $country)
    {
        $query = "INSERT INTO `bitrix_form` (`id`, `key`, `url`, `name`, `handler`, `script`, `status`, `direction`, `country`) VALUES (NULL, '$key', '$url', '$name', '$handler', '$script', '$status', '$direction', '$country')";
        $result = $this->pdo->query($query);
        return ($result) ? true : false;
    }

    function update_form($id, $key, $url, $name, $handler, $script, $status, $direction, $country)
    {
        $query = "UPDATE `bitrix_form` SET `key` = '$key', `url` = '$url', `name` = '$name', `handler` = '$handler', `script` = '$script', `status` = '$status', `direction` = '$direction', `country` = '$country' WHERE `id` = $id";
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
                $comment .= "Textarea:\n".urldecode($params['textarea'])."\n";
            }
            if (isset($params['comments']) && !empty($params['comments'])) {
                $comment .= "Comments:\n".urldecode($params['comments'])."\n";
            }
            if (isset($params['comments_2']) && !empty($params['comments_2'])) {
                $comment .= "Comments_2:\n".urldecode($params['comments_2'])."\n";
            }
            if (isset($params['comment_form']) && !empty($params['comment_form'])) {
                $comment .= "Comment_form:\n".urldecode($params['comment_form'])."\n";
            }
            $phone = [];
            if (isset($params['phone']) && !empty($params['phone'])) {
                $phone[] = [
                    "VALUE" => urldecode($params['phone']),
                    "VALUE_TYPE" => "OTHER",
                ];
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
                "EMAIL" => $mail,
                "UTM_SOURCE" => (isset($params['utm_source']) && !empty($params['utm_source'])) ? urldecode($params['utm_source']) : "",
                "UTM_MEDIUM" => (isset($params['utm_medium']) && !empty($params['utm_medium'])) ? urldecode($params['utm_medium']) : "",
                "UTM_CAMPAIGN" => (isset($params['utm_campaign']) && !empty($params['utm_campaign'])) ? urldecode($params['utm_campaign']) : "",
                "UTM_CONTENT" => (isset($params['utm_content']) && !empty($params['utm_content'])) ? urldecode($params['utm_content']) : "",
                "UTM_TERM" => (isset($params['utm_term']) && !empty($params['utm_term'])) ? urldecode($params['utm_term']) : "",
                "UF_CRM_1608101741558" => TRUE,
            ];
            if ($this->form['direction'] != 0) {
                $this->params['UF_CRM_1607588088964'] = $this->form['direction'];
            }
            if ($this->form['country'] != 0) {
                $this->params['UF_CRM_1607589066228'] = $this->form['country'];
            }
        }
    }

    function handler_gitlab($params = [])
    {
        if ($params != []) {
            $comment = '';
            if (isset($params['message']) && !empty($params['message'])) {
                $comment .= "Message:\n".urldecode($params['message'])."\n";
            }
            if (isset($params['country']) && !empty($params['country'])) {
                $comment .= "Country:\n".urldecode($params['country'])."\n";
            }
            if (isset($params['stations']) && !empty($params['stations'])) {
                $comment .= "Stations:\n".urldecode($params['stations'])."\n";
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
                "UTM_SOURCE" => (isset($params['utm_source']) && !empty($params['utm_source'])) ? urldecode($params['utm_source']) : "",
                "UTM_MEDIUM" => (isset($params['utm_medium']) && !empty($params['utm_medium'])) ? urldecode($params['utm_medium']) : "",
                "UTM_CAMPAIGN" => (isset($params['utm_campaign']) && !empty($params['utm_campaign'])) ? urldecode($params['utm_campaign']) : "",
                "UTM_CONTENT" => (isset($params['utm_content']) && !empty($params['utm_content'])) ? urldecode($params['utm_content']) : "",
                "UTM_TERM" => (isset($params['utm_term']) && !empty($params['utm_term'])) ? urldecode($params['utm_term']) : "",
                "UF_CRM_1608101741558" => TRUE,
            ];
            if ($this->form['direction'] != 0) {
                $this->params['UF_CRM_1607588088964'] = $this->form['direction'];
            }
            if ($this->form['country'] != 0) {
                $this->params['UF_CRM_1607589066228'] = $this->form['country'];
            }
        }
    }

    function handler_linkodium($params = [])
    {
        if ($params != []) {
            $this->params = $params;
        }
    }

    function script_add_lead()
    {
        //$email_result = $this->send_email(self::SETTINGS['EMAIL'], $this->_params);
        $api_result = $this->add_lead($this->params);
        $_params = json_encode($this->_params);
        $api_result = json_encode($api_result);
        $log_result = $this->add_log_forms($this->form['id'], $_params, $api_result);
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

    function send_email($email, $params = [])
    {
        $subject = $this->form['url'].'/'.$this->form['name'].'/'.$this->date;
        $message = $this->mapped_implode($params);
        $result = $this->mail->send('Обработчик форм',  $email, $subject, $message);
        return $result;
    }

    function mapped_implode($array) {
        return implode(PHP_EOL, array_map(
                function($k, $v) {
                    return $k . ": " . $v ."\n";
                },
                array_keys($array),
                array_values($array)
            )
        );
    }

    function prepare_keys_params($params){
        $result = [];
        foreach ($params as $key => $value) {
            $k = mb_strtolower($key);
            $result[$k] = $value;
        }
        return $result;
    }

    function __destruct()
    {

    }

}


