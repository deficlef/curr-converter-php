<?php
/** ErrorMessenger.class.php
*This class displays the API's error messages
*
* Created by Chinedu Umebolu on 25/02/2016.
*/

class ErrorMessenger {
    
    public $xmlHeadTag;

    public function __construct (){
        $this->xmlHeadTag = "<?xml version='1.0' encoding='UTF-8'?>";
    }

    //Methods//
    function ShowClientAppError($request, $code, $msg){
        //header("Content-type: text/xml");
        // Start XML file
        $xml = $this->xmlHeadTag;
        $xml .= '<method ';
        $xml .= 'type ="'. $request .'"';
        $xml .= '>';
        $xml .= '<error>';
        $xml .= '<code>' . $code . '</code>';
        $xml .= '<msg>' . $msg . '</msg>';
        $xml .= '</error>';
        // End XML file
        $xml .= '</method>';
        echo $xml;
    }

    function ShowXMLError($code, $msg){
        header("Content-type: text/xml");
        // Start XML file
        $xml = $this->xmlHeadTag;
        $xml .= '<conv>';
        $xml .= '<error>';
        $xml .= '<code>' . $code . '</code>';
        $xml .= '<msg>' . $msg . '</msg>';
        $xml .= '</error>';
        // End XML file
        $xml .= '</conv>';
        echo $xml;
    }

    function ShowJSONError($code, $msg){
        $response = array();
        $response["conv"] = array();
        $response["conv"]['error'] = array();
        
        $response["conv"]['error']["code"] = $code;
        $response["conv"]['error']["msg"] = $msg;	
        // echoing JSON response
        header ("Content-Type: application/json");
        echo json_encode($response);
    }

    function ShowFormatErrorMsg($code, $msg){
        if(isset($_GET[PARAM_FORMAT])){
        	if($_GET[PARAM_FORMAT] == FORMAT_JSON){
        	    $this->ShowJSONError($code, $msg);
        	}
        	if($_GET[PARAM_FORMAT] == FORMAT_XML){
        	    $this->ShowXMLError($code, $msg);
        	}
            return;
        }
    }

    //End of Methods
}
?>