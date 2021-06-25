<?php
include __DIR__.'/include.php';
include __DIR__.'/config.php';

// создаем объект API
$api = OneBoxOSAPIv2_Client::Init($boxURL, $login, $restAPIPassword, false);

$paramArray = [];
$paramArray['workflowid'] = 672;
//$paramArray['limit'] = 10000;
$paramArray['orderby'] = 'DESC';
$paramArray['orderfields'] = ['id', 'name', 'cdate'];
$a = $api->request('GET', 'api/v2/order/get/', $paramArray);

foreach ($a as $x) {
    print_r($x);
}

print "\n\ndone.\n\n";