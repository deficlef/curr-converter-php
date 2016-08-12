<?php
error_reporting(E_ALL);

$configs = include('config.php');
$errors = include('errors.php');

//var_dump($_POST);
if(!isset($_POST['request'])){
    XMLError("Null", 2000, $errors['2000']);
    return;
}

$request = $_POST['request'];

if(isset($_POST['code'])){
    if($_POST['code'] == null || !ctype_alpha($_POST['code']) || !ctype_upper($_POST['code'])){
        XMLError($request, 2200, $errors['2200']);
        return;
    }
}
$code = $_POST['code'];

if(isset($_POST['rate'])){
    if($_POST['rate'] == null || !is_numeric($_POST['rate']) || strpos( $_POST['rate'], '.') === false){
        XMLError($request, 2100, $errors['2100']);
        return;
    }
    $rate = $_POST['rate'];
}

if(isset($_POST['countries'])){
    if($_POST['countries'] == null){
        XMLError($request, 2300, $errors['2300']);
        return;
    }
    $countries = $_POST['countries'];
}

if(isset($_POST['name'])){
    $name = $_POST['name'];
}

$XML_FILE = $configs['XML_FILE_URL'];


if(!file_exists ($XML_FILE)){
    XMLError($request, 3100, $errors['3100']);
    return;
}
$xmlFile = simplexml_load_file($XML_FILE);

switch($request){
    case "post":
	//check to avoid updating base currency
	if($code == "GBP"){
            XMLError($request, 2700, $errors['2700']);
            return;
        }
        $xpath = '//currency[@code="' . $code . '"]';
        $obj = $xmlFile->xpath($xpath);
        //print_r($obj[0]);
        if(!isset($obj[0])){
            XMLError($request, 2400, $errors['2400']);
            return;
        }
        $xml = "<?xml version='1.0' encoding='UTF-8'?>";
        $xml .= '<method ';
            $xml .= 'type ="'. $request .'" ';
        $xml .= '>';
            $xml .= '<at>'. date('d M o H:i') . '</at>';
            $xml .= '<previous>';
                $xml .= '<rate>'. $obj[0]->attributes()->rate . '</rate>';
                $xml .= '<curr>';
                    $xml .= '<code>'. $obj[0]->attributes()->code . '</code>';
                    $xml .= '<name>'. $obj[0]->name . '</name>';
                    $locs = $obj[0]->loc;
                    $str = "";
                    $i = 0;
                    $len = count($locs);
                    foreach ($locs as $loc) {
                        if ($i != $len - 1) {
                            $str .= $loc . ", ";
                        }
                        else{
                            $str .= $loc;	    
                        }
                        $i++;
                    }
                    $xml .='<loc>' . $str . '</loc>';
                $xml .= '</curr>';
            $xml .= '</previous>';
            $xml .= '<new>';
            $obj[0]->attributes()->rate = $rate;
                $xml .= '<rate>'. $obj[0]->attributes()->rate . '</rate>';
                $xml .= '<curr>';
                    $xml .= '<code>'. $obj[0]->attributes()->code . '</code>';
                    $xml .= '<name>'. $obj[0]->name . '</name>';
                    $xml .='<loc>' . $str . '</loc>';
                $xml .= '</curr>';
            $xml .= '</new>';
        $xml .= '</method>';
        echo $xml;
        //Save new XML file
        $xmlFile->asXml($XML_FILE);
        break;
    
    case "put":
        $xpath = '//currency[@code="' . $code . '"]';
        $obj = $xmlFile->xpath($xpath);
        //Check if currency already exists
        if(isset($obj[0])){
            XMLError($request, 2500, $errors['2500']);
            return;
        }
        $xpath = '/rates';
        $obj = $xmlFile->xpath($xpath);
        $currency = $obj[0]->addChild('currency');
        $currency->addAttribute('iso', "4217");
        $currency->addAttribute('code', $code);
        $currency->addAttribute('rate', $rate);
        $currency->addChild('name', $name);
        //check if there are country values
        if($countries != null){
            //seperate the values and store into an array
            $locs = explode(",", $countries);
            foreach ($locs as $loc){
                //for each value, seperate the names from their codes and store in array 
                $values = explode("-", $loc);
                //assign country name to var
                $locName = $values[0];
                //add loc node to XML, with country name
                $l = $currency->addChild('loc', $locName);
                //check to see if country code is set
                if(isset($values[1])){
                    //if so, assign code to var
                    $locCode = $values[1];
                    //add code attribute containint the country code to the loc XML node
                    $l->addAttribute('code', $locCode);
                }
                else{
                    //if not, add empty code attribute to loc node                    
                    $l->addAttribute('code', "");
                }
                //print_r($values);
            }
        }
        $xmlFile->asXml($XML_FILE);
        //display PUT XML response
        $xml = "<?xml version='1.0' encoding='UTF-8'?>";
        $xml .= '<method ';
            $xml .= 'type ="'. $request .'" ';
        $xml .= '>';
            $xml .= '<at>'. date('d M o H:i') . '</at>';
            $xml .= '<rate>'. $rate . '</rate>';
            $xml .= '<curr>';
                $xml .= '<code>'. $code . '</code>';
                $xml .= '<name>'. $name . '</name>';
                $xml .='<loc>' . $countries . '</loc>';
            $xml .= '</curr>';
        $xml .= '</method>';

        echo $xml;
        break;
    
    case "delete":
        //to prevent deleting the base currency
    	//check to avoid updating base currency
    	if($code == "GBP"){
            XMLError($request, 2700, $errors['2700']);
            return;
        }
        $xpath = '//currency[@code="' . $code . '"]';
        $obj = $xmlFile->xpath($xpath);
        if(!isset($obj[0])){
            XMLError($request, 2600, $errors['2600']);
            return;
        }
        //remove the object reference from the array
        foreach($obj as $key){
            unset($key[0]); 
        }

        //echo $xmlFile->asXml();
        $xmlFile->asXml($XML_FILE);
        //display XML response
        $xml = "<?xml version='1.0' encoding='UTF-8'?>";
        $xml .= '<method ';
            $xml .= 'type ="'. $request .'" ';
        $xml .= '>';
            $xml .= '<at>'. date('d M o H:i') . '</at>';
            $xml .= '<code>'. $code . '</code>';
        $xml .= '</method>';
            
        echo $xml;
        break;
    default:
        XMLError($request, 2000, $errors['2000']);
        break;
    }
    
//Methods//
function XMLError($request, $code, $msg){
    //header("Content-type: text/xml");
    // Start XML file
    $xml = "<?xml version='1.0' encoding='UTF-8'?>";
    $xml .= '<method ';
    $xml .= 'type ="'. $request .'" ';
    $xml .= '>';
    $xml .= '<error>';
    $xml .= '<code>' . $code . '</code>';
    $xml .= '<msg>' . $msg . '</msg>';
    $xml .= '</error>';
    // End XML file
    $xml .= '</method>';
    echo $xml;
}

?>