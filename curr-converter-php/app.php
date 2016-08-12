<!DOCTYPE html>
<html lang="en">
<head>
    <script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/stylesheet.css"/>
</head>
<body>
<form id="updateForm" name="addstoreForm" action="update.php" method="post">
    <div id="top">
	<h3>Form interface for POST, PUT & DELETE</h3>
	<div>
	    <label id="header">Action</label>
	    <div class="radio-buttons">
		<input type="radio" id="post" name="request" value="post">
		<label for="post">Post</label>
	    </div>
	    <div class="radio-buttons">
		<input type="radio" id="put" name="request" value="put">
		<label for="put">Put</label>
	    </div>
	    <div class="radio-buttons">
		<input type="radio" id="delete" name="request" value="delete">
		<label for="delete">Delete</label>
	    </div>
	</div>
	<div>
	    <label id="header">Currency Code <span id="hint">(in uppercase e.g ABC)</span></label>
	    <input type="text" name="code" id="code" placeholder="code">
	</div>
	<div>
	    <label id="header">Currency Name</label>
	    <input type="text" name="name" id="name" placeholder="name">
	</div>
	<div>
	    <label id="header">Rate <span id="hint">(decimal values, £=1)</span></label>
	    <input type="text" name="rate" id="rate" placeholder="rate">
	</div>
	<div>
	    <label id="header">Countries <span id="hint">(comma seperated if 1+, country codes can also be included e.g france-FR)</span></label>
	    <input type="text" name="countries" id="countries" placeholder="countries" style="width: 90%;">
	</div>
	<div>
	<button id="submitBtn" class="header-button name=" type="button">Submit</button>
	</div>
    </div>
	<div>
	    <label id="header">Response Message</label>
	    <textarea id="response"></textarea>
	    <div id="test"></div>
	</div>
<a href="xml/rates.xml" target="_blank">View XML file</a>
</form>
</body>
<script src="js/app.js"></script>
</html>