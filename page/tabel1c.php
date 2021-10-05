<?php
class tabel1c
{
    const SEND_STATUS_SUCCESSFULLY = 200;
    const SEND_STATUS_UNSUCCESSFULLY = 500;
    const SEND_STATUS_NO = 0;
    const ENTITY_TYPE_DB = [
        "ITEM" => 10, //номенклатура
        "PROVIDER" => 20, //контрагенты
        "DEVICE" => 31, //!!!НУЖНО ДОБАВИТЬ В БАЗУ Комплектация и привязать к устройствам
        "PRODUCT" => 32, //роботы
        "ORDER" => 40, //заказ
        "ADMISSION" => 50, //поступление
        "TYPE_WRITEOFF" => 61, //!!!НУЖНО ДОБАВИТЬ В БАЗУ (есть только списком) и привязать к свободным списаниям
        "WRITEOFF" => 62, //списание
    ];
    const ENTITY_TYPE_1C = [
        "ITEM" => 10, //Номенклатура //Catalog_Номенклатура
        "PROVIDER" => 20, //Контрагенты //Catalog_Контрагенты
        "DEVICE" => 30, //Номенклатура //Catalog_Номенклатура
        "ORDER" => 40, //Document_Заказ
        "ADMISSION" => 50, //Document_Поступление
        "PRODUCTION_WRITEOFF" => 610, //???
        "PRODUCTION_WRITEOFF_DEVICE" => 611, //???
        "FREE_WRITEOFF" => 620, //Требование накладная //Document_ТребованиеНакладная
        "TYPE_WRITEOFF" => 621, //???
        "STOCK" => 70, //???
    ];
    const CRUD_TYPE = [
        "CREATE" => 1,
        "UPDATE" => 2,
        "DELETE" => 3,
        "POST_DOC" => 10,
        "UN_POST_DOC" => 11,
    ];

    private $pdo;

    function __construct()
    {
        global $database_server, $database_user, $database_password, $dbase, $dbconnect;
        $dsn = "mysql:host=$database_server;dbname=$dbase;charset=utf8";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $this->pdo = &$dbconnect->pdo;
    }

    function init()
    {

    }

    function get_status()
    {
        return [
            self::SEND_STATUS_SUCCESSFULLY => "доставлено",
            self::SEND_STATUS_UNSUCCESSFULLY => "ошибка",
            self::SEND_STATUS_NO => "не отправлялось",
        ];
    }

    function get_db_entity_types()
    {
        return [
            self::ENTITY_TYPE_DB["ITEM"] => "Номенклатура/Позиция",
            self::ENTITY_TYPE_DB["PROVIDER"] => "Контрагенты/Поставщик",
            self::ENTITY_TYPE_DB["DEVICE"] => "Роботы/Список комплектаций",
            self::ENTITY_TYPE_DB["PRODUCT"] => "Роботы/Робот",
            self::ENTITY_TYPE_DB["ORDER"] => "Заказы/Заказ",
            self::ENTITY_TYPE_DB["ADMISSION"] => "Поступления/Поступление",
            self::ENTITY_TYPE_DB["TYPE_WRITEOFF"] => "Списания/Список произвольных списаний",
            self::ENTITY_TYPE_DB["WRITEOFF"] => "Списания/Списание",

        ];
    }

    function get_1c_entity_types()
    {
        return [
            self::ENTITY_TYPE_1C["ITEM"] => "Catalog_Номенклатура",
            self::ENTITY_TYPE_1C["PROVIDER"] => "Catalog_Контрагенты",
            self::ENTITY_TYPE_1C["DEVICE"] => "Catalog_Номенклатура",
            self::ENTITY_TYPE_1C["ORDER"] => "Document_Заказ",
            self::ENTITY_TYPE_1C["ADMISSION"] => "Document_Поступление",
            self::ENTITY_TYPE_1C["PRODUCTION_WRITEOFF"] => "",
            self::ENTITY_TYPE_1C["PRODUCTION_WRITEOFF_DEVICE"] => "",
            self::ENTITY_TYPE_1C["FREE_WRITEOFF"] => "Document_Требования-накладная",
            self::ENTITY_TYPE_1C["TYPE_WRITEOFF"] => "",
            self::ENTITY_TYPE_1C["STOCK"] => "",
        ];
    }

    function get_crud_types()
    {
        return [
            self::CRUD_TYPE["CREATE"] => "Создание",
            self::CRUD_TYPE["UPDATE"] => "Изменение",
            self::CRUD_TYPE["DELETE"] => "Удаление",
            self::CRUD_TYPE["POST_DOC"] => "Провести",
            self::CRUD_TYPE["UN_POST_DOC"] => "Распровести",
        ];
    }

    //add send
    function add_send($type_crud, $type_db, $type_1c, $id_db)
    {
        $send_status = self::SEND_STATUS_NO;
        $query = "INSERT 
            INTO `tabel1c` (`id`, `type_crud`, `db_type`, `db_id`, `1c_type`, `1c_id`, `send_status`) 
            VALUES (NULL, '$type_crud', '$type_db', '$id_db', '$type_1c', NULL, '$send_status')";
        $res = $this->pdo->query($query);
        $idd = $this->pdo->lastInsertId();
        $result = ($idd) ? true : false;
        if ($result) {
            $res = $this->add_items_send($idd, $type_1c, $type_crud, $type_db, $id_db);
            $result = ($result && $res);
        }

        return $result;
    }

    //add send items
    function add_items_send($id, $type_1c, $type_crud, $type_db, $id_db)
    {
        switch ($type_1c) {
            case self::ENTITY_TYPE_1C["ITEM"]:
                $result = $this->add_items_item($id, $type_crud, $type_db, $id_db);
                break;
            case self::ENTITY_TYPE_1C["PROVIDER"]:
                $result = $this->add_items_provider($id, $type_crud, $type_db, $id_db);
                break;
            case self::ENTITY_TYPE_1C["DEVICE"]:
                $result = $this->add_items_device($id, $type_crud, $type_db, $id_db);
                break;
            case self::ENTITY_TYPE_1C["ORDER"]:
                $result = $this->add_items_order($id, $type_crud, $type_db, $id_db);
                break;
            case self::ENTITY_TYPE_1C["ADMISSION"]:
                $result = $this->add_items_admission($id, $type_crud, $type_db, $id_db);
                break;
            case self::ENTITY_TYPE_1C["PRODUCTION_WRITEOFF"]:
                $result = $this->add_items_production_writeoff($id, $type_crud, $type_db, $id_db);
                break;
            case self::ENTITY_TYPE_1C["PRODUCTION_WRITEOFF_DEVICE"]:
                $result = $this->add_items_production_writeoff_device($id, $type_crud, $type_db, $id_db);
                break;
            case self::ENTITY_TYPE_1C["FREE_WRITEOFF"]:
                $result = $this->add_items_free_writeoff($id, $type_crud, $type_db, $id_db);
                break;
            case self::ENTITY_TYPE_1C["TYPE_WRITEOFF"]:
                $result = $this->add_items_type_writeoff($id, $type_crud, $type_db, $id_db);
                break;
            case self::ENTITY_TYPE_1C["STOCK"]:
                $result = $this->add_items_stock($id, $type_crud, $type_db, $id_db);
                break;
            default:
                $result = true;
        }
        return $result;
    }

    //item
    function add_items_item($id, $type_crud, $type_db, $id_db)
    {
        return 0;
    }
    //provider
    function add_items_provider($id, $type_crud, $type_db, $id_db)
    {
        return 0;
    }
    //device
    function add_items_device($id, $type_crud, $type_db, $id_db)
    {
        return 0;
    }
    //order
    function add_items_order($id, $type_crud, $type_db, $id_db)
    {
        return 0;
    }
    //admission
    function add_items_admission($id, $type_crud, $type_db, $id_db)
    {
        return 0;
    }
    //production_writeoff
    function add_items_production_writeoff($id, $type_crud, $type_db, $id_db)
    {
        return 0;
    }
    //production_writeoff_device
    function add_items_production_writeoff_device($id, $type_crud, $type_db, $id_db)
    {
        return 0;
    }
    //free_writeoff
    function add_items_free_writeoff($id, $type_crud, $type_db, $id_db)
    {
        return 0;
    }
    //type_writeoff
    function add_items_type_writeoff($id, $type_crud, $type_db, $id_db)
    {
        return 0;
    }
    //stock
    function add_items_stock($id, $type_crud, $type_db, $id_db)
    {
        return 0;
    }



    function __destruct()
    {
    }
}
