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
    // Also handles toggling achieved on action items
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
        }

        // Disable day of week
        $('.day-of-week-container').children().addClass('disabled');
        $('.day-of-week > input').attr('disabled', true);

        // Focus input
        everyXDaysInput.focus();
    });

    // History overlay toggles
    $('.overlay').click(function(){
        $('.overlay-hide').hide();
        $('.overlay').hide();
    });
    $('.overlay-cancel-button').click(function(){
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

    // Goal selectors
    $('.goal-selector').change(function(){
        var scope = $('#scope-selector').find(':selected').val();
        var category = $('#category-selector').find(':selected').val();
        var url = window.location.toString();
        url = url.substring(0, url.indexOf('goals') + 5);
        url = url + '/' + scope;

        if(category != 'all')
        {
            url = url + '/' + category;
        }

        window.location.href = url;
    });
    $('#goal-create-type-select').change(function(){
        var id = $('#goal-create-type-select').find(':selected').val();
        $('.goal-create-type-description').hide();
        $('#goal-create-type-description-' + id).show();
    });

    // Goal change push todo
    $('#goal-show-todo').change(function(){
        var span = $('#default-days-before-due-label');
        var num = $('#default-days-before-due-input');
        if($(this).is(":checked"))
        {
            num.prop('disabled', false);
            span.removeClass('disabled');
        }
        else
        {
            num.prop('disabled', true);
            span.addClass('disabled');
        }
    });

    // Goal change nav selector function
    $.fn.goalNavSelector = function(){
        $('.goal-nav-div').hide();
        $('.goal-section-title').hide();
        $('#goal-progress-div').removeClass('show-all');
        var div = $('#goal-nav-dropdown').find(':selected').val();
        if(div == 'show-all')
        {
            $('.goal-section-title').show();
            $('#goal-details-div').show();
            $('#goal-progress-div').show();
            $('#goal-progress-div').addClass('show-all');
            $('#goal-action-plan-div').show();
            $('#goal-sub-goals-div').css('display', 'flex');
            $('#goal-ad-hoc-list-div').show();
        }
        else if(div == 'sub-goals')
        {
            $('#goal-' + div + '-div').css('display', 'flex');
        }
        else
        {
            $('#goal-' + div + '-div').show();
        }
    };
    $.fn.goalNavSelector(); // Check on doc load
    $('#goal-nav-dropdown').change(function(){ // And check on select change
        $.fn.goalNavSelector();
    });


    // Goal manual progress increment/decrement
    $('#manual-completed-decrement').click(function(){
        var input = $('#manual-completed-input');
        if(parseInt(input.val()) > parseInt(input.prop('min')))
        {
            input.val(input.val() - 1);
        }
    });
    $('#manual-completed-increment').click(function(){
        var input = $('#manual-completed-input');
        if(parseInt(input.val()) < parseInt(input.prop('max')))
        {
            input.val(parseInt(input.val()) + 1);
        }
    });

    // Goal shift dates increment/decrement -- might possibly want to add a mousedown/mouseup for holding increment/decrement here
    $('#shift-days-decrement').click(function(){
        var input = $('#shift-days-input');
        if(parseInt(input.val()) > parseInt(input.prop('min')))
        {
            input.val(input.val() - 1);
        }
    });
    $('#shift-days-increment').click(function(){
        var input = $('#shift-days-input');
        if(parseInt(input.val()) < parseInt(input.prop('max')))
        {
            input.val(parseInt(input.val()) + 1);
        }
    });

    // Action item form toggle override show todo
    $.fn.checkActionItemShowTodo = function(){
        if($('#override-show-todo').is(':checked'))
        {
            $('#show-todo-options').removeClass('disabled');
            $('#action-item-show-todo').attr('disabled', false);

            if($('#action-item-show-todo').is(':checked'))
            {
                $('#days-before-due-label').removeClass('disabled');
                $('#days-before-due-input').removeClass('disabled');
                $('#days-before-due-input').attr('disabled', false);
            }
            else
            {
                $('#days-before-due-label').addClass('disabled');
                $('#days-before-due-input').addClass('disabled');
                $('#days-before-due-input').attr('disabled', true);
            }
        }
        else
        {
            $('#show-todo-options').addClass('disabled');
            $('#days-before-due-input').addClass('disabled');
            $('#action-item-show-todo').attr('disabled', true);
            $('#days-before-due-input').attr('disabled', true);
        }
    };
    $.fn.checkActionItemShowTodo(); // Check on doc load
    $('#override-show-todo').change(function(){
        $.fn.checkActionItemShowTodo();
    });
    $('#action-item-show-todo').change(function(){
        $.fn.checkActionItemShowTodo();
    });

    // Handles making sure only one journal entry mood checkbox is checked
    $('.mood-checkbox').change(function(){
        // in the handler, 'this' refers to the box clicked on
        var $box = $(this);
        if($box.is(":checked"))
        {
            // get all of the other checkboxes
            var group = $('.mood-checkbox');
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

    // Journal selectors
    $('.journal-selector').change(function(){
        var month = $('#month-selector').find(':selected').val();
        var year = $('#year-selector').find(':selected').val();
        var url = window.location.toString();
        url = url.substring(0, url.indexOf('list') + 4);
        url = url + '/' + month + '/' + year;
        window.location.href = url;
    });

    // Journal filter
    $('#journal-filter-selector').change(function(){
        $('.summary-hide').hide();
        var show = $('#journal-filter-selector').find(':selected').val();
        switch(show)
        {
            case 'all':
                $('.summary-hide').show();
                break;
            case 'affirmations':
                $('.summary-show-affirmations').show();
                break;
            case 'habit':
                $('.summary-show-habits').show();
                break;
            case 'todo':
                $('.summary-show-todo-item').show();
                break;
            case 'goal':
                $('.summary-show-goal').show();
                break;
            case 'action-item':
                $('.summary-show-action-item').show();
                break;
            case 'journal-entry':
                $('.summary-show-journal-entry').show();
                break;
        }
    });

    // Stripe selector
    $('#subscription-select').change(function(){
        $('.sub-desc').hide();
        $('.checkout-button').hide();
        var show = $(this).find(':selected').val();
        $('.' + show).show();
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

window.verifyRemoveForm = function (message, formID){
    swal.fire({
        title: `<span class="swal-title" style="color:#ffffff">${message}</span>`,
        text: 'You are about to remove this sub-goal from it\'s current parent goal.',
        icon: 'warning',
        iconColor: '#d12828',
        padding: '.5rem',
        showCancelButton: true,
        confirmButtonColor: '#d12828',
        cancelButtonColor: '#155466',
        confirmButtonText: 'Yes, remove it!',
        background: '#3b3b3b',
    }).then((result) => {
        if(result.isConfirmed)
        {
            $(formID).submit();
        }
    });
}

// For informing an upgrade is needed -- uses sweetalert2
window.blackLabelUpgrade = function (url){
    swal.fire({
        title: `<span class="swal-title" style="color:#ffffff">Black Label Upgrade</span>`,
        text: 'You need upgrade to a Black Label membership to use this feature.',
        icon: 'info',
        iconColor: '#d12828',
        padding: '.5rem',
        showCancelButton: true,
        confirmButtonColor: '#155466',
        cancelButtonColor: '#d12828',
        confirmButtonText: 'Upgrade',
        background: '#3b3b3b',
    }).then((result) => {
        if(result.isConfirmed)
        {
            location.href = url;
        }
    });
}
