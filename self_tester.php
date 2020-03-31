<?php
include 'include/class.inc.php';
$check = new Checks;

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if (isset($_POST['robot'])) {
        
        $robot = $_POST['robot'];  
        
        if ($action == 1) {
            
            $checks->auth = $checks->z_auth();
            $z_host = $checks->z_get_hosts(array('host'=>$robot));
            $checks->z_update_hosts($z_host[0]['hostid'], 'enable');
            $checks->z_remove_group($z_host[0]['hostid'], '32');
            $checks->z_add_group($z_host[0]['hostid'], '31');
            $str = "Поставлен на тест";
            $time =  date("H:i:s"); 
            $robots->add_log_width_zabbix($robot,$time,1,$str,0,"Information","0","0");
            echo "Robot ".$robot." start Self test";
        }
        
        if ($action == 0) {
            $checks->auth = $checks->z_auth();
            $z_host = $checks->z_get_hosts(array('host'=>$robot));
            //$checks->z_update_hosts($z_host[0]['hostid'], 'disable');
            //$checks->z_add_group($z_host[0]['hostid'], '29');
           // $checks->z_add_group($z_host[0]['hostid'], '8');
            $checks->z_remove_group($z_host[0]['hostid'], '31');
            $checks->z_add_group($z_host[0]['hostid'], '32');
            $str = "Снят с теста";
            $time =  date("H:i:s"); 
            $robots->add_log_width_zabbix($robot,$time,1,$str,0,"Information","0","0");
            echo "Robot ".$robot." stop Self test";
        }
        
        
    }
    
}


?>