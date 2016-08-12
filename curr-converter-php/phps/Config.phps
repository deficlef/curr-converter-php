<?php
/** Config.php
*This file contains all the constants and settings used in the API.
*
* Created by Chinedu Umebolu on 25/02/2016.
*/

	//API general information
    const APP_VERSION = '1.0';
    const DEFAULT_LANGUAGE = 'en';
    const BASE_CURRENCY = "GBP";

    //API settings
    //Update duration of 12 hours in seconds
    const UPDATE_INTERVAL = '43200';

    //External Fixer.io API URL
    const FIXER_API_URL = 'http://api.fixer.io/latest?base=GBP';

    //File paths
   	const CONFIG_FILE_PATH = 'Config.php';
    const XML_FILE_PATH = 'xml/rates.xml';
    const XSD_FILE_PATH = 'xsd/rates.xsd';    
    const ERRORS_FILE_PATH = 'Errors.php';

    //API Classes paths
    const CLASS_RESPONSE_MESSENGER_PATH = 'ResponseMessenger.class.php';
    const CLASS_ERROR_MESSENGER_PATH = 'ErrorMessenger.class.php';
	const CLASS_GET_CURR_PATH = 'GetCurrencyXML.class.php';
	const CLASS_POST_CURR_PATH = 'PostCurrencyXML.class.php';
	const CLASS_PUT_CURR_PATH = 'PutCurrencyXML.class.php';
	const CLASS_DELETE_CURR_PATH = 'DeleteCurrencyXML.class.php';

    //API Interface(s)
    const INTERFACE_XMLFILE_PATH = 'IXMLFile.php';

    //Rates.xml attributes 
    const ATTR_CODE = 'code';
    const ATTR_RATE = 'rate';

	//Rates.xml tags 
    const PARENT_TAG_RATE = 'rates'; 
    const TAG_RATE = 'rate';
    const TAG_NAME = 'name';
    const TAG_TIMESTAMP = 'timestamp';
    const TAG_CURRENCY = 'currency';
    const TAG_LOC = 'loc';

	//Seperators for Client API loc values
    const LOCS_SEPERATOR = ",";
    const LOC_CODE_SEPERATOR = "-";

    //API GET Parameters
    const PARAM_FROM = 'from';
    const PARAM_TO = 'to';
    const PARAM_AMOUNT = 'amnt';
    const PARAM_FORMAT = 'format';

    //API Response formats
    const FORMAT_XML = "xml";
    const FORMAT_JSON = "json";

	//Client API POST parameters
	const POST_REQUEST = 'request';
	const POST_NAME = 'name';
	const POST_CODE = 'code';
	const POST_RATE = 'rate';
	const POST_COUNTRIES = 'countries';

    //Client API Action methods
    const METHOD_GET = "get";
    const METHOD_POST = "post";
    const METHOD_DELETE = "delete";
    const METHOD_PUT = "put";

    //Miscellaneous
    const EMPTY_STR = "";

