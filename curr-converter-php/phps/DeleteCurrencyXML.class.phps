<?php
/** DeleteCurrencyXML.class.php
*This class handles delete responsibilities of the currency XML document and implements XMLFile interface
*
* Created by Chinedu Umebolu on 25/02/2016.
*/

//Include ResponseMessenger class and XMLFile interface
include_once CLASS_RESPONSE_MESSENGER_PATH;
include_once INTERFACE_XMLFILE_PATH;

class DeleteCurrencyXML implements XMLFile {

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
	public function DeleteCurrencyRate($code){
		//to prevent deleting the base currency, check to avoid updating base currency
    	if($code == BASE_CURRENCY){
            $this->em->ShowClientAppError($this->request, 3400, $this->errors['3400']);
            return;
        }
        $XPATH = '//' . TAG_CURRENCY . '[@' . ATTR_CODE . '="' . $code . '"]';
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

    function SaveXMLFile(){
        //Save to XML file
        $this->xmlFile->asXml($this->urlPath);
    }
}
?>