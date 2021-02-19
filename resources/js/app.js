require('./bootstrap');

$(document).ready(function(){
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
    // Also handles the toggles on the settings page
    $('.submit-completed').change(function(){
        event.target.closest('form').submit();
    });

    // Handles making sure only one priority checkbox is selected
    $('.priority-checkbox').change(function(){
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

    // Close pop up boxes after 'okay' is hit
    $('.close-box').click(function(){
        $(this).parent().hide();
    });

    // Profile picture toggle input
    $('.profile-picture-link').click(function(){
        $('#profile-picture-input').click();
    });

    // Profile picture submits form
    $('#profile-picture-input').change(function(){
        $('#profile-picture-form').submit();
    });

    // Toggles trashcan/delete button in values list
    $('.trash-can').click(function(){
        // Start by hiding all delete buttons and show
        // All the trashcans
        $('.trash-can').show();
        $('.delete').hide();

        $(this).hide(); // Hide clicked trashcan
        $(this).siblings('.delete').show(); // And show it's delete button

        // Automatically hide the delete button after
        // 2 seconds if user does nothing
        $(this).delay(200).show(1);
        $(this).siblings('.delete').delay(2000).hide(1);
    });

    // Handles submitting seettings
    $('.numeric-setting').focusout(function(){
        if($(this).val() != null && $(this).val() != '')
        {
            $(this).parent().submit();
        }
    });

    $('.options-setting').change(function(){
        if($(this).val() != null && $(this).val() != '')
        {
            $(this).parent().submit();
        }
    });

    // Handles toggling disabled classes and attributes between day of week and every x days inputs
    $('.day-of-week').click(function(){
        // Remove disabled classes
        $('.day-of-week').removeClass('disabled');

        // Remove checkbox disabled
        var dayOfWeekCheckbox = $(this).children('input');
        var attr = dayOfWeekCheckbox.attr('disabled');
        if(typeof attr !== typeof undefined && attr !== false) {
            dayOfWeekCheckbox.removeAttr('disabled');
            dayOfWeekCheckbox.prop('checked', !dayOfWeekCheckbox.prop('checked'));
        }

        // Disabled every x days
        $('.every-x-days').addClass('disabled');
        $('#every-x-days-input').prop('disabled', true);
    });
    $('.every-x-days').click(function(){
        // Remove disabled classes
        $('.every-x-days').removeClass('disabled');

        // Remove disabled attr from input
        var everyXDaysInput = $('#every-x-days-input');
        var attr = everyXDaysInput.attr('disabled');
        if(typeof attr !== typeof undefined && attr !== false) {
            everyXDaysInput.attr('disabled', false);
            everyXDaysInput.focus();
        }

        // Disable day of week
        $('.day-of-week-container').children().addClass('disabled');
        $('.day-of-week > input').attr('disabled', true);
    });

    // History overlay toggles
    $('.overlay').click(function(){
        $('.overlay-hide').hide();
        $('.overlay').hide();
    });
    $('.history-cancel-button').click(function(){
        $('.overlay-hide').hide();
        $('.overlay').hide();
    });

    // History status checkbox toggles
    $('.history-status-checkbox').change(function(){
        if(this.checked)
        {
            $('.history-status-checkbox').prop('checked', false);
            $(this).prop('checked', true);

            if(this.id.includes('skipped'))
            {
                $('#' + this.id.replace('skipped', 'label')).text('Skipped');
                $('#' + this.id.replace('skipped', 'notes')).attr('placeholder', 'If you\'re skipping you must explain yourself!');
                $('#' + this.id.replace('skipped', 'notes')).attr('required', true);
                $('#' + this.id.replace('skipped', 'times-container')).hide();
                $('#' + this.id.replace('completed', 'input').replace('status', 'times')).prop('disabled', true);
            }
            if(this.id.includes('completed'))
            {
                $('#' + this.id.replace('completed', 'label')).text('Completed');
                $('#' + this.id.replace('completed', 'notes')).attr('placeholder', 'Notes');
                $('#' + this.id.replace('completed', 'notes')).attr('required', false);
                $('#' + this.id.replace('completed', 'times-container')).show();
                $('#' + this.id.replace('completed', 'input').replace('status', 'times')).prop('disabled', false);
            }
            if(this.id.includes('missed'))
            {
                $('#' + this.id.replace('missed', 'label')).text('Missed');
                $('#' + this.id.replace('missed', 'notes')).attr('placeholder', 'Notes');
                $('#' + this.id.replace('missed', 'notes')).attr('required', false);
                $('#' + this.id.replace('missed', 'times-container')).hide();
                $('#' + this.id.replace('completed', 'input').replace('status', 'times')).prop('disabled', true);
            }
        }
        else
        {
            if(this.id.includes('skipped'))
            {
                $('#' + this.id.replace('skipped', 'label')).text('To Be Determined');
                $('#' + this.id.replace('skipped', 'notes')).attr('placeholder', 'Notes');

            }
            if(this.id.includes('completed'))
            {
                $('#' + this.id.replace('completed', 'label')).text('To Be Determined');
                $('#' + this.id.replace('completed', 'notes')).attr('placeholder', 'Notes');
            }
            if(this.id.includes('missed'))
            {
                $('#' + this.id.replace('missed', 'label')).text('To Be Determined');
                $('#' + this.id.replace('missed', 'notes')).attr('placeholder', 'Notes');
            }
        }
    });

    // History img incrementer/decrementer
    $('.history-times-decrement').click(function(){
        var input = $('#' + this.id.replace('decrement', 'input'));
        if(input.val() > input.prop('min'))
        {
            input.val(input.val() - 1);
        }
    });

    $('.history-times-increment').click(function(){
        var input = $('#' + this.id.replace('increment', 'input'));
        if(input.val() < input.prop('max'))
        {
            input.val(parseInt(input.val()) + 1);
        }
    });

    // Home hide/show functionality
    $('.hide-show').click(function(){
        $(this).children()[0].submit();
    });
});

// Replaces custom alert pop-up boxes
window.sweetAlert = function (title, icon, color, message){
    swal.fire({
        title: `<span class="swal-title" style="color:#ffffff">${title}</span>`,
        icon: icon,
        iconColor: color,
        text: message,
        padding: '.5rem',
        showCancelButton: false,
        confirmButtonColor: '#155466',
        confirmButtonText: 'Okay',
        background: '#3b3b3b',
    }).then((result) => {
        // Don't do anything
    });
}

// For verifying delete forms -- uses sweetalert2
window.verifyDeleteForm = function (message, formID){
    swal.fire({
        title: `<span class="swal-title" style="color:#ffffff">${message}</span>`,
        icon: 'warning',
        iconColor: '#d12828',
        padding: '.5rem',
        showCancelButton: true,
        confirmButtonColor: '#d12828',
        cancelButtonColor: '#155466',
        confirmButtonText: 'Yes, delete it!',
        background: '#3b3b3b',
    }).then((result) => {
        if(result.isConfirmed)
        {
            $(formID).submit();
        }
    });
}