function start_spinner() {
	$("#loader").addClass("loader");
    $("#explanation").text("Server is now processing the log and will soon send the data to the browser");
}

function mid_spinner() {
	$("#loader").css("border-left","1.1em solid #0C6943");
    $("#explanation").text("Data has been recieved, now the browser will render it");
}

function end_spinner() {
	$("#loader").removeClass("loader");
	$("#explanation").text("Process ended");
}

function check_dates() {
	s = moment( $("#start_date").val() , "DD/MM/YYYY@HH:mm:ss");
	e = moment( $("#end_date").val() , "DD/MM/YYYY@HH:mm:ss");
	if ( e.diff(s) > 3605000 ) {
		$("#start_date, #end_date").css("background-color","rgba(255, 0, 0, 0.1)");
		$("#explanation").text("Often is better to check only a little amount of time with this graph (1h)");
	}else {
		$("#start_date, #end_date").css("background-color","");
	}
}

function makeid(){
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    for( var i=0; i < 5; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));
    return text;
}

var string_name = makeid();

$(document).ready(function(){
	if(typeof moment !== 'undefined') {
		setInterval(function() { check_dates(); }, 700); //must use a timer check because all input events are overridden by datetime-picker library
	}
});
