<?php
/** PostCurrencyXML.class.php
*This class handles Post responsibilities of the currency XML document
*
* Created by Chinedu Umebolu on 25/02/2016.
*/

//Include ResponseMessenger class and XMLFile interface
include_once CLASS_RESPONSE_MESSENGER_PATH;
include_once INTERFACE_XMLFILE_PATH;

class PostCurrencyXML implements XMLFile {

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
	public function PostCurrencyRate($code, $rate){
        if($code == BASE_CURRENCY){
            $this->em->ShowClientAppError($this->request, 2700, $this->errors['2700']);
            return;
        }
        $XPATH = '//' . TAG_CURRENCY . '[@' . ATTR_CODE . '="' . $code . '"]';
        $obj = $this->xmlFile->xpath($XPATH);
        if(!isset($obj[0])){
            $this->em->ShowClientAppError($this->request, 2400, $this->errors['2400']);
            return;
        }
        //Save new XML file
        $this->rm->DisplayPostResponse($obj[0], $rate);
        $this->SaveXMLFile();
	}

    public function UpdateCurrencyRates($FIXER_API_URL){
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
            $XPATH = '//' . TAG_CURRENCY . '[@' . ATTR_CODE . '="' . $key . '"]';
            //check to avoid updating GBP and other non-included currencies
            if($key != BASE_CURRENCY && $obj = $this->xmlFile->xpath($XPATH)){
                //update the corresponding attribute value with the new value off the Fixer API
                $obj[0]->attributes()->rate = $value;
            }
        }
        //next, update the timestamp in the XML timestamp node
        $XPATH = '/' . PARENT_TAG_RATE;
        $obj = $this->xmlFile->xpath($XPATH);
        $currentTime = date('d M o H:i');
        $obj[0]->timestamp = $currentTime;
        
        //Save new XML data to file
        $this->SaveXMLFile();
    }

    function SaveXMLFile(){
        //Save new XML file
        $this->xmlFile->asXml($this->urlPath);
    }
}
?>