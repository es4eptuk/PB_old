<?php
include_once('include/class.inc.php');
//echo $_POST['action'];
if (isset($_POST['action'])) {
    if ($_POST['action'] == "orderDate") {
        echo $orders->orderDateStr($_POST['id']);
    }
    if ($_POST['action'] == "invent") {
        echo json_encode($position->invent($_POST['id'], $_POST['new_total']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "del_pos_equipment") {
        echo json_encode($position->del_pos_equipment($_POST['id'], $_POST['id_row']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "get_pos_in_assembly") {
        echo json_encode($position->get_pos_in_assembly($_POST['id']), JSON_UNESCAPED_UNICODE);
    }
    //отправить сборку в архив = отправить позицию к которой привязана данная сборка в архив
    if ($_POST['action'] == "assembly_to_archive") {
        echo json_encode($position->assembly_to_archive($_POST['id']), JSON_UNESCAPED_UNICODE);
    }
    //выбрать все позиции из набора
    if ($_POST['action'] == "get_pos_in_kit") {
        echo json_encode($position->get_pos_in_kit($_POST['id']), JSON_UNESCAPED_UNICODE);
    }
    //выбрать все комплекты в которых состоит позиция
    if ($_POST['action'] == "get_kit_by_pos") {
        echo json_encode($position->get_kit_by_pos($_POST['id']), JSON_UNESCAPED_UNICODE);
    }
    //выбрать все сборки в которых состоит позиция
    if ($_POST['action'] == "get_assembly_by_pos") {
        echo json_encode($position->get_assembly_by_pos($_POST['id']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "del_pos_writeoff") {
        echo json_encode($writeoff->del_pos_writeoff($_POST['id'], $_POST['pos_id'], $_POST['count']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "del_writeoff") {
        echo json_encode($writeoff->del_writeoff($_POST['id']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "del_pos_assembly") {
        echo json_encode($position->del_pos_assembly($_POST['id'], $_POST['id_row']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "del_pos_kit") {
        echo json_encode($position->del_pos_kit($_POST['id'], $_POST['id_row']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "add_task") {
        echo json_encode($task->add_task($_POST['param']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "1c_change_income_cat") {
        echo json_encode($oneC->change_income_cat($_POST['id'], $_POST['value']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "1c_get_cat_income") {
        echo json_encode($oneC->get_cat_income(), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "1c_add_income_category") {
        echo json_encode($oneC->add_income_category($_POST['title']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "1c_add_cost_category") {
        echo json_encode($oneC->add_cost_category($_POST['title']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "1c_add_cost_subcategory") {
        echo json_encode($oneC->add_cost_subcategory($_POST['category'], $_POST['title']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "robot_remont") {
        echo json_encode($robots->onRemont($_POST['robot']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "PP_stat") {
        echo json_encode($oneC->get_stat($_POST['param']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "PP_search") {
        echo json_encode($oneC->get_PP($_POST['param']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "income_search") {
        echo json_encode($oneC->get_income($_POST['param']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "get_info_income_cat") {
        echo json_encode($oneC->get_info_income_cat($_POST['id']), JSON_UNESCAPED_UNICODE);
    }
    //Получение списка категорий
    /*if ($_POST['action'] == "get_pos_category") {
        echo json_encode($position->get_pos_category(), JSON_UNESCAPED_UNICODE);
    }*/
    //Получение списка подкатегорий  
    if ($_POST['action'] == "get_pos_sub_category") {
        echo json_encode($position->get_pos_sub_category($_POST['subcategory']), JSON_UNESCAPED_UNICODE);
    }
    //Получение списка позиций в подкатегории  
    if ($_POST['action'] == "get_pos_in_sub_category") {
        echo json_encode($position->get_pos_in_sub_category($_POST['subcategory']), JSON_UNESCAPED_UNICODE);
    }
    //Получение списка позиций в категории  
    if ($_POST['action'] == "get_pos_in_category") {
        echo json_encode($position->get_pos_in_category($_POST['category']), JSON_UNESCAPED_UNICODE);
    }
    //Получение списка позиций в категории  
    if ($_POST['action'] == "gen_art") {
        echo json_encode($position->generate_art(), JSON_UNESCAPED_UNICODE);
    }
    //Добавление позиции  
    if ($_POST['action'] == "add_pos") {
        echo json_encode($position->add_pos($_POST['title'], $_POST['longtitle'], $_POST['category'], $_POST['unit'], $_POST['subcategory'], $_POST['vendorcode'], $_POST['provider'], $_POST['price'], $_POST['quant_robot'], $_POST['quant_total']), JSON_UNESCAPED_UNICODE);
    }
    //Получение списка поставщиков  
    if ($_POST['action'] == "get_pos_provider") {
        echo json_encode($position->get_pos_provider(), JSON_UNESCAPED_UNICODE);
    }
    //Получение информации о поставщике  
    if ($_POST['action'] == "get_info_pos_provider") {
        echo json_encode($position->get_info_pos_provider($_POST['provider']), JSON_UNESCAPED_UNICODE);
    }
    //Добавление поставщика 
    if ($_POST['action'] == "add_pos_provider") {
        echo $position->add_pos_provider($_POST['type'], $_POST['title']);
    }
    if ($_POST['action'] == "add_full_provider") {
        echo $position->add_full_provider($_POST['type'], $_POST['title'], $_POST['name'], $_POST['phone'], $_POST['email'], $_POST['address'], $_POST['contact']);
    }
    //Добавление поставщика 
    if ($_POST['action'] == "add_customer") {
        echo $robots->add_customer($_POST['name'], $_POST['fio'], $_POST['phone'], $_POST['email'], $_POST['address']);
    }
    //Получение информации о позиции
    if ($_POST['action'] == "get_info_pos") {
        echo json_encode($position->get_info_pos($_POST['id']), JSON_UNESCAPED_UNICODE);
    }
    //Получение информации о позиции с удаленного склада
    if ($_POST['action'] == "get_info_pos_warehouse") {
        echo json_encode($position_warehouse->get_info_pos($_POST['id']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "get_info_check") {
        echo json_encode($checks->get_info_check($_POST['id']), JSON_UNESCAPED_UNICODE);
    }
    //Редактирование позиции
    if ($_POST['action'] == "edit_pos") {
        echo json_encode($position->edit_pos($_POST['id'], $_POST['title'], $_POST['longtitle'], $_POST['unit'], $_POST['category'], $_POST['subcategory'], $_POST['vendorcode'], $_POST['provider'], $_POST['price'], $_POST['quant_robot'], $_POST['quant_total'], $_POST['min_balance'], $_POST['assembly'], $_POST['summary'], $_POST['archive'], $_POST['file']), JSON_UNESCAPED_UNICODE);
    }
    //Редактирование позиции с удаленного склада
    if ($_POST['action'] == "edit_pos_warehouse") {
        echo json_encode($position_warehouse->edit_pos($_POST['id'], $_POST['title'], $_POST['longtitle'], $_POST['category'], $_POST['subcategory'], $_POST['vendorcode'], $_POST['provider'], $_POST['price'], $_POST['quant_robot'], $_POST['quant_total'], $_POST['min_balance'], $_POST['assembly'], $_POST['summary'], $_POST['archive'], $_POST['file']), JSON_UNESCAPED_UNICODE);
    }
    //Редактирование контрагента
    if ($_POST['action'] == "edit_provider") {
        echo json_encode($position->edit_provider($_POST['id'], $_POST['title'], $_POST['name'], $_POST['type'], $_POST['phone'], $_POST['email'], $_POST['address'], $_POST['contact']), JSON_UNESCAPED_UNICODE);
    }
    //Удаление позиции
    if ($_POST['action'] == "delete_pos") {
        echo json_encode($position->del_pos($_POST['id']), JSON_UNESCAPED_UNICODE);
    }
    //Удаление позиции с удаленного склада
    if ($_POST['action'] == "delete_pos_warehouse") {
        echo json_encode($position_warehouse->del_pos($_POST['id']), JSON_UNESCAPED_UNICODE);
    }
    //Перемещение позиции с основного на удаленный склад
    if ($_POST['action'] == "to_warehouse") {
        echo json_encode($position->to_warehouse($_POST['id']), JSON_UNESCAPED_UNICODE);
    }
    //Перемещение позиции с удаленного на основной склад
    if ($_POST['action'] == "from_warehouse") {
        echo json_encode($position_warehouse->from_warehouse($_POST['id']), JSON_UNESCAPED_UNICODE);
    }
    //Удаление робота
    if ($_POST['action'] == "del_robot") {
        echo json_encode($robots->del_robot($_POST['id']), JSON_UNESCAPED_UNICODE);
    }
    //Получение название категории по id
    if ($_POST['action'] == "get_name_pos_category") {
        echo $position->get_name_pos_category($_POST['id']);
        // echo $_GET['id'];
    }
    //Получение название подкатегории по id  
    if ($_POST['action'] == "get_name_pos_subcategory") {
        echo $position->get_name_pos_subcategory($_POST['id']);
    }
    //Создание нового заказа
    if ($_POST['action'] == "add_order") {
        echo $orders->add_order($_POST['json'], $_POST['version']);
    }

    //Создание нового заказа на сварку и зенковку
    if ($_POST['action'] == "add_order_dop") {
        echo $orders->add_order_dop($_POST['json'], $_POST['category'], $_POST['provider']);
    }

    //Создание нового заказа
    if ($_POST['action'] == "add_order_plan") {
        echo $orders->add_order_plan($_POST['arr_order'], $_POST['month']);
    }
    //Создание нового заказа
    if ($_POST['action'] == "add_order_plan_new") {
        echo $plan->add_order_plan_new($_POST['category'], $_POST['version'], $_POST['month'], $_POST['filter']);
    }
    //Выгрузка заказа
    /*if ($_POST['action'] == "print_order") {
        echo $orders->createFileOrder($_POST['id']);

    }*/
    //Создание нового списания
    if ($_POST['action'] == "add_writeoff") {
        echo $writeoff->add_writeoff($_POST['json']);
    }
    //Провести списание
    if ($_POST['action'] == "conduct_writeoff") {
        echo $writeoff->conduct_writeoff($_POST['id']);
    }
    //Отменить проведение списания
    if ($_POST['action'] == "unconduct_writeoff") {
        echo $writeoff->unconduct_writeoff($_POST['id']);
    }
    //Создание новой версии робота
    if ($_POST['action'] == "add_equipment") {
        echo $position->add_equipment($_POST['json']);
    }
    //Создание новой опции для робота
    if ($_POST['action'] == "add_option") {
        echo $robots->add_option($_POST['version'], $_POST['title']);
    }
    //Удаление опции
    if ($_POST['action'] == "del_option") {
        echo $robots->del_option($_POST['id']);
    }
    //Создание нового заказа
    if ($_POST['action'] == "add_assembly") {
        echo $position->add_assembly($_POST['json']);
    }
    if ($_POST['action'] == "add_kit") {
        $position->add_kit($_POST['json']);
    }
    if ($_POST['action'] == "add_split_kit") {
        $position->add_split_kit($_POST['kit1'], $_POST['kit2']);
    }
    if ($_POST['action'] == "del_kit") {
        $position->del_kit($_POST['id']);
    }
    //Получение информации о заказе
    if ($_POST['action'] == "get_info_order") {
        echo json_encode($orders->get_info_order($_POST['id']), JSON_UNESCAPED_UNICODE);
    }
    //Получение списка заказов
    if ($_POST['action'] == "get_orders") {
        echo json_encode($orders->get_orders($_POST['id'], $_POST['status']), JSON_UNESCAPED_UNICODE);
    }
    //Получение списка позиций в заказе
    if ($_POST['action'] == "get_pos_in_order") {
        echo json_encode($orders->get_pos_in_order($_POST['id']), JSON_UNESCAPED_UNICODE);
    }
    //Редактирование заказа
    if ($_POST['action'] == "edit_order") {
        echo json_encode($orders->edit_order($_POST['id'], $_POST['category'], $_POST['provider'], $_POST['status'], $_POST['responsible'], $_POST['version'], $_POST['json']), JSON_UNESCAPED_UNICODE);
    }
    //Редактирование заказа
    if ($_POST['action'] == "edit_equipment") {
        echo json_encode($position->edit_equipment($_POST['id'], $_POST['json']), JSON_UNESCAPED_UNICODE);
    }
    //Редактирование заказа
    if ($_POST['action'] == "edit_assembly") {
        echo json_encode($position->edit_assembly($_POST['id'], $_POST['json']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "edit_kit") {
        echo json_encode($position->edit_kit($_POST['id'], $_POST['json']), JSON_UNESCAPED_UNICODE);
    }
    //Редактирование списания
    if ($_POST['action'] == "edit_writeoff") {
        echo json_encode($writeoff->edit_writeoff($_POST['id'], $_POST['json']), JSON_UNESCAPED_UNICODE);
    }
    //Редактирование только описания списания
    if ($_POST['action'] == "edit_description_writeoff") {
        echo json_encode($writeoff->edit_description_writeoff($_POST['id'], $_POST['description']), JSON_UNESCAPED_UNICODE);
    }
    //Удаление заказа
    if ($_POST['action'] == "del_order") {
        echo $_POST['id'];
        echo json_encode($orders->del_order($_POST['id']), JSON_UNESCAPED_UNICODE);
    }
    //Добавление поступления
    if ($_POST['action'] == "add_admission") {
        echo json_encode($admission->add_admission($_POST['order_id'], $_POST['json'], $_POST['category'], $_POST['provider'], $_POST['description']), JSON_UNESCAPED_UNICODE);
    }
    //Получение списка групп чек листа  
    if ($_POST['action'] == "get_checks_group") {
        echo json_encode($checks->get_checks_group($_POST['category']), JSON_UNESCAPED_UNICODE);
    }
    //добавление операции в чеклист
    if ($_POST['action'] == "add_check") {
        echo json_encode($checks->add_check($_POST['category'], $_POST['title'], $_POST['sort'], $_POST['version'], $_POST['kit']), JSON_UNESCAPED_UNICODE);
    }
    //добавление операции в чеклист
    if ($_POST['action'] == "add_check_on_option") {
        echo json_encode($checks->add_check_on_option($_POST['id_option'], $_POST['title'], $_POST['category'], $_POST['kit']), JSON_UNESCAPED_UNICODE);
    }
    
    
     if ($_POST['action'] == "add_option_check") {
        echo json_encode($checks->add_option_check($_POST['id'], $_POST['robot'], $_POST['value']), JSON_UNESCAPED_UNICODE);
    }
   
    
    
    //добавление операции в чеклист
    if ($_POST['action'] == "edit_check") {
        echo json_encode($checks->edit_check($_POST['id'], $_POST['title'], $_POST['kit'], $_POST['version']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "edit_check_on_option") {
        echo json_encode($checks->edit_check_on_option($_POST['id'], $_POST['title'], $_POST['category'], $_POST['kit']), JSON_UNESCAPED_UNICODE);
    }
    //добавление выполненной операции
    if ($_POST['action'] == "add_check_on_robot") {
        echo json_encode($checks->add_check_on_robot($_POST['id_row'], $_POST['robot'], $_POST['id'], $_POST['value'], $_POST['number'], $_POST['remont'], $_POST['kit']), JSON_UNESCAPED_UNICODE);
    }
    //добавление робота
    if ($_POST['action'] == "add_robot") {
        //echo $_POST['number'];
        $options = (isset($_POST['options'])) ? $_POST['options']: [];
        echo json_encode($robots->add_robot($_POST['number'], $_POST['name'], $_POST['version'], $options, $_POST['customer'], $_POST['owner'], $_POST['language_robot'], $_POST['language_doc'], $_POST['charger'], $_POST['color'], $_POST['brand'], $_POST['ikp'], $_POST['battery'], $_POST['dop'], $_POST['dop_manufactur'], $_POST['date_start'], $_POST['date_test'], $_POST['date_send'], $_POST['send'], $_POST['delivery'], $_POST['commissioning']), JSON_UNESCAPED_UNICODE);
    }
    /*старый код
    if ($_POST['action'] == "add_robot") {
        //echo $_POST['number'];
        echo json_encode($robots->add_robot($_POST['number'], $_POST['name'], $_POST['version'], $_POST['photo'], $_POST['termo'], $_POST['dispenser'], $_POST['terminal'], $_POST['kaznachey'], $_POST['lidar'], $_POST['other'], $_POST['customer'], $_POST['language_robot'], $_POST['language_doc'], $_POST['charger'], $_POST['color'], $_POST['brand'], $_POST['ikp'], $_POST['battery'], $_POST['dop'], $_POST['dop_manufactur'], $_POST['send'], $_POST['date'], $_POST['date_test']), JSON_UNESCAPED_UNICODE);
    }*/
    //редактирование робота
    if ($_POST['action'] == "edit_robot") {
        //echo $_POST['number'];
        $options = (isset($_POST['options'])) ? $_POST['options']: [];
        echo json_encode($robots->edit_robot($_POST['id'], $_POST['number'], $_POST['name'], $_POST['version'], $options, $_POST['customer'], $_POST['owner'], $_POST['language_robot'], $_POST['language_doc'], $_POST['charger'], $_POST['color'], $_POST['brand'], $_POST['ikp'], $_POST['battery'], $_POST['dop'],  $_POST['dop_manufactur'], $_POST['date_start'], $_POST['date_test'], $_POST['date_send'], $_POST['send'], $_POST['delivery'], $_POST['commissioning']), JSON_UNESCAPED_UNICODE);
    }
    //редактирование опции
    if ($_POST['action'] == "edit_option") {
        //echo $_POST['number'];
        echo json_encode($robots->edit_option($_POST['id'], $_POST['version'], $_POST['title']), JSON_UNESCAPED_UNICODE);
    }
    //сортировка роботов
    if ($_POST['action'] == "sortable") {
        //echo $_POST['number'];
        echo json_encode($robots->sortable($_POST['json']), JSON_UNESCAPED_UNICODE);
    }
    //сортировка чеков
    if ($_POST['action'] == "sortable_check") {
        //echo $_POST['number'];
        echo json_encode($checks->sortable($_POST['json']), JSON_UNESCAPED_UNICODE);
    }
    //добавление комментария к операции
    if ($_POST['action'] == "add_comment_on_check") {
        echo json_encode($checks->add_comment_on_check($_POST['id_row'], $_POST['robot'], $_POST['id'], $_POST['value'], $_POST['comment'], $_POST['number']), JSON_UNESCAPED_UNICODE);
    }
    //добавление комментария к операции
    if ($_POST['action'] == "add_log") {
        echo json_encode($robots->add_log($_POST['robot'], $_POST['level'], $_POST['comment'], $_POST['number']), JSON_UNESCAPED_UNICODE);
    }
    //удаление комментария к операции
    if ($_POST['action'] == "delete_log") {
        echo json_encode($robots->delete_log($_POST['id']), JSON_UNESCAPED_UNICODE);
    }
    //
    if ($_POST['action'] == "zabbix") {
        if (isset($_POST['client'])) {
            $client = $_POST['client'];
        } else {
            $client = 0;
        }
        echo json_encode($robots->add_log_width_zabbix($_POST['host'], $_POST['time'], $_POST['type'], $_POST['problem'], $_POST['total_uptime'], $_POST['type_message'], $_POST['id'], $_POST['status'], $client), JSON_UNESCAPED_UNICODE);

        $file = fopen('log.txt', 'a');
            foreach ($_REQUEST as $key => $val)
            {
                if($_POST['host']=="promobotv4_0167") {
                fwrite($file, $key . ' => ' . $val . "\n");
                fwrite($file, "\n");
                }
            }
            fclose($file);
    }

    if ($_POST['action'] == "ticket_get_subcategory") {
        echo json_encode($tickets->get_subcategory($_POST['category']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "ticket_get_category") {
        echo json_encode($tickets->get_category($_POST['type']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "ticket_add") {
        echo json_encode($tickets->add($_POST['robot'], $_POST['ticket_source'], $_POST['ticket_priority'], $_POST['ticket_class'], $_POST['category'], $_POST['subcategory'], $_POST['status'], $_POST['comment']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "delete_ticket") {
        echo json_encode($tickets->delete_ticket($_POST['id']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "ticket_edit") {
        echo json_encode($tickets->edit($_POST['id'], $_POST['category'], $_POST['subcategory'], $_POST['description']), JSON_UNESCAPED_UNICODE);
    }
    //создать комментарий для техпод
    if ($_POST['action'] == "ticket_add_comment") {
        echo json_encode($tickets->add_comment($_POST['robot'], $_POST['id'], $_POST['comment']), JSON_UNESCAPED_UNICODE);
        echo $_POST['comment'];
    }
    //создать комментарий для клиента
    if ($_POST['action'] == "ticket_add_comment_customers") {
        echo json_encode($tickets->add_comment_customers($_POST['robot'], $_POST['id'], $_POST['comment']), JSON_UNESCAPED_UNICODE);
        echo $_POST['comment'];
    }
    if ($_POST['action'] == "ticket_change_status") {
        echo json_encode($tickets->ticket_change_status($_POST['id'], $_POST['status']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "new_ticket_change_status") {
        echo json_encode($tickets->new_ticket_change_status($_POST['date'], $_POST['comment'], $_POST['id'], $_POST['status']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "ticket_arhiv") {
        echo json_encode($tickets->arhiv($_POST['id']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "ticket_add_result") {
        echo json_encode($tickets->ticket_add_result($_POST['id'], $_POST['result']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "ticket_add_date") {
        echo json_encode($tickets->ticket_add_date($_POST['id'], $_POST['date']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "ticket_add_category") {
        echo json_encode($tickets->add_category($_POST['title'], $_POST['cat_class']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "ticket_add_subcategory") {
        echo json_encode($tickets->add_subcategory($_POST['category'], $_POST['title']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "ticket_change_assign") {
        echo json_encode($tickets->change_assign($_POST['id'], $_POST['assign']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "ticket_get") {
        echo json_encode($tickets->get_tickets_kanban($_POST['robot'], $_POST['user'], $_POST['status'], $_POST['sortby'], $_POST['sortdir']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "change_auto_assign_for_user") {
        echo json_encode($tickets->change_auto_assign_for_user($_POST['id']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "ticket_info") {
        echo json_encode($tickets->info($_POST['id']), JSON_UNESCAPED_UNICODE);
    }
    //изменение приоритета чеклиста
    if ($_POST['action'] == "ticket_change_priority") {
        echo json_encode($tickets->change_priority($_POST['id'], $_POST['priority']), JSON_UNESCAPED_UNICODE);
    }
    //изменение источника чеклиста
    if ($_POST['action'] == "ticket_change_source") {
        echo json_encode($tickets->change_source($_POST['id'], $_POST['source']), JSON_UNESCAPED_UNICODE);
    }
    //удаление чеклиста
    if ($_POST['action'] == "del_check") {
        echo json_encode($checks->del_check($_POST['id'], $_POST['version']), JSON_UNESCAPED_UNICODE);
    }
    //удаление чеклиста в опции
    if ($_POST['action'] == "del_check_in_option") {
        echo json_encode($checks->del_check_in_option($_POST['id']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "del_contragent") {
        echo json_encode($position->del_provider($_POST['id']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "setPaymentStatus") {
        echo json_encode($orders->setPaymentStatus($_POST['id'],$_POST['value']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "print_info_robot") {
        echo json_encode($robots->print_info_robot($_POST['id']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "launch_production_robot") {
        echo json_encode($robots->launch_production_robot($_POST['id']), JSON_UNESCAPED_UNICODE);
    }

    //покупатели
    if ($_POST['action'] == "add_full_customer") {
        echo json_encode($robots->add_full_customer($_POST['name'], $_POST['fio'], $_POST['phone'], $_POST['email'], $_POST['address'], $_POST['inn'], $_POST['ident']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "edit_customer") {
        echo json_encode($robots->edit_customer($_POST['id'], $_POST['name'], $_POST['fio'], $_POST['phone'], $_POST['email'], $_POST['address'], $_POST['inn'], $_POST['ident']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "del_customer") {
        echo json_encode($robots->del_customer($_POST['id']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "get_info_customer") {
        $customer = $robots->get_customers();
        echo json_encode($customer[$_POST['id']], JSON_UNESCAPED_UNICODE);
    }

    //пользователи
    if ($_POST['action'] == "add_user") {
        echo json_encode($user->add_user($_POST['login'], $_POST['password'], $_POST['name'], $_POST['email'], $_POST['group']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "edit_user") {
        echo json_encode($user->edit_user($_POST['id'], $_POST['name'], $_POST['email'], $_POST['telegram'], $_POST['group']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "del_user") {
        echo json_encode($user->del_user($_POST['id']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "get_info_user") {
        echo json_encode($user->get_info_user($_POST['id']), JSON_UNESCAPED_UNICODE);
    }

    //статистика сборки
    if ($_POST['action'] == "change_status_robot_production_statistics") {
        echo json_encode($statistics->change_status_robot_production_statistics($_POST['id']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "del_robot_production_statistics") {
        echo json_encode($statistics->del_robot_production_statistics($_POST['id']), JSON_UNESCAPED_UNICODE);
    }

    //версии
    if ($_POST['action'] == "add_version") {
        echo json_encode($robots->add_version($_POST['title']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "edit_version") {
        echo json_encode($robots->edit_version($_POST['id'], $_POST['title']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "get_info_version") {
        echo json_encode($robots->getEquipment[$_POST['id']], JSON_UNESCAPED_UNICODE);
    }

    //сабверсии
    if ($_POST['action'] == "add_subversion") {
        echo json_encode($robots->add_subversion($_POST['version'], $_POST['title']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "edit_subversion") {
        echo json_encode($robots->edit_subversion($_POST['id'], $_POST['title']), JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['action'] == "get_info_subversion") {
        echo json_encode($robots->getSubVersion[$_POST['id']], JSON_UNESCAPED_UNICODE);
    }
}
if (isset($_GET['action'])) {
    //поиск позиций
    if ($_GET['action'] == "pos_search") {
        echo json_encode($position->search($_GET['term']), JSON_UNESCAPED_UNICODE);
    }
}
//print_r($_POST);
?>