<?php

// The limit of data fetch from database at an attempt
const DB_ROW_LIMIT = 100;
const FRONT_ROW_LIMIT = 10;

const USER_ROLES = array(
    'subscriber'=>'Subscriber',
    'author'=>'Author',
    'editor'=>'Editor', 
    'admin'=>'Admin'
);

const ADMIN_ROLES = array(
    'subscriber'=>'Subscriber',
    'author'=>'Author',
    'editor'=>'Editor', 
    'shop_manager'=>'Shop Manager'
);

const USER_GROUP = array(
    'admin'=>'admin',
    'user'=>'user',
    'driver'=>'driver',
);
const USER_GROUP_LIST = ['driver','user'];




