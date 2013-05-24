//Last know size of the file
var lastsize = 0;

//Grep keyword
var grep = "";

//Should the Grep be inverted?
var invert = 0;

//Last known document height
var documentHeight = 0; 

//Last known scroll position
var scrollPosition = 0; 

//Should we scroll to the bottom?
var scroll = true;

// Interval used for clearing
var intervalHandler = undefined;

$(document).ready(function(){
    // Setup the settings dialog
    $( "#settings" ).dialog({
        modal: true,
        resizable: false,
        draggable: false,
        autoOpen: false,
        width: 590,
        height: 270,
        buttons: {
            Close: function() {
                $( this ).dialog( "close" );
            }
        },
        close: function(event, ui) { 
            grep = $("#grep").val();
            invert = $('#invert input:radio:checked').val();
            $("#grepspan").html("Grep keyword: \"" + grep + "\"");
            $("#invertspan").html("Inverted: " + (invert == 1 ? 'true' : 'false'));
        }
    });
    
    //Close the settings dialog after a user hits enter in the textarea
    $('#grep').keyup(function(e) {
        if(e.keyCode == 13) {
            $( "#settings" ).dialog('close');
        }
    });		
    
    //Focus on the textarea					
    $("#grep").focus();
    
    //Settings button into a nice looking button with a theme
    $("#grepKeyword").button();
    
    //Settings button opens the settings dialog
    $("#grepKeyword").click(function(){
        $( "#settings" ).dialog('open');
        $("#grepKeyword").removeClass('ui-state-focus');
    });

    
    // Set up an interval for updating the log. Change updateTime in the PHPTail contstructor to change this
    $("#readLog").click(function() {
        var log = $("#logFile").val();
        intervalHandler = setInterval(function(){updateLog(log)}, 2000);
    });

    // Stop reading the log
    $("#stopLog").click(function() {
        clearInterval(intervalHandler);
    });

    // Clear the log results
    $("#clearResults").click(function() {
        $("#results").empty();
    });
    
    //If window is resized should we scroll to the bottom?
    $(window).resize(function(){
        if(scroll) {
            scrollToBottom();
        }
    });
    
    //Handle if the window should be scrolled down or not
    $(window).scroll(function(){
        documentHeight = $(document).height(); 
        scrollPosition = $(window).height() + $(window).scrollTop(); 
        if(documentHeight <= scrollPosition) {
            scroll = true;
        }
        else {
            scroll = false; 
        }
    });
    scrollToBottom();
                        
});

//This function scrolls to the bottom
function scrollToBottom() {
    $('.ui-widget-overlay').width($(document).width());
    $('.ui-widget-overlay').height($(document).height());

    $("html, body").scrollTop($(document).height());
    if($( "#settings" ).dialog("isOpen")) {
        $('.ui-widget-overlay').width($(document).width());
        $('.ui-widget-overlay').height($(document).height());
        $( "#settings" ).dialog("option", "position", "center");
    }
}
//This function queries the server for updates.
function updateLog(file) {
    $.getJSON('tail?ajax=1&lastsize='+lastsize + '&file='+file + '&grep='+grep + '&invert='+invert, function(data) {
        lastsize = data.size;
        $.each(data.data, function(key, value) { 
            $("#results").append('' + value + '<br/>');
        });
        if(scroll) {
            scrollToBottom();
        }
    });
}
