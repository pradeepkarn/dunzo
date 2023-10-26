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
        $req = obj($req);
        if (!isset($req->sts)) {
            msg_set('Provide orders status');
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }

        $ord_list = $this->order_list($order_group = "petrol", $ord = "DESC", $limit = 100, $active = 1);
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
    function order_list()
    {
        $arr = [];
        $data = $this->db->show("
        SELECT orders.id, orders.driver_id, pk_user.lat AS driver_lat, pk_user.lon AS driver_lon, orders.add_on_price AS local_price, orders.jsn AS api_data
        FROM orders
        LEFT JOIN pk_user ON pk_user.id = orders.driver_id;        
        ");

        // Check if data is not empty
        if (!empty($data)) {
            // Loop through the data and decode the JSON values
            foreach ($data as $d) {
                $d['id'] = intval($d['id']); // true parameter for associative array
                $apidata = json_decode($d['api_data']);
                $dat = array(
                    'id' => $apidata->id,
                    'orderid' => $apidata->orderid,
                    'buyer' => $apidata->buyer,
                    "buyer_id" => $apidata->buyer_id,
                    "buyer_lat" => $apidata->buyer_lat,
                    "buyer_lon" => $apidata->buyer_lon,
                    "rest_lat" => $apidata->rest_lat,
                    "rest_lon" => $apidata->rest_lon,
                    "distance_unit" => $apidata->distance_unit,
                    "user_to_rest" => $apidata->user_to_rest,
                );
                $d['api_data'] = $dat;
                $arr[] = $d;
            }
        }

        return $arr;
    }
    function update_location($req=null)  {
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
        }
        else {
            msg_set('Location not updated, user not found');
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
    }
}
