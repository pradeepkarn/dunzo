<?php
$api_routes = [
    "/api/v1/account/create/{ug}" => 'Users_api@create_account@name.createAccountApi',
    "/api/v1/account/login/{ug}" => 'Users_api@login@name.loginAccountApi',
    "/api/v1/account/login-via-token/{ug}" => 'Users_api@login_via_token@name.loginAccountViaTokenApi',

    "/api/v1/fuels/show-fuels" => 'Fuels_api@get_fules@name.getFuelsApi',

    "/api/v1/orders/list" => 'Orders_api@fetch_orders@name.fetchOrdersApi',
    "/api/v1/orders/driver/accept" => 'Orders_api@accept_order@name.acceptOrderApi',
    "/api/v1/orders/driver/status-change/{delivery_status}" => 'Orders_api@status_update_order@name.statustOrderApi',

    "/api/v1/orders/driver/history" => 'Orders_api@order_history@name.orderHistoryApi',
    "/api/v1/orders/driver/running" => 'Orders_api@running_orders@name.orderRunningApi',
    "/api/v1/account/update/location" => 'Orders_api@update_location@name.updateLocationApi',

    '/api/orders/update-single-order' => 'Orders_api_ctrl@update_on_purchase_event_from_client@name.updateSingleOrder',
];

