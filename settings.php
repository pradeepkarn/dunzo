<?php

// The limit of data fetch from database at an attempt
const DB_ROW_LIMIT = 10;
const FRONT_ROW_LIMIT = 10;

const USER_ROLES = array(
    'subscriber'=>'Subscriber',
    'author'=>'Author',
    'editor'=>'Editor', 
    'admin'=>'Admin'
);
const STATUS_CODES = array(
    0 => 'New order',
    1 => 'Picked Up',
    2 => 'Delivered',
    3 => 'Cancelled'
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