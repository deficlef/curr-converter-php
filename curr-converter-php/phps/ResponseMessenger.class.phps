<?php
/** ResponseMessenger.class.php
*This class displays the API's response messages
*
* Created by Chinedu Umebolu on 25/02/2016.
*/

class ResponseMessenger {

    //methods
    public function DisplayPostResponse($obj, $rate){
        //construct the POST XML response
        $xml = "<?xml version='1.0' encoding='UTF-8'?>";
        $xml .= '<method ';
            $xml .= 'type ="post">';
            $xml .= '<at>'. date('d M o H:i') . '</at>';
            $xml .= '<previous>';
                $xml .= '<rate>'. $obj->attributes()->rate . '</rate>';
                $xml .= '<curr>';
                    $xml .= '<code>'. $obj->attributes()->code . '</code>';
                    $xml .= '<name>'. $obj->name . '</name>';
                    $locs = $obj->loc;
                    $str = "";
                    $i = 0;
                    $len = count($locs);
                    //loop through the locations and extract text values
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
            $obj->attributes()->rate = $rate;
                $xml .= '<rate>'. $obj->attributes()->rate . '</rate>';
                $xml .= '<curr>';
                    $xml .= '<code>'. $obj->attributes()->code . '</code>';
                    $xml .= '<name>'. $obj->name . '</name>';
                    $xml .='<loc>' . $str . '</loc>';
                $xml .= '</curr>';
            $xml .= '</new>';
        $xml .= '</method>';
        echo $xml;
    }

    public function DisplayPutResponse($rate, $code, $name, $countries){
        //construct the PUT XML response
        $xml = "<?xml version='1.0' encoding='UTF-8'?>";
        $xml .= '<method type ="put">';
            $xml .= '<at>'. date('d M o H:i') . '</at>';
            $xml .= '<rate>'. $rate . '</rate>';
            $xml .= '<curr>';
                $xml .= '<code>'. $code . '</code>';
                $xml .= '<name>'. $name . '</name>';
                $xml .='<loc>' . $countries . '</loc>';
            $xml .= '</curr>';
        $xml .= '</method>';

        echo $xml;
    }

    public function DisplayDeleteResponse($code){
        //construct the Delete XML response
        $xml = "<?xml version='1.0' encoding='UTF-8'?>";
        $xml .= '<method type ="delete">';
            $xml .= '<at>'. date('d M o H:i') . '</at>';
            $xml .= '<code>'. $code . '</code>';
        $xml .= '</method>';
            
        echo $xml;
    }

    public function ConvXMLResponse(array $fromArray, array $toArray, $toRate){
        /***START OF XML RESPONSE***/
        $xml = "<?xml version='1.0' encoding='UTF-8'?>";
        $xml .= '<conv>';

        $xml .= '<at>' . date('d M o H:i') . '</at>';
        $xml .= '<rate>' . $toRate . '</rate>';

        $xml .= '<from>';
        $xml .= '<code>' . $fromArray[0] . '</code>';
        $xml .= '<curr>' . $fromArray[1] . '</curr>';
        $xml .= '<loc>';
        //return all the locations using the CreateLocsString method
        $xml .= $fromArray[2] . '</loc>';
        $xml .= '<amount>' . $fromArray[3] . '</amount>';
        $xml .= '</from>';

        $xml .= '<to>';
        $xml .= '<code>' . $toArray[0] . '</code>';
        $xml .= '<curr>' . $toArray[1] . '</curr>';
        $xml .= '<loc>';
        //return all the locations using the CreateLocsString method    
        $xml .= $toArray[2] . '</loc>';
        $xml .= '<amount>' . $toArray[3] . '</amount>';
        $xml .= '</to>';

        // End XML response
        $xml .= '</conv>';
        /******END OF XML RESPONSE******/
        header("Content-type: text/xml");
        return $xml;
    }

    public function ConvJSONResponse(array $fromArray, array $toArray, $toRate){
        /***  START OF JSON RESPONSE ***/
        $response = array();
        $response["conv"] = array();
        $response["conv"]['at'] = date('d M o H:i');
        $response["conv"]['rate'] = (string)$toRate;

        $response["conv"]['from'] = array();

        $response["conv"]['from']["code"] = $fromArray[0];
        $response["conv"]['from']["curr"] = (string)$fromArray[1];
        //return all the location values using the CreateLocsString method
        $response["conv"]['from']["loc"] = $fromArray[2];
        $response["conv"]['from']["amnt"] = $fromArray[3];

        $response["conv"]['to'] = array();

        $response["conv"]['to']["code"] = $toArray[0];
        $response["conv"]['to']["curr"] = (string)$toArray[1];
        //return all the locations using the CreateLocsString method
        $response["conv"]['to']["loc"] = $toArray[2];
        $response["conv"]['to']["amnt"] = $toArray[3];
        /****** END OF JSON RESPONSE ******/
        // echoing JSON response
        header ("Content-Type: application/json");
        return json_encode($response);
    }
}
?>