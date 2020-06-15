<?php

class Statistics
{
    const TIME_PRODUCTION = [
        'time_start' => '09:00',
        'time_end' => '18:00',
        'days_week' => [1,2,3,4,5],
    ];

    private $query;
    private $pdo;

    private $robot;


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
        global $robots;
        $this->robot = $robots;
    }

    function __destruct()
    {

    }

    /** ВРЕМЯ СБОРКИ **/
    //начать учет времени сборки
    public function add_robot_production_statistics($id_robot, $date_start=0)
    {
        $date = ($date_start == 0) ? time() : $date_start;
        $query = "INSERT INTO `robot_production_statistics` (`id_robot`, `date_start`) VALUES ($id_robot, '$date')";
        $result = $this->pdo->query($query);

        return true;
    }
    //удалить запись из статистики учета времени сборки
    public function del_robot_production_statistics($id_robot)
    {
        $query = "DELETE FROM `robot_production_statistics` WHERE `id_robot` = $id_robot";
        $result = $this->pdo->query($query);

        return true;
    }
    //просмотр данных записи из статистики учета времени сборки
    public function get_robot_production_statistics($id_robot)
    {
        $query = "SELECT * FROM `robot_production_statistics` WHERE `id_robot` = $id_robot";
        $result = $this->pdo->query($query);

        while ($line = $result->fetch()) {
            $res[] = $line;
        }
        return (isset($res)) ? $res[0] : null;
    }
    //весь список статистики учета времени сборки
    public function lists_robot_production_statistics($version=0, $date_start='', $date_end='')
    {
        $where = '';
        if ($date_start != '') {
            $date_start = strtotime($date_start);
            $where = " WHERE `robot_production_statistics`.`date_end` >= $date_start";
        }
        if ($date_end != '') {
            $date_end = strtotime($date_end);
            $where = ($where != '') ? $where." AND `robot_production_statistics`.`date_end` <= $date_end" : " WHERE `robot_production_statistics`.`date_end` <= $date_end";
        }
        if ($version != 0) {
            $where = ($where != '') ? $where." AND `robots`.`version` = $version" : " WHERE `robots`.`version` = $version";
        }
        $query = "SELECT * FROM `robot_production_statistics` JOIN `robots` ON `robot_production_statistics`.`id_robot` = `robots`.`id`".$where;
        $result = $this->pdo->query($query);
        $res = [];
        while ($line = $result->fetch()) {
            $res[$line['id_robot']] = $line;
        }
        $date_now = time();
        foreach ($res as $id => $item) {
            //даты в формате
            $res[$id]['start'] = date('d.m.Y H:i', $item['date_start']);
            $res[$id]['end'] = ($item['date_end'] == null) ? '' : date('d.m.Y H:i', $item['date_end']);
            //статус и расчет времени
            if ($item['date_end'] != null) {
                $status = 'stop';
                $res[$id]['current_pause'] = 0;
            } elseif ($item['start_pause'] != null) {
                $status = 'pause';
                $res[$id]['result'] = $this->get_time_spent($item['date_start'], $item['start_pause']) - $item['time_pause'];
                $res[$id]['current_pause'] = $this->get_time_spent($item['start_pause'], $date_now);
            } else {
                $status = 'play';
                $res[$id]['result'] = $this->get_time_spent($item['date_start'], $date_now) - $item['time_pause'];
                $res[$id]['current_pause'] = 0;
            }
            $res[$id]['status'] = $status;
            //итоговое время в формате
            $hh = intval($res[$id]['result']/3600);
            $mm = intval(($res[$id]['result'] - $hh * 3600)/60);
            $res[$id]['time'] = $hh.':'.$mm;
            $hh = intval($res[$id]['time_pause']/3600);
            $mm = intval(($res[$id]['time_pause'] - $hh * 3600)/60);
            $res[$id]['pause'] = $hh.':'.$mm;
            $hh = intval($res[$id]['current_pause']/3600);
            $mm = intval(($res[$id]['current_pause'] - $hh * 3600)/60);
            $res[$id]['current_pause'] = ($res[$id]['current_pause'] == 0) ? '' : $hh.':'.$mm;
        }

        return $res;
    }
    //поставить на паузу учета времени сборки
    public function pause_robot_production_statistics($id_robot)
    {
        $date = time();
        $query = "UPDATE `robot_production_statistics` SET `start_pause` =  $date WHERE `id_robot` = $id_robot";
        $result = $this->pdo->query($query);
        return true;
    }
    //снять с паузы учета времени статистики
    public function start_robot_production_statistics($id_robot)
    {
        $statistics = $this->get_robot_production_statistics($id_robot);
        $date_end = time();
        $date_start = $statistics['start_pause'];
        $spent = $this->get_time_spent($date_start, $date_end);
        $time_pause = intval($spent);
        $query = "UPDATE `robot_production_statistics` SET `start_pause` =  NULL, `time_pause` =  time_pause + $time_pause WHERE `id_robot` = $id_robot";
        $result = $this->pdo->query($query);
        return true;
    }
    //завершить учет сборки и расчитать результат
    public function stop_robot_production_statistics($id_robot, $date_end=0)
    {
        $statistics = $this->get_robot_production_statistics($id_robot);
        $date_end = ($date_end == 0) ? time() : $date_end;
        $date_start = $statistics['date_start'];
        $time_pause = $statistics['time_pause'];
        $spent = $this->get_time_spent($date_start, $date_end);
        $res = $spent - $time_pause;
        $query = "UPDATE `robot_production_statistics` SET `date_end` =  $date_end, `result` =  $res WHERE `id_robot` = $id_robot";
        $result = $this->pdo->query($query);
        return true;
    }
    //смена статуса
    public function change_status_robot_production_statistics($id_robot)
    {
        $statistics = $this->get_robot_production_statistics($id_robot);
        if ($statistics['date_end'] != null) {
            return false;
        }
        if ($statistics['start_pause'] == null) {
            $this->pause_robot_production_statistics($id_robot);
        } else {
            $this->start_robot_production_statistics($id_robot);
        }
        return true;
    }

    /** ПОДСЧЕТ РАБОЧЕГО ВРЕМЕНИ **/
    //определить затраченное время с учетом графика и выходных
    public function get_time_spent($date_start, $date_end, $schedule=self::TIME_PRODUCTION)
    {
        $time_work_start = explode(':', $schedule['time_start']);
        $time_work_end = explode(':', $schedule['time_end']);
        //рабочее время в сек
        $work_time = (($time_work_end[0]*60+$time_work_end[1])-($time_work_start[0]*60+$time_work_start[1]))*60;
        //определим год/месяц/день/час/мин/сек старт
        $y_start = date('Y', $date_start);
        $m_start = date('m', $date_start);
        $d_start = date('d', $date_start);
        //определим год/месяц/день/час/мин/сек конец
        $y_end = date('Y', $date_end);
        $m_end = date('m', $date_end);
        $d_end = date('d', $date_end);
        //собираем массив праздников
        $holidays = array_merge($this->getHolidays($y_start), $this->getHolidays($y_start+1));
        //проверка одного дня
        if ($y_start == $y_end && $m_start == $m_end && $d_start == $d_end) {
            $time = ($date_end - $date_start);
            return $time;
        }
        //определить время от текущей метки до конца времени рабочего дня в сек
        $start_time = mktime($time_work_end[0], $time_work_end[1], 0, $m_start, $d_start, $y_start) - $date_start;
        $start_time = ($start_time>0) ? $start_time : 0;
        //определить время от начала времени рабочего дня до текущей метки в сек
        $end_time = $date_end - mktime($time_work_start[0], $time_work_start[1], 0, $m_end, $d_end, $y_end);
        $end_time = ($end_time>0) ? $end_time : 0;
        //определяем количество дней исключая дни начало и конец даты
        $day_work_count = 0;
        $d=$d_start+1;
        $end = date('Y-m-d', $date_end);
        $i_date = date('Y-m-d', mktime(0, 0, 0, $m_start, $d, $y_start));
        while ($i_date < $end) {
            $i_d = date('d', strtotime($i_date));
            $i_m = date('m', strtotime($i_date));
            $i_y = date('Y', strtotime($i_date));
            $i_w = date('N', strtotime($i_date));
            $plus = true;
            //проверка на выходные и праздники
            if (!in_array($i_w, $schedule['days_week']) || in_array($i_y.'-'.$i_m.'-'.$i_d, $holidays)) {
                $plus = false;
            }
            //если все гут прибавляем раб день
            $day_work_count = ($plus) ? $day_work_count + 1 : $day_work_count;
            //добавляем счетчики и меняем дату
            $d++;
            $i_date = date('Y-m-d', mktime(0, 0, 0, $m_start, $d, $y_start));
        }
        //считаем конечный результат (сек)
        $time = $start_time + $day_work_count * $work_time + $end_time;
        return $time;
    }
    //запрос выходных дней
    public function getHolidays($year=0)
    {
        $year = ($year == 0) ? date('Y') : $year;
        //на локалке убраны дни выходных из за пандемии
        if ( $year== 2020) {
            $calendar = simplexml_load_file(PATCH_DIR.'/date/'.$year.'/calendar.xml');
        } else {
            if($this->isDomainAvailible('http://xmlcalendar.ru/data/ru/'.$year.'/calendar.xml')){
                $calendar = simplexml_load_file('http://xmlcalendar.ru/data/ru/'.$year.'/calendar.xml');
            } else {
                return [];
            }

        }
        $calendar =  $calendar->days->day;
        foreach( $calendar as $day ){
            $d = (array)$day->attributes()->d;
            $d = $d[0];
            $d = $year.'-'.str_replace('.', '-', $d);
            //$d = substr($d, 3, 2).'.'.substr($d, 0, 2).'.'.$year;
            if( $day->attributes()->t == 1 ) $arHolidays[] = $d;
        }
        return $arHolidays;
    }
    //проверка валидности ссылки
    public function isDomainAvailible($domain)
    {
        if(!filter_var($domain, FILTER_VALIDATE_URL)){
            return false;
        }
        $curlInit = curl_init($domain);
        curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,10);
        curl_setopt($curlInit,CURLOPT_HEADER,false);
        curl_setopt($curlInit,CURLOPT_NOBODY,true);
        curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);
        $response = curl_exec($curlInit);
        $result = curl_getinfo($curlInit, CURLINFO_HTTP_CODE);
        curl_close($curlInit);
        return ($result == 200) ? true : false;
    }
}