<?php
// function to clean up the XML data and replace non-XML characters with their equivalents ( sourced from https://developers.google.com/maps/articles/phpsqlajax_v3) 
function parseToXML($htmlStr) { 
	$xmlStr=str_replace('<','&lt;',$htmlStr); 
	$xmlStr=str_replace('>','&gt;',$xmlStr); 
	$xmlStr=str_replace('"','&quot;',$xmlStr); 
	$xmlStr=str_replace("'",'&#39;',$xmlStr); 
	$xmlStr=str_replace("&",'&amp;',$xmlStr); 
	return $xmlStr; 
} 

// Start XML file
 $xml = '<rates>';
$xml .= '<timestamp>place holder</timestamp>';
// Iterate through the rows, printing XML nodes for each
for ($i=0; $i < 22; $i++){		
	// ADD TO XML DOCUMENT NODE
	$xml .= '<currency ';
		$xml .= 'iso ="4217" ';
		$xml .= 'code ="" ';
		$xml .= 'rate ="" ';
	$xml .= '>';
	$xml .= '<name>name</name>';
	$xml .= '<loc code = "place holder">';
	$xml .= '</loc>';	
	$xml .= '</currency>';
}
// End XML file
$xml .= '</rates>';

$filename = "xml/rates.xml";
# save the file as a DOM document
if(!file_exists ($filename)){
	$doc = new DOMDocument();
	$doc->loadXML($xml);
	$doc->save($filename);
	$response = "XML file has been saved";
}
else{
	$response = "XML file has already been generated";
}
# print out xml
//header("Content-type: text/xml");
//echo $xml;
?>


<!doctype html>
<html>
	<head></head>
	<body>
		<p><?php echo $response; ?></p>
		<a href="xml/rates.xml">View XML file</a>
	</body>
</html>