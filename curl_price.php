
               <?php 
               
               include '/var/www/promobot/data/www/db.promo-bot.ru/new/include/config.inc.php';
               include '/var/www/promobot/data/www/db.promo-bot.ru/new/page/mail.php';
               include '/var/www/promobot/data/www/db.promo-bot.ru/new/page/log.php';
               include '/var/www/promobot/data/www/db.promo-bot.ru/new/page/orders.php';
               include '/var/www/promobot/data/www/db.promo-bot.ru/new/page/writeoff.php';
               include '/var/www/promobot/data/www/db.promo-bot.ru/new/page/position.php';
               $query = "";
               
               
               
               $mh = $position->get_kit(1,4,0);
               $hp = $position->get_kit(2,4,0);
               $bd = $position->get_kit(3,4,0);
               $up = $position->get_kit(4,4,0);
               
               //print_r($mh);
               $sum_mh = 0;
               $sum_hp = 0;
               $sum_bd = 0;
               $sum_up = 0;
               $sum_all = 0;
               
               foreach ($mh as &$value) {
                    $arr_pos = $position->get_pos_in_kit($value['id_kit']);
                        foreach ($arr_pos as &$value_pos) {
                        $sum_mh = $sum_mh + ($value_pos['price']*$value_pos['count']);
                         }
                }
                
                foreach ($hp as &$value) {
                    $arr_pos = $position->get_pos_in_kit($value['id_kit']);
                        foreach ($arr_pos as &$value_pos) {
                        $sum_hp = $sum_hp + ($value_pos['price']*$value_pos['count']);
                         }
                }
                
                
                if (isset($bd)) {
                 foreach ($bd as &$value) {
                    $arr_pos = $position->get_pos_in_kit($value['id_kit']);
                        foreach ($arr_pos as &$value_pos) {
                        $sum_bd = $sum_bd + ($value_pos['price']*$value_pos['count']);
                         }
                }
                }
                
                foreach ($up as &$value) {
                    $arr_pos = $position->get_pos_in_kit($value['id_kit']);
                        foreach ($arr_pos as &$value_pos) {
                        $sum_up = $sum_up + ($value_pos['price']*$value_pos['count']);
                         }
                }
                
               $sum_bd = 80000;
               setlocale(LC_MONETARY, 'ru_RU');
               $sum_all = $sum_mh + $sum_hp + $sum_bd + $sum_up;
               $date    = date("Y-m-d H:i:s");
               
               $query = "INSERT INTO `price_stat` (`id`, `all`, `mh`, `hp`, `bd`, `up`, `date`) VALUES (NULL, $sum_all, $sum_mh, $sum_hp, $sum_bd, $sum_up, '$date')";
               $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
               
               ?> 
             