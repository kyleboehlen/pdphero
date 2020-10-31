require('./bootstrap');

// Handles toggling the the nav in mobile mode
$('#hamburger-nav').click(function(){
    $('nav').toggle('slide');
});
$('#close-nav').click(function(){
    $('nav').toggle('slide');
});
$('.close-nav').click(function(){
    if($('#close-nav').is(":visible"))
    {
        $('nav').toggle('slide');
    }
});