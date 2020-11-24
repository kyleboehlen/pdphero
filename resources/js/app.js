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

// Handles submitting the toggle checkbox forms
$('.submit-completed').change(function(){
    event.target.closest('form').submit();
});

// Handles making sure only one priority checkbox is selected
$('.priority-checkbox').change(function() {
    // in the handler, 'this' refers to the box clicked on
    var $box = $(this);
    if($box.is(":checked"))
    {
        // get all of the other checkboxes
        var group = $('.priority-checkbox');
        // the checked state of the group/box on the other hand will change
        // and the current value is retrieved using .prop() method
        $(group).prop("checked", false);
        $box.prop("checked", true);
    }
    else
    {
        $box.prop("checked", false);
    }
});