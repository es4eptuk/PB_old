<?php 
echo __DIR__.'/include/config.inc.php';
include __DIR__.'/include/config.inc.php';
include __DIR__.'/page/telegram.php';
include __DIR__.'/page/writeoff.php';
include __DIR__.'/page/position.php';
include __DIR__.'/page/orders.php';
include __DIR__.'/page/users.php';
include __DIR__.'/page/robots.php';
include __DIR__.'/page/tickets.php';



 $arr = $robots->get_robots();
 
                $arr_tickets = $tickets->get_tickets();
                $finish = array();
                $inprocess = array();
                $wait = array();
                $robot_problem = 0;
                $open_tickets = 0;
                $total_robots = 0;
                
                foreach ($arr_tickets as &$ticket) {
                    $ticket_status = $ticket['status'];
                    $ticket_robot = $ticket['robot'];
                    
                    if ($ticket_status==3 || $ticket_status==6) {
                       // if(isset($finish[$ticket_robot])) {$finish[$ticket_robot]}
                        $finish[$ticket_robot] = isset($finish[$ticket_robot] ) + 1;
                    } 
                    
                    if ($ticket_status==1 || $ticket_status==2 || $ticket_status==4 || $ticket_status==5) {
                       $inprocess[$ticket_robot] = isset($inprocess[$ticket_robot] ) + 1;
                       $open_tickets++;
                       
                    }
                    
                     if ($ticket_status==7 ) {
                       $wait[$ticket_robot] = isset($wait[$ticket_robot] ) + 1;
                    }
                }
                //print_r($finish);
                //print_r($inprocess);
                
               

                $open_tickets = array_sum($inprocess);
                $robot_problem = count($inprocess);
                $total_robots = count($arr);
                
                
                $tickets->write_stat($total_robots,$open_tickets,$robot_problem);
                echo "finish";
                
?>