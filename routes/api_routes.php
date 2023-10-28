<?php
$api_routes = [
    "/api/v1/account/create/{ug}" => 'Users_api@create_account@name.createAccountApi',
    "/api/v1/account/login/{ug}" => 'Users_api@login@name.loginAccountApi',
    "/api/v1/account/login-via-token/{ug}" => 'Users_api@login_via_token@name.loginAccountViaTokenApi',
    "/api/v1/orders/list" => 'Orders_api@fetch_orders@name.fetchOrdersApi',
    "/api/v1/orders/driver/accept" => 'Orders_api@accept_order@name.acceptOrderApi',
    "/api/v1/orders/driver/history" => 'Orders_api@aorder_history@name.orderHIstoryApi',
    "/api/v1/account/update/location" => 'Orders_api@update_location@name.updateLocationApi',
];

