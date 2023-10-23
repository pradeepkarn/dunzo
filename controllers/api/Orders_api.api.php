<?php

class Users_api extends Main_ctrl
{
    function fetch_orders()
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://localhost/webartroot/restaurant/api/v1/get/orders',
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
        $php = json_decode($response);
    }
}
