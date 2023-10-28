<?php

class Orders_api
{
    public $db;
    function __construct()
    {
        $this->db = (new DB_ctrl)->db;
    }
    function fetch_orders($req = null)
    {
        header("Content-type:application/json");
        // $req = obj($req);
        $req = obj($_GET);
        if (!isset($req->status)) {
            msg_set('Provide orders status');
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $req->status = urldecode($req->status);
        $req->status = json_decode($req->status, true);
        if (!is_array($req->status)) {
            msg_set('Invalid status format');
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $ord_list = $this->order_list($status = $req->status);
        if ($ord_list) {
            msg_set('Orders found');
            $api['success'] = true;
            $api['data'] = $ord_list;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        } else {
            msg_set('No orders are available');
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
    }
    function order_list($status = [0])
    {
        $arr = [];
        $statusString = implode(',', $status);
        // echo $statusString;
        $data = $this->db->show("
        SELECT orders.id, orders.delivery_status, orders.driver_id, orders.add_on_price, orders.jsn AS api_data
        FROM orders
        LEFT JOIN pk_user ON pk_user.id = orders.driver_id 
        where orders.delivery_status IN ($statusString)
        ;");
        if (!empty($data)) {
            // Loop through the data and decode the JSON values
            foreach ($data as $d) {
                $d['id'] = intval($d['id']); // true parameter for associative array
                $d['delivery_status_text'] = getStatusText($d['delivery_status']);
                $apidata = json_decode($d['api_data']);
                $dat = array(
                    // 'id' => $apidata->id,
                    'orderid' => $apidata->orderid,
                    'is_prepaid' => strtolower($apidata->payment_method) == 'cod' ? false : true,
                    'amount' => strtolower($apidata->payment_method) == 'cod' ? $apidata->amount : "0",
                    'created_at' => $apidata->created_at,
                    'buyer_name' => $apidata->buyer_name,
                    "buyer_id" => $apidata->buyer_id,
                    "buyer_lat" => $apidata->buyer_lat,
                    "buyer_lon" => $apidata->buyer_lon,
                    "rest_id" => $apidata->rest_id,
                    'isd_code' => $apidata->isd_code,
                    'mobile' => $apidata->mobile,
                    'address' => $apidata->landmark,
                    'city' => $apidata->city,
                    'state' => $apidata->state,
                    'country' => $apidata->country,
                    "rest_name" => $apidata->rest_name,
                    "rest_address" => $apidata->rest_address,
                    "rest_lat" => $apidata->rest_lat,
                    "rest_lon" => $apidata->rest_lon,
                    "distance_unit" => $apidata->distance_unit,
                    "user_to_rest" => $apidata->user_to_rest,
                    "logo" => null,
                );
                $d['api_data'] = $dat;
                $arr[] = $d;
            }
        }
        // $arr['status_codes'] = obj(STATUS_CODES);
        return $arr;
    }
    function order_list_by_driver($driver_id)
    {
        $arr = [];
        $data = $this->db->show("
        SELECT orders.id, orders.delivery_status, orders.driver_id, orders.add_on_price, orders.jsn AS api_data
        FROM orders
        LEFT JOIN pk_user ON pk_user.id = orders.driver_id 
        where orders.driver_id = '$driver_id';");
        if (!empty($data)) {
            // Loop through the data and decode the JSON values
            foreach ($data as $d) {
                $d['id'] = intval($d['id']); // true parameter for associative array
                $d['delivery_status_text'] = getStatusText($d['delivery_status']);
                $apidata = json_decode($d['api_data']);
                $dat = array(
                    // 'id' => $apidata->id,
                    'orderid' => $apidata->orderid,
                    'is_prepaid' => strtolower($apidata->payment_method) == 'cod' ? false : true,
                    'amount' => strtolower($apidata->payment_method) == 'cod' ? $apidata->amount : "0",
                    'created_at' => $apidata->created_at,
                    'buyer_name' => $apidata->buyer_name,
                    "buyer_id" => $apidata->buyer_id,
                    "buyer_lat" => $apidata->buyer_lat,
                    "buyer_lon" => $apidata->buyer_lon,
                    "rest_id" => $apidata->rest_id,
                    'isd_code' => $apidata->isd_code,
                    'mobile' => $apidata->mobile,
                    'address' => $apidata->landmark,
                    'city' => $apidata->city,
                    'state' => $apidata->state,
                    'country' => $apidata->country,
                    "rest_name" => $apidata->rest_name,
                    "rest_address" => $apidata->rest_address,
                    "rest_lat" => $apidata->rest_lat,
                    "rest_lon" => $apidata->rest_lon,
                    "distance_unit" => $apidata->distance_unit,
                    "user_to_rest" => $apidata->user_to_rest,
                    "logo" => null,
                );
                $d['api_data'] = $dat;
                $arr[] = $d;
            }
        }
        return $arr;
    }
    function order_history($req = null)
    {
        header('Content-Type: application/json');
        $ok = true;
        $req = obj($req);
        $data  = json_decode(file_get_contents('php://input'));

        $rules = [
            'token' => 'required|string'
        ];

        $pass = validateData(data: arr($data), rules: $rules);
        if (!$pass) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $user = false;
        $user = (new Users_api)->get_user_by_token($data->token);
        if ($user) {
            if ($user['user_group'] != 'driver') {
                $ok = false;
                msg_set("Invalid login portal");
                $api['success'] = false;
                $api['data'] = null;
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            }
            
            try {
                $dt = $this->order_list_by_driver($driver_id = $user['id']);
                msg_set("Orders found");
                $api['success'] = count($dt) ? true : false;
                $api['data'] = count($dt) ? $dt : null;
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            } catch (PDOException $th) {
                // echo $th;
                msg_set("Unable to fetch");
                $api['success'] = false;
                $api['data'] = null;
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            }
        } else {
            msg_set("User not found, invalid token");
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
    }
    function accept_order($req = null)
    {
        header('Content-Type: application/json');
        $ok = true;
        $req = obj($req);
        $data  = json_decode(file_get_contents('php://input'));

        $rules = [
            'token' => 'required|string',
            'orderid' => 'required|string'
        ];

        $pass = validateData(data: arr($data), rules: $rules);
        if (!$pass) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $user = false;
        $user = (new Users_api)->get_user_by_token($data->token);
        if ($user) {
            if ($user['user_group'] != 'driver') {
                $ok = false;
                msg_set("Invalid login portal");
                $api['success'] = false;
                $api['data'] = null;
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            }
            // if (!isset($data->delivery_status)) {
            //     msg_set("Provide delivery status");
            //     $api['success'] = false;
            //     $api['data'] = null;
            //     $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            //     echo json_encode($api);
            //     exit;
            // }
            $db = $this->db;
            $pdo = $db->conn;
            $pdo->beginTransaction();
            try {
                $db->tableName = 'orders';
                $db->insertData['driver_id'] = $user['id'];
                // $db->insertData['delivery_status'] = $data->delivery_status;
                $db->findOne(['unique_id' => $data->orderid]);
                $db->update();
                $pdo->commit();
                msg_set("Assigned");
                $api['success'] = true;
                $api['data'] = [];
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            } catch (PDOException $th) {
                // echo $th;
                msg_set("Not Assigned");
                $api['success'] = false;
                $api['data'] = null;
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                $pdo->rollBack();
                exit;
            }
        } else {
            msg_set("User not found, invalid token");
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
    }
    function update_location($req = null)
    {
        $req = obj($req);
        $rules = [
            'token' => 'required|string',
            'lat' => 'required|string',
            'lon' => 'required|string',
        ];
        $reqdata = json_decode(file_get_contents("php://input"));
        $pass = validateData(data: arr($reqdata), rules: $rules);
        if (!$pass) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $update = $this->db->execSql("update pk_user set lat = '$reqdata->lat', lon = '$reqdata->lon' where app_login_token = '$reqdata->token'");
        if ($update) {
            msg_set('Location updated');
            $api['success'] = true;
            $api['data'] = [];
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        } else {
            msg_set('Location not updated, user not found');
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
    }
}
