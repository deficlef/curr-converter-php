<?php
/*Functions copied off http://php.net/manual/en/domdocument.schemavalidate.php. 
Used to display XML errors in the validation process*/

//Reuire config.php
require 'Config.php';

function libxml_display_error($error)
{
    $return = "<br/>\n";
    switch ($error->level) {
        case LIBXML_ERR_WARNING:
            $return .= "<b>Warning $error->code</b>: ";
            break;
        case LIBXML_ERR_ERROR:
            $return .= "<b>Error $error->code</b>: ";
            break;
        case LIBXML_ERR_FATAL:
            $return .= "<b>Fatal Error $error->code</b>: ";
            break;
    }
    $return .= trim($error->message);
    if ($error->file) {
        $return .=    " in <b>$error->file</b>";
    }
    $return .= " on line <b>$error->line</b>\n";

    return $return;
}

function libxml_display_errors() {
    $errors = libxml_get_errors();
    foreach ($errors as $error) {
        print libxml_display_error($error);
    }
    libxml_clear_errors();
}
/* End of functions*/

// Enable user error handling
libxml_use_internal_errors(true);

//variables to define the XML document and XSD schema locations
//surveys.xml is saved to the server when genxml.php is run
$xmlDoc = XML_FILE_PATH;
$xmlSchema = XSD_FILE_PATH;

$xml = new DOMDocument();
$xmlraw = $xml->load($xmlDoc);

$xml->load($xmlraw); 

// If XMl doesn't validate to schema, display the error messages, otherwise print the validation message.
if (!$xml->schemaValidate($xmlSchema)) {
    print '<b>DOMDocument::schemaValidate() Generated Errors!</b>';
    libxml_display_errors();
}
else{
	echo $xmlDoc . ' validates with ' . $xmlSchema . ' and contains no errors.';
}
?>