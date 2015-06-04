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
	
