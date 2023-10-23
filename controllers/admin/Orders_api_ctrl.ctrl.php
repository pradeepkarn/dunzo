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
        // print_r($orders_list);
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

        $curl = curl_init();
        $RESTAURANT_API_KEY = RESTAURANT_API_KEY;
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => REST_API_ENDPOINT.'/api/v1/get/orders',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "API_KEY: $RESTAURANT_API_KEY"
            ),
        ));

        $response = curl_exec($curl);
// echo $response;
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
