<?php

include('ResponseMessenger.class.php');

class CurrencyXML{

    //Properties of the class CurrencyXML
    private $urlPath;
    private $xmlFile;
    private $request;
    private $errors;
    private $em;
    private $rm;
    

    //Constructor(s)
    public function __construct ($XMLPath, $httpReq){
        $this->errors = require('Errors.php');
        $this->em = new ErrorMessenger();
        $this->rm = new ResponseMessenger();


    	$this->request = $httpReq;

		if(!file_exists ($XMLPath)){
		    $this->em->ShowClientAppError($this->request, 3100, $this->errors['3100']);
		    return;
		}
		$this->urlPath = $XMLPath;
		$this->xmlFile = simplexml_load_file($this->urlPath);
	}


	//Public methods
    public function GetCurrencyRate($code){
        //define XPATHs to locate the currency in the XML document
        $XPATH = '//currency[@code="' . $code . '"]';

        $obj = $this->xmlFile->xpath($XPATH);
        //if the from currency is not found, give error and return
        if(empty($obj) ){
            $this->em->SetErrorMsgFormat(1000, $this->errors['1000']);    
            return;
        }
        //else get the currency rate
        $rate = $obj[0]->attributes()->rate;

        return $rate;
    }

	public function PostCurrencyRate($code, $rate){
        if($code == "GBP"){
            $this->em->ShowClientAppError($this->request, 2700, $this->errors['2700']);
            return;
        }
        $XPATH = '//currency[@code="' . $code . '"]';
        $obj = $this->xmlFile->xpath($XPATH);
        if(!isset($obj[0])){
            $this->em->ShowClientAppError($this->request, 2400, $this->errors['2400']);
            return;
        }
        //Save new XML file
        $this->SaveXMLFile();
        $this->rm->DisplayPostResponse($obj[0], $rate);
	}

	public function PutCurrencyRate($code, $name, $rate, $countries){
	    $XPATH = '//currency[@code="' . $code . '"]';
        $obj = $this->xmlFile->xpath($XPATH);
        //Check if currency already exists
        if(isset($obj[0])){
            $this->em->ShowClientAppError($this->request, 3500, $this->errors['3500']);
            return;
        }
        $XPATH = '/rates';
        $obj = $this->xmlFile->xpath($XPATH);
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
        $this->SaveXMLFile();
        $this->rm->DisplayPutResponse($rate, $code, $name, $countries);
	}

	public function DeleteCurrencyRate($code){
		//to prevent deleting the base currency, check to avoid updating base currency
    	if($code == "GBP"){
            $this->em->ShowClientAppError($this->request, 3400, $this->errors['3400']);
            return;
        }
        $XPATH = '//currency[@code="' . $code . '"]';
        $obj = $this->xmlFile->xpath($XPATH);
        if(!isset($obj[0])){
            $this->em->ShowClientAppError($this->request, 3300, $this->errors['3300']);
            return;
        }
        //remove the object reference from the array
        foreach($obj as $key){
            unset($key[0]); 
        }

        $this->SaveXMLFile();
        $this->rm->DisplayDeleteResponse($code);
	}

    public function GetCurrencyName($code){
        //define XPATHs to locate the currency in the XML document
        $XPATH = '//currency[@code="' . $code . '"]';

        $obj = $this->xmlFile->xpath($XPATH);
        //if the from currency is not found, give error and return
        if(empty($obj) ){
            $this->em->SetErrorMsgFormat(1000, $this->errors['1000']);    
            return;
        }
        //else get the currency rate
        $name = $obj[0]->name;

        return $name;
    }

    public function GetCurrencyLocs($code){
        //define XPATHs to locate the currency in the XML document
        $XPATH = '//currency[@code="' . $code . '"]';

        $obj = $this->xmlFile->xpath($XPATH);
        //if the from currency is not found, give error and return
        if(empty($obj) ){
            $this->em->SetErrorMsgFormat(1000, $this->errors['1000']);    
            return;
        }
        //else get the currency rate
        $locs = $obj[0]->loc;

        return $locs;
    }

    public function UpdateCurrencyRates($FIXER_API_URL){
        //if the Fixer API does not return data, give error and return
        if(! @ file_get_contents($FIXER_API_URL)){
            $this->em->ShowXMLError(3000, $errors['3000']);
        return;
        }
        //else, store the API data into variables 
        $fixerJSON = file_get_contents($FIXER_API_URL);
        $obj = json_decode($fixerJSON);
        //base currency
        $base = $obj->base;
        //timestamp
        $date = $obj->date;
        //rates array
        $rate = $obj->rates;

        foreach ($rate as $key => $value) {
            //define the XPATH to locate them in the XML document
            $xpath = '//currency[@code="' . $key . '"]';
            //check to avoid updating GBP and other non-included currencies
            if($key != "GBP" && $obj = $this->xmlFile->xpath($xpath)){
                //update the corresponding attribute value with the new value off the Fixer API
                $obj[0]->attributes()->rate = $value;
            }
        }
        //next, update the timestamp in the XML timestamp node
        $xpath = '/rates';
        $obj = $this->xmlFile->xpath($xpath);
        $currentTime = date('d M o H:i');
        $obj[0]->timestamp = $currentTime;
        
        //Save new XML data to file
        $this->SaveXMLFile();
    }

    public function GetTimeStamp(){
            $xpath = '//timestamp';
            $obj = $this->xmlFile->xpath($xpath);
            return $obj[0];
    }

    function CreateLocsString($locs){
        $str = "";
        $i = 0;
        //get the count for the 'from' currency loc nodes
        $len = count($locs);
        foreach ($locs as $loc) {
        //for each one, if it isn't the last, add a comma at the end
        if ($i != $len - 1) {
            $str .= $loc . ", ";
        }
        //else no comma
        else{
            $str .= $loc;       
        }
        $i++;
        }
        return $str;
    }

    function SaveXMLFile(){
        //Save new XML file
        $this->xmlFile->asXml($this->urlPath);
    }
}
?>