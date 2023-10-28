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
const STATUS_CODES = array(
    0 => 'New Order',
    1 => 'Order Confirmed',
    2 => 'Driver Assigned',
    3 => 'Picked Up',
    4 => 'Delivered',
    5 => 'Cancelled',
    6 => 'Returned'
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

const RESTAURANT_API_KEY = "6SedFzPnMuFxC9L3hyLbLCJnevY+k8HAv6afu8WiQa0=";