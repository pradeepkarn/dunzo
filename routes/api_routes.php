<?php
$api_routes = [
    "/api/v1/account/create/{ug}" => 'Users_api@create_account@name.createAccountApi',
    "/api/v1/account/login/{ug}" => 'Users_api@driver_login@name.loginAccountApi',

    "/api/v1/users/search" => 'Users_api@search_users@name.searchUsersApi',
];

