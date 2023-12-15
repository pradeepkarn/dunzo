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
            $this->update_order($req = new stdClass);
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
        if (isset($req->status)) {
            $res =  $this->order_list_by_delv_status(ord: "DESC", limit: $page_limit, active: 1, delv_sts: $req->status);
        } else {
            $res = $this->order_list(ord: "DESC", limit: $page_limit, active: 1);
        }
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
                    "buyer_lat" => $apidata->lat,
                    "buyer_lon" => $apidata->lon,
                    "pickup_lat" => $apidata->pickup_lat,
                    "pickup_lon" => $apidata->pickup_lon
                );
                $d['api_data'] = $dat;
                $orders_list[] = $d;
            }
        } else {
            $orders_list = [];
        }
        // myprint($orders_list);

        $tu = $this->order_list_count($active = 1, $req->status ?? null)['total_orders'] ?? 0;
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
    function order_list_by_delv_status($ord = "DESC", $limit = 5, $active = 1, $delv_sts = 0)
    {
        $db = new Dbobjects;
        $db->tableName = "manual_orders";
        return $db->filter(assoc_arr: ['is_active' => $active, 'delivery_status' => $delv_sts], ord: $ord, limit: $limit);
    }
    function order_list($ord = "DESC", $limit = 5, $active = 1)
    {
        $db = new Dbobjects;
        $db->tableName = "manual_orders";
        return $db->filter(assoc_arr: ['is_active' => $active], ord: $ord, limit: $limit);
    }
    function order_list_count($active = 1, $delv_sts = null)
    {
        $delv_str  = null;
        if ($delv_sts) {
            $delv_str = "and delivery_status= '$delv_sts'";
        }
        $db = new Dbobjects;
        $db->tableName = "manual_orders";
        return $db->showOne("select COUNT(id) as total_orders from manual_orders where is_active=$active $delv_str");
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
                $sql = "INSERT INTO manual_orders 
                (created_at, name, email, phone, address, lat, lon, pickup_address, pickup_lat, pickup_lon, order_item, quantity, amount, order_type) 
                VALUES 
                (:created_at, :name, :email, :phone, :address, :lat, :lon, :pickup_address, :pickup_lat, :pickup_lon, :order_item, :quantity, :amount, :order_type)";
                // Loop through each row and insert data into the database
                $count = 0;
                for ($row = 2; $row <= $sheet->getHighestRow(); $row++) {
                    $created_at = $sheet->getCell('A' . $row)->getCalculatedValue();
                    $name = $sheet->getCell('B' . $row)->getCalculatedValue();
                    $email = $sheet->getCell('C' . $row)->getCalculatedValue();
                    $phone = $sheet->getCell('D' . $row)->getCalculatedValue();
                    $address = $sheet->getCell('E' . $row)->getCalculatedValue();
                    $loc = $sheet->getCell('F' . $row)->getCalculatedValue();
                    $pkg = $sheet->getCell('G' . $row)->getCalculatedValue();
                    $qty = $sheet->getCell('H' . $row)->getCalculatedValue();
                    $amt = $sheet->getCell('I' . $row)->getCalculatedValue();
                    $order_type = $sheet->getCell('J' . $row)->getCalculatedValue();

                    $created_at = $this->convertToValidDateFormat($created_at);
                    $cord = $this->separateCoordinates($coordinates = $loc);
                    $old_sql = "select id from manual_orders where email='$email' and created_at = '$created_at'";
                    $exists = $db->showOne($old_sql);
                    if (!$exists) {
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            msg_set("Valid Email not found in the row $row");
                        }
                        if ($cord[0] == '' || $cord[1] == '') {
                            msg_set("Valid location not found in the row $row");
                        }
                        $params = [
                            ':created_at' => $created_at,
                            ':name' => $name,
                            ':email' => $email,
                            ':phone' => $phone,
                            ':address' => $address,
                            ':pickup_address' => WAREHOUSE['pickup_address'],
                            ':pickup_lat' => WAREHOUSE['pickup_lat'],
                            ':pickup_lon' => WAREHOUSE['pickup_lon'],
                            ':lat' => $cord[0],
                            ':lon' => $cord[1],
                            ':order_item' => $pkg,
                            ':quantity' => $qty,
                            ':amount' => floatval($amt),
                            ':order_type' => intval($order_type) == 1 ? 1 : 0,
                        ];
                    } else {
                        msg_set("Duplicate entry found in the row $row");
                        $count = -2;
                    }
                    try {
                        if (isset($params)) {
                            $stmt = $db->pdo->prepare($sql);
                            if ($stmt->execute($params)) {
                                $count += 1;
                            }
                        }
                    } catch (PDOException $e) {
                        msg_set("Database import error");
                    }
                }
                msg_set("$count data imported");
                echo msg_ssn();
            }
        }
        exit;
    }
    function update_order($req = null)
    {
        $req = (object) $_POST;
        $rules = [
            'action' => 'required|string',
            'id' => 'required|numeric',
            'address' => 'required|string',
            'pickup_address' => 'required|string',
            'lat' => 'required|string',
            'lon' => 'required|string',
            'pickup_lat' => 'required|string',
            'pickup_lon' => 'required|string',
        ];
        $pass = validateData(data: arr($req), rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn(return: true));
            exit;
        } else {
            $db = $this->db;
            $old_sql = "select id from manual_orders where id='$req->id';";
            $exists = $db->showOne($old_sql);
            if ($exists) {
                try {
                    $params = [
                        ':id' => $req->id,
                        ':address' => $req->address,
                        ':pickup_address' => $req->pickup_address,
                        ':lat' => $req->lat,
                        ':lon' => $req->lon,
                        ':pickup_lat' => $req->pickup_lat,
                        ':pickup_lon' => $req->pickup_lon,
                    ];
                    $sql = "UPDATE manual_orders SET 
                    address = :address ,
                    pickup_address = :pickup_address ,
                    lat = :lat ,
                    lon = :lon ,
                    pickup_lat = :pickup_lat ,
                    pickup_lon = :pickup_lon 
                    WHERE id = :id";
                    $stmt = $db->pdo->prepare($sql);
                    if ($stmt->execute($params)) {
                        msg_set("data updated");
                    } else {
                        msg_set("data not updated");
                    }
                } catch (PDOException $e) {
                    msg_set("Database import error");
                }
            } else {
                msg_set("Object not found in database");
            }
            echo js_alert(msg_ssn(return: true));
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
    function separateCoordinates($coordinates)
    {
        // Trim leading and trailing spaces
        $coordinates = trim($coordinates);

        // Check if the string is empty or contains only a comma
        if (empty($coordinates) || $coordinates === ',') {
            return [null, null];
        }

        // Split the coordinates by comma
        $coordinatesArray = explode(',', $coordinates);

        // Trim each coordinate and remove empty values
        $coordinatesArray = array_map('trim', $coordinatesArray);
        $coordinatesArray = array_filter($coordinatesArray);

        // Check if there are at least two coordinates (latitude and longitude)
        if (count($coordinatesArray) < 2) {
            return [null, null];
        }

        // Return the separated coordinates
        return $coordinatesArray;
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
                $api['reload'] = true;
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
    function delete_bulk()
    {
        $action = $_POST['action'] ?? null;
        $ids = $_POST['selected_ids'] ?? null;
        if ($action != null && $action == "delete_selected_items" && $ids != null) {
            $num = count($ids);
            if ($num == 0) {
                echo js_alert('Object not seleted');
                exit;
            };
            $idsString = implode(',', $ids);
            $db = new Dbobjects;
            $pdo = $db->conn;
            $pdo->beginTransaction();
            $sql = "DELETE FROM manual_orders WHERE id IN ($idsString)";
            try {
                $db->show($sql);
                $pdo->commit();
                echo js_alert("$num Selected item deleted");
                echo RELOAD;
                return true;
            } catch (PDOException $pd) {
                $pdo->rollBack();
                echo js_alert('Database quer error');
                return false;
            }
        } else {
            echo js_alert('Action not or items not selected');
            exit;
        }
    }
}
