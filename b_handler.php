<?php
include 'include/class.inc.php';

define('REST_KEY', 'ywR5djLVUy');

global $bitrixForm;

//$log = date('Y-m-d H:i:s') . ' ' . print_r($_POST, true);
//file_put_contents(__DIR__ . '/log.txt', $log . PHP_EOL, FILE_APPEND);

$rest_key = (isset($_GET['rest_key'])) ? $_GET['rest_key'] : '';

if ($rest_key == REST_KEY) {
    $post = [];
    if (isset($_POST)) {
        foreach ($_POST as $name => $value) {
            $post[$name] = $value;
        }
    }
    $key = (array_key_exists('formname', $post)) ? $post['formname'] : 0;
    $b_form = $bitrixForm->get_info_form_by_key($key);
    if ($b_form) {
        $result = $bitrixForm->action($b_form['id'], $post);
        if ($result) {
            echo 'Ok';
            //echo json_encode(['status' => true], JSON_UNESCAPED_UNICODE);
        } else {
            echo 'Error';
            //echo json_encode(['status' => false, 'error' => $error], JSON_UNESCAPED_UNICODE);
        }
    } else {
        echo 'Not found form';
        //echo json_encode(['status' => false, 'error' => 'Not found form'], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo 'Access is denied';
    //echo json_encode(['status' => false, 'error' => 'Access is denied'], JSON_UNESCAPED_UNICODE);
}
