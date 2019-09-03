<?php 
function my_curl_zabbix($arr) {
    global $auth;
    $url = 'https://pb2.icmm.ru/zabbix/api_jsonrpc.php';
    $arr['jsonrpc'] = '2.0';
    $arr['id'] = '1';
    $arr['auth'] = $auth;
    $postfields = json_encode($arr);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json-rpc'));
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
    echo $postfields."<br>";
    $return = curl_exec($curl);
    curl_close($curl);
    return json_decode($return, true);
}
 
function z_auth() {
    $jsonData = array("jsonrpc" => "2.0","method" => "user.login","params" => array("user" => "manufacture","password" => "queetoh6Ace"), "id" => "1", "auth" => "null");
    $auth_arr = my_curl_zabbix($jsonData);
   // print_r($auth_arr);
    return $auth_arr['result'];
}
 
function z_get_hosts($filter_arr) {
    $jsonData = array("method" => "host.get","params" => array("output" => array("hostid", "host", "name"),"selectInventory" => "name"));
    if ($filter_arr) {
        $jsonData['params']['filter'] = $filter_arr;
    }
    $result = my_curl_zabbix($jsonData);
    //print_r($result);
    return $result['result'];
}
 
function z_update_hosts($host_id, $action) {
    $jsonData = array("method" => "trigger.get","params" => array("hostid" => "{$host_id}", "output" => "extend", "selectFunctions" => "extend"));
    switch ($action) {
        case 'disable':
            //$jsonData['params']['status'] = 1;
            break;
        case 'enable':
            //$jsonData['params']['status'] = 0;
            break;
        default:
            return false;
    }
    $result = my_curl_zabbix($jsonData);
    print_r($result);
    //return $result;
}

function z_add_group($host_id, $group) {
   
    $jsonData = array("method" => "hostgroup.massadd");
    $jsonData['params']['groups'][]['groupid'] = $group;
    $jsonData['params']['hosts'][]['hostid'] = $host_id;
    $result = my_curl_zabbix($jsonData);
    return $result;
}  

function z_remove_group($host_id, $group) {
   
    $jsonData = array("method" => "hostgroup.massremove");
    $jsonData['params']['groupids'] = $group;
    $jsonData['params']['hostids'] = $host_id;
    $result = my_curl_zabbix($jsonData);
    return $result;
} 
 
$auth = z_auth();
$z_host = z_get_hosts(array('host'=>'promobotv4_0004'));
//var_dump($z_host);
z_remove_group($z_host[0]['hostid'], "31");

?>