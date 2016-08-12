<?php
/** GetCurrencyXML.class.php
*This class handles Get responsibilities of the currency XML documentand and implements XMLFile interface
*
*
* Created by Chinedu Umebolu on 25/02/2016.
*/

//Include ResponseMessenger class and XMLFile interface
include_once CLASS_RESPONSE_MESSENGER_PATH;
include_once INTERFACE_XMLFILE_PATH;

class GetCurrencyXML implements XMLFile {

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
    public function GetCurrencyRate($code){
        //define XPATHs to locate the currency in the XML document
        $XPATH = '//' . TAG_CURRENCY . '[@' . ATTR_CODE . '="' . $code . '"]';

        $obj = $this->xmlFile->xpath($XPATH);
        //if the from currency is not found, give error and return
        if(empty($obj) ){
            $this->em->ShowFormatErrorMsg(1000, $this->errors['1000']);
			$error = true;			
            return;
        }
        //else get the currency rate
        $rate = $obj[0]->attributes()->rate;

        return $rate;
    }

    public function GetCurrencyName($code){
        //define XPATHs to locate the currency in the XML document
        $XPATH = '//' . TAG_CURRENCY . '[@' . ATTR_CODE . '="' . $code . '"]';

        $obj = $this->xmlFile->xpath($XPATH);
        //if the from currency is not found, give error and return
        if(empty($obj) ){
            $this->em->ShowFormatErrorMsg(1000, $this->errors['1000']);
			$error = true;			
            return;
        }
        //else get the currency rate
        $name = $obj[0]->name;

        return $name;
    }

    public function GetCurrencyLocs($code){
        //define XPATHs to locate the currency in the XML document
        $XPATH = '//' . TAG_CURRENCY . '[@' . ATTR_CODE . '="' . $code . '"]';

        $obj = $this->xmlFile->xpath($XPATH);
        //if the from currency is not found, give error and return
        if(empty($obj) ){
            $this->em->ShowFormatErrorMsg(1000, $this->errors['1000']);    
            return;
        }
        //else get the currency rate
        $locs = $obj[0]->loc;

        return $locs;
    }

    public function GetTimeStamp(){
        $XPATH = '//' . TAG_TIMESTAMP;
        $obj = $this->xmlFile->xpath($XPATH);
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
        //Save to XML file
        $this->xmlFile->asXml($this->urlPath);
    }
}
?>