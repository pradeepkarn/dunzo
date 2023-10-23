<?php
class Orders_api_ctrl extends DB_ctrl
{

    // List fuels
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
        $data = $this->order_list(order_group: $req->fg, ord: "DESC", limit: 10000, active: 1);
        if ($data->success == true) {
            $orders_list = $data->data;
        } else {
            $orders_list = [];
        }
        $tu = count($orders_list);
        if ($tu %  $data_limit == 0) {
            $tu = $tu / $data_limit;
        } else {
            $tu = floor($tu / $data_limit) + 1;
        }
        // if (isset($req->search)) {
        //     $order_list = $this->order_search_list(order_group: $req->fg, keyword: $req->search, ord: "DESC", limit: $page_limit, active: 1);
        // } else {
        //     $order_list = $this->order_list(order_group: $req->fg, ord: "DESC", limit: $page_limit, active: 1);
        // }
        $context = (object) array(
            'page' => 'orders/list.php',
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



    // User list
    public function order_list($order_group = "petrol", $ord = "DESC", $limit = 5, $active = 1)
    {
//         return json_decode('{
//         "success": true,
//         "data": [
//             {
//                 "id": 40,
//                 "driver_assigned": true,
//                 "orderid": "64f2fa99b0068",
//                 "driver_id": 128,
//                 "driver": "sumit",
//                 "buyer_id": 110,
//                 "buyer": "mail2pkarn",
//                 "buyer_lat": "26.19573",
//                 "buyer_lon": "86.01837",
//                 "rest_lat": "26.152548",
//                 "rest_lon": "85.894543",
//                 "driver_lat": "26.156999",
//                 "driver_lon": "85.899506",
//                 "user_to_rest": 16.612,
//                 "driver_to_user": 17.937,
//                 "distance_unit": "km"
//             },
//             {
//                 "id": 41,
//                 "driver_assigned": false,
//                 "orderid": "64f30965912a0",
//                 "driver_id": 0,
//                 "driver": null,
//                 "buyer_id": 110,
//                 "buyer": "mail2pkarn",
//                 "buyer_lat": "26.193144",
//                 "buyer_lon": "85.734601",
//                 "rest_lat": "26.152548",
//                 "rest_lon": "85.894543",
//                 "driver_lat": null,
//                 "driver_lon": null,
//                 "user_to_rest": 23.006,
//                 "driver_to_user": 0,
//                 "distance_unit": "km"
//             }
//         ],
//         "msg": "Data found\\n"
//     }');
// return;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => REST_API_ENDPOINT.'/v1/get/orders',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'API_KEY: 6SedFzPnMuFxC9L3hyLbLCJnevY+k8HAv6afu8WiQa0=',
                'Cookie: PHPSESSID=gb6o72lh20copj1ngiu7897ujb; lang=en'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }
    function order_search_list($order_group = "petrol", $keyword = "", $ord = "DESC", $limit = 5, $active = 1)
    {
        return [];
    }
    // User detail

    // render function
    public function render_main($context = null)
    {
        import("apps/admin/layouts/admin-main.php", $context);
    }
}
