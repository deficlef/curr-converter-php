var submitBtn = document.getElementById("submitBtn");
var inactiveCol = "#DEDEDE";
var activeCol = "#FFFFFF";
$(document).click(function(){
    if($('#post').is(':checked')) {
        $("#code").prop('disabled', false).css("background-color", activeCol);
        $("#name").prop('disabled', true).css("background-color", inactiveCol);
        $("#rate").prop('disabled', false).css("background-color", activeCol);
        $("#countries").prop('disabled', true).css("background-color", inactiveCol);
        //console.log("post is checked");
    }
    else if($('#delete').is(':checked')) {
        $("#code").prop('disabled', false).css("background-color", activeCol);
        $("#name").prop('disabled', true).css("background-color", inactiveCol);
        $("#rate").prop('disabled', true).css("background-color", inactiveCol);
        $("#countries").prop('disabled', true).css("background-color", inactiveCol);
        //console.log("put is checked");
    }
    else if($('#put').is(':checked')) {
        $("#code").prop('disabled', false).css("background-color", activeCol);
        $("#name").prop('disabled', false).css("background-color", activeCol);
        $("#rate").prop('disabled', false).css("background-color", activeCol);
        $("#countries").prop('disabled', false).css("background-color", activeCol);
        //console.log("delete is checked");
    }
});

$("#submitBtn").click(function(){
        //ValidateBeforePost();
        $.post( "update.php", $( "#updateForm" ).serialize(), function( data ) {
            $( "#response" ).html( data );
        });
});

function isUpperCase(str) {
    return str === str.toUpperCase();
}
