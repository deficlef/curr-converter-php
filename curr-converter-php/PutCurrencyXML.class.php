<?php
/** GetCurrencyXML.class.php
*This class handles Put responsibilities of the currency XML document
*
* Created by Chinedu Umebolu on 25/02/2016.
*/

//Include ResponseMessenger class and XMLFile interface
include_once CLASS_RESPONSE_MESSENGER_PATH;
include_once INTERFACE_XMLFILE_PATH;

class PutCurrencyXML implements XMLFile {

    //Properties of the class CurrencyXML
    private $urlPath;
    private $xmlFile;
    private $request;
    private $errors;
    private $em;
    private $rm;

    //Constructor(s)
    public function __construct ($XMLPath, $httpReq){
        //require Errors.php
        $this->errors = require(ERRORS_FILE_PATH);
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
	public function PutCurrencyRate($code, $name, $rate, $countries){
	    $XPATH = '//' . TAG_CURRENCY . '[@' . ATTR_CODE . '="' . $code . '"]';
        $obj = $this->xmlFile->xpath($XPATH);
        //Check if currency already exists
        if(isset($obj[0])){
            $this->em->ShowClientAppError($this->request, 3500, $this->errors['3500']);
            return;
        }
        $XPATH = '/' . PARENT_TAG_RATE;
        $obj = $this->xmlFile->xpath($XPATH);
        $currency = $obj[0]->addChild(TAG_CURRENCY);
        //$currency->addAttribute('iso', "4217");
        $currency->addAttribute(ATTR_CODE, $code);
        $currency->addAttribute(TAG_RATE, $rate);
        $currency->addChild(TAG_NAME, $name);
        //check if there are country values
        if($countries != null){
            //seperate the values and store into an array
            $locs = explode(LOCS_SEPERATOR, $countries);
            foreach ($locs as $loc){
                //for each value, seperate the names from their codes and store in array 
                $values = explode(LOC_CODE_SEPERATOR, $loc);
                //assign country name to var
                $locName = $values[0];
                //add loc node to XML, with country name
                $l = $currency->addChild(TAG_LOC, $locName);
                //check to see if country code is set
                if(isset($values[1])){
                    //if so, assign code to var
                    $locCode = $values[1];
                    //add code attribute containint the country code to the loc XML node
                    $l->addAttribute(ATTR_CODE, $locCode);
                }
                else{
                    //if not, add empty code attribute to loc node                    
                    $l->addAttribute(ATTR_CODE, "");
                }
                //print_r($values);
            }
        }
        $this->SaveXMLFile();
        $this->rm->DisplayPutResponse($rate, $code, $name, $countries);
	}

    function SaveXMLFile(){
        //Save new XML file
        $this->xmlFile->asXml($this->urlPath);
    }
}
?>