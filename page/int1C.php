<?php
class Int1C
{
    const LOGIN_1C = [
        "authPROD" =>  [
            'odata_user',
            'Qq12345'
        ],
        "authDEV" =>  [
            'TestBX24',
            'sI7gi3pi'
        ],
    ];
    const LIST_I = [
        0 => "Нет",
        1 => "Да",
    ];

    public $getList;
    private $pdo;
    private $client;
    private $position;
    private $plan;


    function __construct()
    {
        global $database_server, $database_user, $database_password, $dbase, $dbconnect;
        $dsn = "mysql:host=$database_server;dbname=$dbase;charset=utf8";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        //$this->pdo = new PDO($dsn, $database_user, $database_password, $opt);
        $this->pdo = &$dbconnect->pdo;

        $this->client = new Kily\Tools1C\OData\Client('http://192.168.24.20/promobotnew/odata/standard.odata/',[
            'auth' => self::LOGIN_1C["authPROD"],
            'timeout' => 300,
        ]);

        /*$clientDev = new Kily\Tools1C\OData\Client('http://192.168.24.20/promobotnew/odata/standard.odata/',[
            'auth' => self::LOGIN_1C["authDEV"],
            'timeout' => 300,
        ]);*/
    }

    function init()
    {
        global $position, $plan;
        $this->position = $position;
        $this->plan = $plan;
        $this->getList = self::LIST_I;
    }

    //СКЛАДЫ
    function c_get_from_1c_warehouses()
    {
        $data = $this->client->{"Catalog_Склады"}->filter("IsFolder ne 1")->get();
        if($this->client->isOk()) {
            $result = $data->values();
        }
        return (isset($result)) ? $result : [];
    }
    function c_download_from_1c_warehouses()
    {
        $warehouses_1c = $this->c_get_from_1c_warehouses();
        $warehouses_db = $this->c_get_warehouses();
        $warehouses = [];
        foreach ($warehouses_db as $value) {
            $warehouses[$value['uuid']] = [
                "id" => $value['id'],
                "on" => $value['on'],
            ];
        }
        $warehouses_db = $warehouses;
        unset($warehouses);
        foreach ($warehouses_1c as $warehouse) {
            $deletion = $warehouse["DeletionMark"] ? 1 : 0;
            if (array_key_exists($warehouse["Ref_Key"], $warehouses_db)) {
                $this->c_update_warehouse($warehouses_db[$warehouse["Ref_Key"]]["id"], $warehouse["Description"], $warehouse["Ref_Key"], $warehouses_db[$warehouse["Ref_Key"]]["on"], $deletion);
                unset($warehouses_db[$warehouse["Ref_Key"]]);
            } else {
                $this->c_create_warehouse($warehouse["Description"], $warehouse["Ref_Key"], 0, $deletion);
            }
        }
        foreach ($warehouses_db as $warehouse) {
            $this->c_delete_warehouse($warehouse["id"]);
        }
        return ['result' => true, 'err' => ''];
    }
    function c_get_warehouses($id = null)
    {
        $cond = $id ? " WHERE `id` = $id" : "";
        $query = "SELECT * FROM `1c_warehouse`".$cond;
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $array[$line['id']] = $line;
        }
        return (isset($array)) ? $array : [];
    }
    function c_create_warehouse($title, $uuid, $on, $deletion)
    {
        $query = "INSERT INTO `1c_warehouse` (`id`, `title`, `uuid`, `on`, `deletion`) VALUES (NULL, '$title', '$uuid', '$on', '$deletion')";
        $result = $this->pdo->query($query);
        return true;
    }
    function c_update_warehouse($id, $title, $uuid, $on, $deletion)
    {
        $query = "UPDATE `1c_warehouse` SET `title` = '$title', `uuid` = '$uuid', `on` = '$on', `deletion` = '$deletion' WHERE `id` = '$id'";
        $result = $this->pdo->query($query);
        return true;
    }
    function c_delete_warehouse($id)
    {
        $query = "DELETE FROM `1c_warehouse` WHERE `id` = '$id'";
        $result = $this->pdo->query($query);
        return true;
    }

    //СЧЕТА
    function c_get_from_1c_accounts()
    {
        $data = $this->client->{"ChartOfAccounts_Хозрасчетный"}->get();
        if($this->client->isOk()) {
            $result = $data->values();
        }
        return (isset($result)) ? $result : [];
    }
    function c_download_from_1c_accounts()
    {
        $accounts_1c = $this->c_get_from_1c_accounts();
        $accounts_db = $this->c_get_accounts();
        $accounts = [];
        foreach ($accounts_db as $value) {
            $accounts[$value['uuid']] = [
                "id" => $value['id'],
                "storage" => $value['storage'],
                "transfer" => $value['transfer'],
            ];
        }
        $accounts_db = $accounts;
        unset($accounts);
        foreach ($accounts_1c as $account) {
            $deletion = $account["DeletionMark"] ? 1 : 0;
            if (array_key_exists($account["Ref_Key"], $accounts_db)) {
                $this->c_update_account($accounts_db[$account["Ref_Key"]]["id"], $account["Description"], $account["Code"], $account["Ref_Key"], $accounts_db[$account["Ref_Key"]]["storage"], $accounts_db[$account["Ref_Key"]]["transfer"], $deletion);
                unset($accounts_db[$account["Ref_Key"]]);
            } else {
                $this->c_create_account($account["Description"], $account["Code"], $account["Ref_Key"], 0, 0, $deletion);
            }
        }
        foreach ($accounts_db as $account) {
            $this->c_delete_account($account["id"]);
        }
        return ['result' => true, 'err' => ''];
    }
    function c_get_accounts($id = null)
    {
        $cond = $id ? " WHERE `id` = $id" : "";
        $query = "SELECT * FROM `1c_account`".$cond;
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $array[$line['id']] = $line;
        }
        return (isset($array)) ? $array : [];
    }
    function c_create_account($title, $code, $uuid, $storage, $transfer, $deletion)
    {
        $query = "INSERT INTO `1c_account` (`id`, `title`, `code`, `uuid`, `storage`, `transfer`, `deletion`) VALUES (NULL, '$title', '$code', '$uuid', '$storage', '$transfer', '$deletion')";
        $result = $this->pdo->query($query);
        return true;
    }
    function c_update_account($id, $title, $code, $uuid, $storage, $transfer, $deletion)
    {
        $query = "UPDATE `1c_account` SET `title` = '$title', `code` = '$code', `uuid` = '$uuid', `storage` = '$storage', `transfer` = '$transfer', `deletion` = '$deletion' WHERE `id` = '$id'";
        $result = $this->pdo->query($query);
        return true;
    }
    function c_delete_account($id)
    {
        $query = "DELETE FROM `1c_account` WHERE `id` = '$id'";
        $result = $this->pdo->query($query);
        return true;
    }

    //НОМЕНКЛАТУРА
    function c_get_from_1c_nomenclatures($parent = false)
    {
        if ($parent) {
            $data = $this->client->{"Catalog_Номенклатура"}->expand("Parent/Parent")->filter("DB_POS_ID ne 0")->get();
        } else {
            $data = $this->client->{"Catalog_Номенклатура"}->filter("DB_POS_ID ne 0")->get();
        }
        if($this->client->isOk()) {
            $result = $data->values();
        }
        return (isset($result)) ? $result : [];
    }
    function c_download_from_1c_nomenclatures()
    {
        $nomenclatures_1c = $this->c_get_from_1c_nomenclatures(true);
        $query = "TRUNCATE TABLE `1c_nomenclature`";
        $result = $this->pdo->query($query);
        if ($result) {
            foreach ($nomenclatures_1c as $nomenclature) {
                $deletion = $nomenclature["DeletionMark"] ? 1 : 0;
                $first_parent = (is_array($nomenclature["Parent"])) ? $nomenclature["Parent"]["Description"] : "-";
                $second_parent = (is_array($nomenclature["Parent"]) && is_array($nomenclature["Parent"]["Parent"])) ? $nomenclature["Parent"]["Parent"]["Description"] : "-";
                $this->c_create_nomenclature($nomenclature["DB_POS_ID"], $nomenclature["НаименованиеПолное"], $nomenclature["Артикул"], $nomenclature["Ref_Key"], $nomenclature["Code"], $first_parent, $second_parent, $deletion);
            }
            return ['result' => true, 'err' => ''];
        }
        return ['result' => false, 'err' => ''];
    }
    function c_get_nomenclatures($id = null)
    {
        $cond = $id ? " WHERE `1c_nomenclature`.`id` = $id" : "";
        $query = "SELECT * FROM `1c_nomenclature` JOIN `pos_items` ON `1c_nomenclature`.`pos_id` = `pos_items`.`id`".$cond;
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $array[$line['id']] = $line;
        }
        return (isset($array)) ? $array : [];
    }
    function c_create_nomenclature($pos_id, $title, $art, $uuid, $code, $first_parent, $second_parent, $deletion)
    {
        $query = "INSERT INTO `1c_nomenclature` (`id`, `pos_id`, `title`, `art`, `uuid`, `code`, `first_parent`, `second_parent`, `deletion`) VALUES (NULL, '$pos_id', '$title', '$art', '$uuid', '$code', '$first_parent', '$second_parent', '$deletion')";
        $result = $this->pdo->query($query);
        return true;
    }
    function c_update_nomenclature($id, $pos_id, $title, $art, $uuid, $code, $first_parent, $second_parent, $deletion)
    {
        $query = "UPDATE `1c_nomenclature` SET `pos_id` = '$pos_id', `title` = '$title', `art` = '$art', `uuid` = '$uuid', `code` = '$code', `first_parent` = '$first_parent', `second_parent` = '$second_parent', `deletion` = '$deletion' WHERE `id` = '$id'";
        $result = $this->pdo->query($query);
        return true;
    }
    function c_delete_nomenclature($id)
    {
        $query = "DELETE FROM `1c_nomenclature` WHERE `id` = '$id'";
        $result = $this->pdo->query($query);
        return true;
    }

    //ОСТАТКИ
    function c_get_from_1c_storage($date = null) //СКЛАДСКОЙ ИЗ 1С
    {
        $date = $date ? $date : date("Y-m-d");
        $accounts = $this->c_get_accounts();
        $plane = [];
        foreach ($accounts as $value) {
            if ($value['storage'] == 1 && $value['deletion'] != 1) {
                $plane[$value['uuid']] = $value;
            }
        }
        $plane_cond = [];
        foreach ($plane as $id => $value) {
            $plane_cond[] = "(Account_Key eq guid'$id')";
        }
        $plane_cond = (count($plane_cond)>0) ? implode($plane_cond, " or ") : '';
        $warehouses = $this->c_get_warehouses();
        $place = [];
        foreach ($warehouses as $value) {
            if ($value['on'] == 1 && $value['deletion'] != 1) {
                $place[$value['uuid']] = $value;
            }
        }
        $place_cond = [];
        foreach ($place as $id => $value) {
            $place_cond[] = "(ExtDimension3 eq cast(guid'$id','Catalog_Склады'))";
        }
        $place_cond = (count($place_cond)>0) ? implode($place_cond, " or ") : '';
        if ($plane_cond != '' && $place_cond != '') {
            $data = $this->client
                ->{"AccountingRegister_Хозрасчетный/Balance(Period=datetime'".$date."T23:59:59')"}
                //BalanceAndTurnovers(EndPeriod=datetime'".$date."T23:59:59',StartPeriod=datetime'".$date."T00:00:00')
                ->filter("(".$plane_cond.") and (".$place_cond.")")
                ->get();
            $storage = [];
            if($this->client->isOk()) {
                $result = $data->values();
                foreach ($result as $value) {
                    //$storage[$value["ExtDimension1"]][$plane[$value["Account_Key"]]["code"]][$place[$value["ExtDimension3"]]["title"]][] = $value["КоличествоBalance"];
                    if (key_exists($value["ExtDimension1"], $storage)) {
                        $storage[$value["ExtDimension1"]] = $storage[$value["ExtDimension1"]] + $value["КоличествоBalance"];
                    } else {
                        $storage[$value["ExtDimension1"]] = $value["КоличествоBalance"];
                    }
                }
                unset($result);
            }
        }
        return (isset($storage)) ? $storage : [];
    }
    function c_download_from_1c_storage()
    {
        $nomenclatures = $this->c_get_leftover();
        if ($nomenclatures == []) {
            return ['result' => false, 'err' => 'Сначала заполните номенклатуру!'];
        }
        $storage = $this->c_get_from_1c_storage();
        foreach ($nomenclatures as $nomenclature) {
            if (array_key_exists($nomenclature["uuid"], $storage)) {
                $this->c_update_leftover_one_by_uuid($nomenclature["uuid"], "storage", $storage[$nomenclature["uuid"]]);
            }
        }
        return ['result' => true, 'err' => 'Складские остатки успешно обнавлены!'];
    }
    function c_get_from_1c_transfer($date = null) //ДАВАЛЬЧЕСКИЙ ИЗ 1С
    {
        $date = $date ? $date : date("Y-m-d");
        $accounts = $this->c_get_accounts();
        $plane = [];
        foreach ($accounts as $value) {
            if ($value['transfer'] == 1 && $value['deletion'] != 1) {
                $plane[$value['uuid']] = $value;
            }
        }
        $plane_cond = [];
        foreach ($plane as $id => $value) {
            $plane_cond[] = "(Account_Key eq guid'$id')";
        }
        $plane_cond = (count($plane_cond)>0) ? implode($plane_cond, " or ") : '';
        if ($plane_cond != '') {
            $data = $this->client
                ->{"AccountingRegister_Хозрасчетный/Balance(Period=datetime'".$date."T23:59:59')"}
                //BalanceAndTurnovers(EndPeriod=datetime'".$date."T23:59:59',StartPeriod=datetime'".$date."T00:00:00')
                ->filter($plane_cond)
                ->get();
            $transfer = [];
            if($this->client->isOk()) {
                $result = $data->values();
                foreach ($result as $value) {
                    //$transfer[$value["ExtDimension2"]][$plane[$value["Account_Key"]]["code"]][$value["ExtDimension1"]][] = $value["КоличествоBalance"];
                    if (key_exists($value["ExtDimension2"], $transfer)) {
                        $transfer[$value["ExtDimension2"]] = $transfer[$value["ExtDimension2"]] + $value["КоличествоBalance"];
                    } else {
                        $transfer[$value["ExtDimension2"]] = $value["КоличествоBalance"];
                    }
                }
                unset($result);
            }
        }
        return (isset($transfer)) ? $transfer : [];
    }
    function c_download_from_1c_transfer()
    {
        $nomenclatures = $this->c_get_leftover();
        if ($nomenclatures == []) {
            return ['result' => false, 'err' => 'Сначала заполните номенклатуру!'];
        }
        $transfer = $this->c_get_from_1c_transfer();
        foreach ($nomenclatures as $nomenclature) {
            if (array_key_exists($nomenclature["uuid"], $transfer)) {
                $this->c_update_leftover_one_by_uuid($nomenclature["uuid"], "transfer", $transfer[$nomenclature["uuid"]]);
            }
        }
        return ['result' => true, 'err' => 'Давальческие остатки успешно обнавлены!'];
    }
    function c_get_leftover($id = null, $join = false)
    {
        $cond = "";
        $cond = $join ? $cond." JOIN `1c_nomenclature` ON `1c_leftover`.`uuid` = `1c_nomenclature`.`uuid` JOIN `pos_items` ON `1c_nomenclature`.`pos_id` = `pos_items`.`id`" : $cond;
        $cond = $id ? $cond." WHERE `1c_leftover`.`id` = $id" : $cond;
        //
        $query = "SELECT * FROM `1c_leftover`".$cond;
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $array[$line['id']] = $line;
        }
        return (isset($array)) ? $array : [];
    }
    function c_create_leftover($uuid, $storage, $transfer)
    {
        $query = "INSERT INTO `1c_leftover` (`id`, `uuid`, `storage`, `transfer`) VALUES (NULL, '$uuid', '$storage', '$transfer')";
        $result = $this->pdo->query($query);
        return true;
    }
    function c_update_leftover_one_by_uuid($uuid, $name, $value)
    {
        $query = "UPDATE `1c_leftover` SET `$name` = '$value' WHERE `uuid` = '$uuid'";
        $result = $this->pdo->query($query);
        return true;
    }
    function c_renew_leftover()
    {
        $query = "TRUNCATE TABLE `1c_leftover`";
        $result = $this->pdo->query($query);
        $nomenclatures = $this->c_get_nomenclatures();
        foreach ($nomenclatures as $nomenclature) {
            $this->c_create_leftover($nomenclature["uuid"], 0, 0);
        }
        return ['result' => true, 'err' => 'Таблица остатков успешно обнавлена!'];
    }
    function c_get_leftover_for_position()
    {
        $query = "SELECT * FROM `1c_leftover` JOIN `1c_nomenclature` ON `1c_leftover`.`uuid` = `1c_nomenclature`.`uuid`";
        $result = $this->pdo->query($query);
        while ($line = $result->fetch()) {
            $array[$line['pos_id']] = $line;
        }
        return (isset($array)) ? $array : [];
    }

    //ИНВЕНТАРИЗАЦИЯ
    function c_invent_from_1c_leftovers()
    {
        $nomenclatures = $this->c_get_leftover_for_position();
        if ($nomenclatures == []) {
            return ['result' => false, 'err' => 'Сначала заполните номенклатуру!'];
        }
        foreach ($nomenclatures as $id => $nomenclature) {
            $new_total = $nomenclature['storage'] + $nomenclature['transfer'];
            $this->position->invent($id, $new_total, "ОСТАТКИ ИЗ 1С", true);
        }
        return ['result' => true, 'err' => 'Остатки успешно загружены в DB!'];
    }

    //ДОП ПО РЕЗЕРВАМ
    function change_reserv_to_null() {
        $query = "UPDATE `pos_items` SET `reserv` = 0 WHERE `reserv` != 0";
        $result = $this->pdo->query($query);
        return ['result' => true, 'err' => 'Все резервы обнулены!'];
    }
    function add_new_reserv() {
        $query = "
            SELECT * FROM `check` 
            JOIN `robots` ON `check`.`robot` = `robots`.`id` 
            WHERE `robots`.`delete` = 0 
                AND `robots`.`writeoff` = 0 
                AND `robots`.`remont` = 0 
                AND `check`.`check` = 0 
                AND `check`.`id_kit` != 0
                AND `robots`.`progress` != 100
        ";
        $result = $this->pdo->query($query);
        $arr_kits = $this->plan->get_kits();
        while ($line = $result->fetch()) {
            $checks[] = $line;
        }
        $col_kits = [];
        foreach ($checks as $check) {
            if (isset($col_kits[$check['id_kit']])) {
                $col_kits[$check['id_kit']]++;
            } else {
                $col_kits[$check['id_kit']] = 1;
            }
        }
        foreach ($arr_kits as $id_kit => $positions) {
            if (isset($col_kits[$id_kit])) {
                foreach ($positions as $id_pos => $count) {
                    $arr_kits[$id_kit][$id_pos] = $count * $col_kits[$id_kit];
                }
            } else {
                unset($arr_kits[$id_kit]);
            }
        }
        foreach ($arr_kits as $arr_pos) {
            $this->position->add_reserv($arr_pos);
        }
        return ['result' => true, 'err' => 'Созданы резервы по всем активным чеклистам!'];
    }

    function __destruct()
    {
    }
}
