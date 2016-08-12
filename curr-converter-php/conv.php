<?php
/** conv.php
*This script processes the GET request of the API
*
* Created by Chinedu Umebolu on 25/02/2016.
*/

//set error reporting level to all errors
error_reporting(E_ALL);

//Reuire config.php
require 'Config.php';

$errors = include ERRORS_FILE_PATH;

//include the currencyXML and ErrorMessenger classes
include CLASS_ERROR_MESSENGER_PATH;
include CLASS_GET_CURR_PATH;
include CLASS_POST_CURR_PATH;

//Create an instance of the ErrorMesssenger object
$em = new ErrorMessenger();

//get the current timestamp
$currentTime = date('d M o H:i');

//Create an instance of the GetCurrencyXML object
$getCurrXML = new GetCurrencyXML(XML_FILE_PATH, METHOD_GET);
$lastUpdated =  $getCurrXML->GetTimeStamp();

/***THIS IS WHERE THE XML DATA IS UPDATED EVERY 12 HOURS***/
//Check if there's a time difference of more than 12 hours from when last updated to the current time
//if so, update the currency rates***/
if(strtotime($currentTime) - strtotime($lastUpdated) > UPDATE_INTERVAL){
    $postCurrXML = new PostCurrencyXML(XML_FILE_PATH, METHOD_GET);
    //if the Fixer API does not return data, give error and return
	$content = @ file_get_contents(FIXER_API_URL);
    if($content === FALSE){
            $em->ShowXMLError(3000, $errors['3000']);
        return;
    }
    //else, update currency rates 
    $postCurrXML->UpdateCurrencyRates(FIXER_API_URL);
    //next, reload the XML data
    $xml = simplexml_load_file(XML_FILE_PATH);
}

//Next check available $_GET parameters, if any does not equal to the required, give error and return
foreach($_GET as $key => $value){
    if($key != PARAM_AMOUNT && $key != PARAM_FROM && $key != PARAM_TO && $key != PARAM_FORMAT){
	   $em->ShowFormatErrorMsg(1200, $errors['1200']);
	   return;
    }
    //if format is empty, give an error and return
    if($key == PARAM_FORMAT && $value == EMPTY_STR){
	   $em->ShowXMLError(1400, $errors['1400']);	
	   return;
    }
    //If format parameter is not XML or JSON, give an error and return
	if($key == PARAM_FORMAT && $value != FORMAT_JSON && $value != FORMAT_XML){
	   $em->ShowXMLError(1400, $errors['1400']);	
	   return;
    }
	//if format is empty, give an error and return
    if($key == PARAM_AMOUNT && preg_match("/[a-z]/i", $value)){
	   $em->ShowFormatErrorMsg(1400, $errors['1400']);	
	   return;
    }
}

//if any of amnt, from, to and format parameters are missing...
if(!isset($_GET[PARAM_AMOUNT]) || !isset($_GET[PARAM_FROM]) ||
   !isset($_GET[PARAM_TO]) || !isset($_GET[PARAM_FORMAT])){
    //...if format is set and has json as value, give missing param error in json and return 
    if(isset($_GET[PARAM_FORMAT]) && $_GET[PARAM_FORMAT] == FORMAT_JSON){
    	    $em->ShowJSONError(1100, $errors['1100']);	
    	    return;
    }
	//else give missing param error in xml and return 
	else{
		$em->ShowXMLError(1100, $errors['1100']);	
		return;
	}                                                      
}
	
//if amnt is a number but not a decimal, give error and return
if (is_numeric($_GET[PARAM_AMOUNT]) && strpos( $_GET[PARAM_AMOUNT], '.') === false){
	$em->ShowFormatErrorMsg(1300, $errors['1300']);    
	return;
}
//get the 'from' currency rate from the xml
$fromRate = $getCurrXML->GetCurrencyRate($_GET[PARAM_FROM]);
if($fromRate == Null){
	return;
}
//get the 'from' currency name from the xml
$fromCurrName = $getCurrXML->GetCurrencyName($_GET[PARAM_FROM]);
if($fromCurrName == Null){
	return;
}
//get the 'from' currency location xml tags
$fromLocs = $getCurrXML->GetCurrencyLocs($_GET[PARAM_FROM]);
if($fromLocs == Null){
	return;
}
//get the 'to' currency rate from the xml
$toRate = $getCurrXML->GetCurrencyRate($_GET[PARAM_TO]);
//If $toRate returns no value, stop for the GetCurrencyRate method's error
if($toRate == Null){
    return;
}
//get the 'to' currency name the xml
$toCurrName = $getCurrXML->GetCurrencyName($_GET[PARAM_TO]);
//get the loc nodes of the 'to' currency 
$toLocs = $getCurrXML->GetCurrencyLocs($_GET[PARAM_TO]);

$amount = $_GET[PARAM_AMOUNT];

/***CONVERSION FORMULA***/
//To get the new amount, divide PARAM_TO currency rate by PARAM_FROM rate then multiply by amount
$conv = (floatval($toRate) * floatval($amount)) / floatval($fromRate);
/************************/

//initialise ResponseMessenger class
$rm = new ResponseMessenger();
//convert the $fromLoc an $toLocs tags to strings
$fromCurrLocs = $getCurrXML->CreateLocsString($fromLocs);
$toCurrLocs = $getCurrXML->CreateLocsString($toLocs);
//put the extracted from and to data into arrays
$fromArray = array($_GET[PARAM_FROM], $fromCurrName, $fromCurrLocs, $amount); 
$toArray = array($_GET[PARAM_TO], $toCurrName, $toCurrLocs, $conv);

//FINALLY pass the arrays to the response messener method,displaying according selected format
//XML
if($_GET[PARAM_FORMAT] == FORMAT_XML){
    echo $rm->ConvXMLResponse($fromArray, $toArray, $toRate);
}
//JSON
else if($_GET[PARAM_FORMAT] == FORMAT_JSON){
    echo $rm->ConvJSONResponse($fromArray, $toArray, $toRate);
}
?>