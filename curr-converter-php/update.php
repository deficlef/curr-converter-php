<?php
/** update.php
*This file processes the client API HTML user interface
*
* Created by Chinedu Umebolu on 25/02/2016.
*/

//set error reporting level to all
error_reporting(E_ALL);

//include the config file
include 'Config.php';
$errors = include ERRORS_FILE_PATH;

//include the currencyXML and ErrorMessenger classes
include CLASS_ERROR_MESSENGER_PATH;
include CLASS_GET_CURR_PATH;
include CLASS_POST_CURR_PATH;
include CLASS_PUT_CURR_PATH;
include CLASS_DELETE_CURR_PATH;

//Create an instance of the ErrorMesssenger object
$em = new ErrorMessenger();

//if POST parameter request is not set, show associated error and return
if(!isset($_POST[POST_REQUEST])){
    $em->ShowClientAppError("Null", 2000, $errors['2000']);
    return;
}

$request = $_POST[POST_REQUEST];

if(isset($_POST[POST_CODE])){
    //if POST parameter code is empty, not an alphabet or not in uppercase, show associated error and return
    if($_POST[POST_CODE] == null || !ctype_alpha($_POST[POST_CODE]) || !ctype_upper($_POST[POST_CODE])){
        $em->ShowClientAppError($request, 2200, $errors['2200']);
        return;
    }
}
$code = $_POST[POST_CODE];

if(isset($_POST[POST_RATE])){
    //if POST parameter rate is empty, not numeric or not a decimal, show associated error and return
    if($_POST[POST_RATE] == null || !is_numeric($_POST[POST_RATE]) || strpos( $_POST[POST_RATE], '.') === false){
        $em->ShowClientAppError($request, 2100, $errors['2100']);
        return;
    }
    $rate = $_POST[POST_RATE];
}

if(isset($_POST[POST_COUNTRIES])){
    //if POST parameter countries is empty, show associated error and return
    if($_POST[POST_COUNTRIES] == null){
        $em->ShowClientAppError($request, 2300, $errors['2300']);
        return;
    }
    $countries = $_POST[POST_COUNTRIES];
}

if(isset($_POST[POST_NAME])){
    $name = $_POST[POST_NAME];
}

//if XML file does not exist, show associated error and return
if(!file_exists (XML_FILE_PATH)){
    $em->ShowClientAppError($request, 2500, $errors['2500']);
    return;
}

//switch operator for handling the action methods
switch($request){
    case METHOD_POST:
            //get an instance of PostCurrencyXML object
            $postCurrXML = new PostCurrencyXML(XML_FILE_PATH, $request);
            //perform the PostCurrencyRate method
            $postCurrXML->PostCurrencyRate($code, $rate);
        break;
    
    case METHOD_PUT:
            //get an instance of PutCurrencyXML object
            $putCurrXML = new PutCurrencyXML(XML_FILE_PATH, $request);
            //perform the PutCurrencyRate method
            $putCurrXML->PutCurrencyRate($code, $name, $rate, $countries);
        break;
    
    case METHOD_DELETE:
            //get an instance of DeleteCurrencyXML object
            $delCurrXML = new DeleteCurrencyXML(XML_FILE_PATH, $request);
            //perform the DeleteCurrencyRate method
            $delCurrXML->DeleteCurrencyRate($code);
        break;

    default:
        //if none of the above, default to showing the associated error
        $em->ShowClientAppError($request, 2000, $errors['2000']);
        break;
}

?>