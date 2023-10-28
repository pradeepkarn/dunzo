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

        // SELECT orders.id, orders.driver_id, pk_user.lat AS driver_lat, pk_user.lon AS driver_lon, orders.add_on_price AS local_price, orders.jsn AS api_data
        // FROM orders
        // LEFT JOIN pk_user ON pk_user.id = orders.driver_id;        
        // Check if data is not empty
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
    function accept_order($req = null)
    {
        header('Content-Type: application/json');
        $ok = true;
        $req = obj($req);
        $data  = json_decode(file_get_contents('php://input'));
        if (isset($req->ug)) {
            if (!in_array($req->ug, USER_GROUP_LIST)) {
                $ok = false;
                msg_set("Invalid account group");
            }
        } else {
            $ok = false;
            msg_set("No user group provided");
        }
        if (!$ok) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
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
            if ($user['user_group'] != $req->ug) {
                $ok = false;
                msg_set("Invalid login portal");
                $api['success'] = false;
                $api['data'] = null;
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            }


            



            msg_set("User found");
            $api['success'] = true;
            $api['data'] = $user;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        } else {
            msg_set("User not found, invalid token");
            $api['success'] = true;
            $api['data'] = $user;
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
