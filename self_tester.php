<?php
include 'include/class.inc.php';
global $check, $robots;

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if (isset($_POST['robot'])) {
        
        $robot = $_POST['robot'];

        /*костыль определяет версию потом лучше переделать на хост с разделением 4ых версий*/
        $host = strstr($robot, '_', true);
        $number = substr(strstr($robot, '_'), 1, 4);
        $version = substr($number,0,1);
        if ($version != '5' && $version != '6' && $version != '7' && $version != '8') {
            $version = '4';
        }
        /**/
        
        if ($action == 1) {
            
            $checks->auth = $checks->z_auth_new($version);
            $z_host = $checks->z_get_hosts_new(['host'=>$robot], $version);
            $checks->z_update_hosts_new($z_host[0]['hostid'], 'enable', $version);
            $checks->z_remove_group_new($z_host[0]['hostid'], $checks::ZABIX[$version]['Manufacture'], $version);
            $checks->z_add_group_new($z_host[0]['hostid'], $checks::ZABIX[$version]['Manufacture_test'], $version);

            //!!!временно для синхронизации
            if ($version == '4') {
                $checks->auth = null;
                $versionD = $version . '1';
                $checks->auth = $checks->z_auth_new($versionD);
                $z_host = $checks->z_get_hosts_new(['host'=>$robot], $versionD);
                $checks->z_update_hosts_new($z_host[0]['hostid'], 'enable', $versionD);
                $checks->z_remove_group_new($z_host[0]['hostid'], $checks::ZABIX[$versionD]['Manufacture'], $versionD);
                $checks->z_add_group_new($z_host[0]['hostid'], $checks::ZABIX[$versionD]['Manufacture_test'], $versionD);
            }

            $str = "Поставлен на тест";
            $time =  date("H:i:s"); 
            $robots->add_log_width_zabbix($robot,$time,1,$str,0,"Information","0","0");
            echo "Robot ".$robot." start Self test";
        }
        
        if ($action == 0) {
            $checks->auth = $checks->z_auth_new($version);
            $z_host = $checks->z_get_hosts_new(['host'=>$robot], $version);
            $checks->z_remove_group_new($z_host[0]['hostid'], $checks::ZABIX[$version]['Manufacture_test'], $version);
            $checks->z_add_group_new($z_host[0]['hostid'], $checks::ZABIX[$version]['Manufacture'], $version);

            //!!!временно для синхронизации
            if ($version == '4') {
                $checks->auth = null;
                $versionD = $version . '1';
                $checks->auth = $checks->z_auth_new($versionD);
                $z_host = $checks->z_get_hosts_new(['host'=>$robot], $versionD);
                $checks->z_remove_group_new($z_host[0]['hostid'], $checks::ZABIX[$versionD]['Manufacture_test'], $versionD);
                $checks->z_add_group_new($z_host[0]['hostid'], $checks::ZABIX[$versionD]['Manufacture'], $versionD);
            }

            $str = "Снят с теста";
            $time =  date("H:i:s"); 
            $robots->add_log_width_zabbix($robot,$time,1,$str,0,"Information","0","0");
            echo "Robot ".$robot." stop Self test";
        }
        
        
    }
    
}


?>