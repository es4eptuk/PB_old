<?php
error_reporting(E_ALL);
ini_set('display_startup_errors', 1);
ini_set('display_errors', '1');

include 'include/config.inc.php';
$term = $_GET['term'];

$output = '';
        $str_arr = array();
        global $database_server, $database_user, $database_password, $dbase;
        $dsn = "mysql:host=$database_server;dbname=$dbase;charset=utf8";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $pdo = new PDO($dsn, $database_user, $database_password, $opt);
        //$term = quotemeta($term);
        $query = "SELECT id,title,vendor_code,assembly FROM pos_items WHERE (title LIKE '%$term%' OR vendor_code LIKE '%$term%') AND (archive = 0)";
        $result = $pdo->query($query);
        while ($line = $result->fetch()) {
            $pos_array[] = $line; 
            }
         
        if (isset($pos_array)) {

            foreach ($pos_array as $row) {
                $row['title'] = str_ireplace("\"", "'", trim($row['title']));

                //$sq = quotemeta($row['title']);
                //$log = date('Y-m-d H:i:s') . ' ' . print_r($sq, true);
                //file_put_contents(__DIR__ . '/loggg.txt', $log . PHP_EOL, FILE_APPEND);
                //die;

                array_push($str_arr, "\"". $row['id'] ."::" . $row['vendor_code'] ."::" . $row['title'] . "\"");
            }

            $s = "[".implode(",", $str_arr)."]";
            echo $s;
        }
        
