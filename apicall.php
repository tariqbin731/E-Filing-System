<?php

function getapidata($url){
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);
$data=[];
$result = json_decode($result, true); // Use true to decode as an associative array

if (isset($result['status']) && $result['status'] === true) { // Use strict comparison (===) for boolean
    if (isset($result['data'])) { // Check if 'data' exists
        $data=$result['data'];
    } 
} 
return $data;
}
?>
