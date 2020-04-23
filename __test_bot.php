<?php 
include 'include/class.inc.php';
  
  $supportTime = $settings->get_param('supportTime');
  $supportTime = unserialize($supportTime['data']);
  $supportTimeStart = $supportTime[0];
  $supportTimeEnd = $supportTime[1];
  
  print_r($supportTimeStart);

?>