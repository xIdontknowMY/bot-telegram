<?php

// Dibangunkan oleh xIdontknow

// GANTIKAN BOT TOKEN ANDA PADA LINE 9 DAN 97

error_reporting(0);

define('BOT_TOKEN', 'GANTI SINI'); // GANTI SINI 
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');

function exec_curl_request($handle) {
  $response = curl_exec($handle);
  if ($response === false) {
    $errno = curl_errno($handle);
    $error = curl_error($handle);
    
    curl_close($handle);
    return false;
  }
  $http_code = intval(curl_getinfo($handle, CURLINFO_HTTP_CODE));
  curl_close($handle);
  if ($http_code >= 500) {
  
    sleep(10);
    return false;
  } else if ($http_code != 200) {
    $response = json_decode($response, true);
    error_log("Request has failed with error {$response['error_code']}: {$response['description']}\n");
    if ($http_code == 401) {
      throw new Exception('Invalid access token provided');
    }
    return false;
  } else {
    $response = json_decode($response, true);
    if (isset($response['description'])) {
      error_log("Request was successfull: {$response['description']}\n");
    }
    $response = $response['result'];
  }
  return $response;
}
function apiRequest($method, $parameters=null) {
  if (!is_string($method)) {
    error_log("Method name must be a string\n");
    return false;
  }
  if (!$parameters) {
    $parameters = array();
  } else if (!is_array($parameters)) {
    error_log("Parameters must be an array\n");
    return false;
  }
  foreach ($parameters as $key => &$val) {
   
    if (!is_numeric($val) && !is_string($val)) {
      $val = json_encode($val);
    }
  }
  $url = API_URL.$method.'?'.http_build_query($parameters);
  $handle = curl_init($url);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($handle, CURLOPT_TIMEOUT, 60);
  return exec_curl_request($handle);
}
function apiRequestJson($method, $parameters) {
  if (!is_string($method)) {
    error_log("Method name must be a string\n");
    return false;
  }
  if (!$parameters) {
    $parameters = array();
  } else if (!is_array($parameters)) {
    error_log("Parameters must be an array\n");
    return false;
  }
  $parameters["method"] = $method;
  $handle = curl_init(API_URL);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($handle, CURLOPT_TIMEOUT, 60);
  curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($parameters));
  curl_setopt($handle, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
  return exec_curl_request($handle);
}
function getUpdates($last_id = null){
  $params = [];
  if (!empty($last_id)){
    $params = ['offset' => $last_id+1, 'limit' => 5];
  }
  
  return apiRequest('getUpdates', $params);
}
function printUpdates($result){
foreach($result as $obj){
$botToken ="GANTI SINI"; // GANTI SINI
$website = "https://api.telegram.org/bot".$botToken;
$update = file_get_contents($website."/getupdates");
$json = json_decode($update, TRUE);
$finaltext = end($json['result']);
$coba1 = $finaltext['message']['text'];
$finalid = end($json['result']);
$coba2 = $finalid['message']['chat']['id'];
$nama1 = $obj['message']['from']['first_name'];
date_default_timezone_set('Asia/Kuala_Lumpur');
$timestamp = $obj['message']['date'];
$waktu = date('d M Y H:i:s', $timestamp);
	if ($coba1 == '/start')
	{
		$direply1 = urlencode("Hi"); // reply kepada client kalau dia send /start
		file_get_contents($website."/sendmessage?chat_id=".$coba2."&text=".$direply1);
	} 
	else { // kalau client send lain
	
	$text = urlencode("Ini lain"); // reply kepada client kalau dia send lain daripada yang lain
	file_get_contents($website."/sendmessage?chat_id=".$coba2."&text=".$text);
	}
	
	$beri =  $nama1.'('.$coba2.'): '.$coba1.' @ '.$waktu.PHP_EOL;
	echo $beri;
    $last_id = $obj['update_id'];
  }
  return $last_id;
}

$last_id = null;
while (true){
  $result = getUpdates($last_id);
  if (!empty($result)) {
    $last_id = printUpdates($result);
  }
  
  sleep(0);
}
?>
