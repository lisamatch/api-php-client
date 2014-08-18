<?php

function __autoload($class_name)
{
    require_once('classes/' . $class_name . '.php');
}

$publicHash = ''; // your Public Key
$privateHash = ''; //your Private Key

$api = new MmpAPIClient($publicHash, $privateHash, realpath(dirname(__FILE__)) . '/ssl/mmprocrmapi.crt');

$response = $api->call('GET', 'members/:ID', array('mode' => 'basic')); // substitute :ID with numerical member ID

if ($response->status === true) {
    var_dump($response->data);
    var_dump($response->message);
} else {
    print_r('Error. Code:' . $response->httpCode . ' Message: ' . $response->message);
    var_dump($response->info);
}

echo "Executed";
exit();

//*********************** OTHER METHODS YOU CAN USE ****************************

$response = $api->call('GET', 'members/:ID', array('mode' => 'full'));
$response = $api->call('POST', 'members/create', array(
    'data' => array(
        'MemberContactDetails_photo' => base64_encode(file_get_contents('other/photo1.png')),
        'MemberPhoto_extraPhoto2' => base64_encode(file_get_contents('other/photo2.jpg')),
        'MemberContactDetails_firstName' => 'Tommy',
        'MemberContactDetails_lastName' => 'Smith',
        'MemberContactDetails_gender' => '0', //male
        'MemberContactDetails_email' => 'tommy@gmail.com',
        'MemberContactDetails_dob' => '03/11/1956', //DD-MM-YY or MM/DD/YY
        'MemberMatchingData_matchGender' => '1', //female
        'ownerAgentID' => '521',
        'memberStatus' => '1', //lead
        )));
$response = $api->call('DELETE', 'members/:ID');
$response = $api->call('GET', 'agents/show'); // get all agents under the company
$response = $api->call('GET', 'agents/:ID'); // get one agent by ID
$response = $api->call('GET', 'mpackages/show'); // get all membership packages under the company
$response = $api->call('GET', 'mpackages/:ID'); // get membership package by ID