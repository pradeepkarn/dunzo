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
        $data = $this->db->show("SELECT id, add_on_price as local_price, jsn as api_data FROM orders;");

        // Check if data is not empty
        if (!empty($data)) {
            // Loop through the data and decode the JSON values
            foreach ($data as $d) {
                $d['id'] = intval($d['id']); // true parameter for associative array
                $d['api_data'] = json_decode($d['api_data'], true); // true parameter for associative array
                $arr[] = $d;
            }
        }

        return $arr;
    }
}
