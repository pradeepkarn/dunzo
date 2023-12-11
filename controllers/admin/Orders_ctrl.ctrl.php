<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

class Orders_ctrl
{
    public $db;
    function __construct()
    {
        $this->db = (new DB_ctrl)->db;
    }
    public function import_orders($req = null)
    {
        if (isset($_POST['action'], $_FILES)) {
            $this->save_imported_orders($req = new stdClass);
            exit;
        }
        $context = (object) array(
            'page' => 'allorders/create.php',
            'data' => (object) array(
                'req' => obj($req),
                'is_active' => true
            )
        );
        $this->render_main($context);
    }
    public function edit_order($req = null)
    {
        if (isset($_POST['action'], $_FILES)) {
            $this->save_imported_orders($req = new stdClass);
            exit;
        }
        $req = obj($req);
        $order = $this->db->showOne("select * from manual_orders where id = $req->id");
        $context = (object) array(
            'page' => 'allorders/edit.php',
            'data' => (object) array(
                'req' => obj($req),
                'is_active' => true,
                'order_detail' => $order,
            )
        );
        $this->render_main($context);
    }
    public function list($req = null)
    {
        $req = obj($req);
        $current_page = 0;
        $data_limit = DB_ROW_LIMIT;
        $page_limit = "0,$data_limit";
        $cp = 0;
        if (isset($req->page) && intval($req->page)) {
            $cp = $req->page;
            $current_page = (abs($req->page) - 1) * $data_limit;
            $page_limit = "$current_page,$data_limit";
        }
        $res = $this->order_list(ord:"ASC", limit:$page_limit, active:1);
        if ($res) {
            $orders_list = [];
            foreach ($res as $d) {
                // myprint($d);
                $apidata = obj($d); // true parameter for associative array
                // $user_to_driver = $apidata->user_to_rest*1000 + $driver_to_rest;
                $dat = array(
                    'id' => $apidata->id,
                    'orderid' => $apidata->id,
                    'buyer' => $apidata->name,
                    'buyer_name' => $apidata->name,
                    "buyer_id" => $apidata->email,
                    "buyer_lat" => "27.7987",
                    "buyer_lon" => "76.8777",
                    "rest_lat" => '78.87',
                    "rest_lon" => "87.89797979",
                    "distance_unit" => 'km',
                    "user_to_rest" => '23'
                );
                $d['api_data'] = $dat;
                $orders_list[] = $d;
            }
        } else {
            $orders_list = [];
        }
        // myprint($orders_list);
        
        $tu = $this->order_list_count($active = 1)['total_orders']??0;
        if ($tu %  $data_limit == 0) {
            $tu = $tu / $data_limit;
        } else {
            $tu = floor($tu / $data_limit) + 1;
        }
        $context = (object) array(
            'page' => 'allorders/list.php',
            'data' => (object) array(
                'req' => obj($req),
                'orders_list' => $orders_list,
                'total_orders' => $tu,
                'current_page' => $cp,
                'is_active' => true
            )
        );
        $this->render_main($context);
    }
    function order_list($ord = "DESC", $limit = 5, $active = 1)
    {
        $db = new Dbobjects;
        $db->tableName = "manual_orders";
        return $db->filter(assoc_arr: ['is_active' => $active], ord: $ord, limit: $limit);
    }
    function order_list_count($active = 1)
    {
        $db = new Dbobjects;
        $db->tableName = "manual_orders";
        return $db->showOne("select COUNT(id) as total_orders from manual_orders where is_active=$active");
    }
    function save_imported_orders($req = null)
    {
        $req = (object) $_POST;
        $req->sheet = (object) $_FILES ?? null;
        $rules = [
            'action' => 'required|string',
            'sheet' => 'required|file',
        ];
        $pass = validateData(data: arr($req), rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn(return: true));
            exit;
        } else {
            $db = new Dbobjects;
            if (isset($req->sheet->sheet)) {
                $file = $req->sheet->sheet;
                // myprint($file);
                $spreadsheet = IOFactory::load($file['tmp_name']);

                $sheet = $spreadsheet->getActiveSheet();

                // Prepare the SQL statement
                $sql = "INSERT INTO manual_orders (created_at, name, email, phone, address, quantity, amount) VALUES (:created_at, :name, :email, :phone, :address, :quantity, :amount)";
                // Loop through each row and insert data into the database
                for ($row = 2; $row <= $sheet->getHighestRow(); $row++) {
                    $created_at = $sheet->getCell('A' . $row)->getCalculatedValue();
                    $created_at = $this->convertToValidDateFormat($created_at);
                    $email = $sheet->getCell('C' . $row)->getCalculatedValue();
                    $qty = $sheet->getCell('F' . $row)->getCalculatedValue();
                    $amt = $sheet->getCell('G' . $row)->getCalculatedValue();
                    $old_sql = "select id from manual_orders where email='$email' and created_at = '$created_at'";
                    $exists = $db->showOne($old_sql);
                    if (!$exists) {
                        if ($email==null) {
                            msg_set("Email not found in the entry");
                        }
                        $params = [
                            ':created_at' => $created_at,
                            ':name' => $sheet->getCell('B' . $row)->getCalculatedValue(),
                            ':email' => $email,
                            ':phone' => $sheet->getCell('D' . $row)->getCalculatedValue(),
                            ':address' => $sheet->getCell('E' . $row)->getCalculatedValue(),
                            ':quantity' => $qty,
                            ':amount' => floatval($amt),
                        ];
                    } else {
                        msg_set("Duplicate entry found");
                    }
                    try {
                        if (isset($params)) {
                            $stmt = $db->pdo->prepare($sql);
                            if($stmt->execute($params)){
                                msg_set("data imported $amt");
                            }else{
                                msg_set("data not imported $amt");
                            }
                        } else {
                            msg_set("data not imported");
                        }
                    } catch (PDOException $e) {
                        msg_set("Database import error");
                    }
                }
                echo js_alert(msg_ssn(return: true));
            }
        }
        exit;
    }
    function convertToValidDateFormat($inputDate)
    {
        // Remove extra spaces and trim the input
        $cleanedDate = trim(preg_replace('/\s+/', ' ', $inputDate));

        // Define possible date formats
        $dateFormats = [
            'm/d/y, h:i A',
            'm/d/y, h:iA',
            'm/d/y, h A',
            'm/d/y, hA',
        ];

        // Try to parse the date using each format
        foreach ($dateFormats as $format) {
            $dateTime = DateTime::createFromFormat($format, $cleanedDate);
            if ($dateTime !== false) {
                // Return the date in a specific format (adjust as needed)
                return $dateTime->format('Y-m-d H:i:s');
            }
        }

        // Return null if the date couldn't be parsed
        return null;
    }
    function update_addon_price($req = null)
    {
        $rules = [
            'orderid' => 'required|string',
        ];

        $pass = validateData(data: $_POST, rules: $rules);
        if (!$pass) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $d = obj($_POST);
        $driver_id = $d->driver_id ?? "0";
        $this->db->tableName = 'orders';
        $this->db->insertData['add_on_price'] = floatval($d->add_on_price ?? 0);


        if ($driver_id != "0") {
            $ruuning = $this->db->showOne("select * from manual_orders where driver_id = '{$driver_id}' and delivery_status IN (0,1)");
            if ($ruuning) {
                msg_set("Driver already assigned");
            } else {
                $this->db->insertData['driver_id'] = $driver_id;
            }
        } else {
            $this->db->insertData['driver_id'] = "0";
        }
        $arready = $this->db->showOne("select id from manual_orders where id = '$d->orderid'");
        if ($arready) {
            $this->db->tableName = 'manual_orders';
            $this->db->pk($arready['id']);
            if ($this->db->update()) {
                msg_set('Orders updated');
                $api['success'] = true;
                $api['data'] = [];
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            } else {
                msg_set('Orders not updated');
                $api['success'] = false;
                $api['data'] = null;
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            }
        } else {
            msg_set('Orders not found');
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
    }
    function update_status($req = null)
    {
        $rules = [
            'orderid' => 'required|string',
        ];
        $pass = validateData(data: $_POST, rules: $rules);
        if (!$pass) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $d = obj($_POST);
        $this->db->tableName = 'manual_orders';
        $this->db->insertData['delivery_status'] = floatval($d->delivery_status ?? 0);
        $arready = $this->db->showOne("select id from manual_orders where id = '$d->orderid'");
        if ($arready) {
            $this->db->tableName = 'manual_orders';
            $this->db->pk($arready['id']);
            if ($this->db->update()) {
                msg_set('Order updated');
                $api['success'] = true;
                $api['data'] = [];
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            } else {
                msg_set('Order not updated');
                $api['success'] = false;
                $api['data'] = null;
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            }
        }
    }
    // render function
    public function render_main($context = null)
    {
        import("apps/admin/layouts/admin-main.php", $context);
    }
}
