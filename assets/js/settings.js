jQuery(function($){


    var selectTab = function(tab) {
        $('.nav-tab').removeClass('nav-tab-active');
        $('.nav-tab[href="' + tab + '"]').addClass('nav-tab-active');
        $('.eddditor-settings-tab-content:visible').fadeOut(100, function(){
            $(tab).fadeIn(50);
        });
        if ($('#eddditor-settings-saved-notice').length) {
            $('#eddditor-settings-saved-notice').css('visibility', 'hidden').css('display', 'block').slideUp(400, function(){
                $(this).remove();
            });
        }
    };
    $('.nav-tab').click(function(event){
        event.preventDefault();
        if (!$(this).hasClass('nav-tab-active')) {
            selectTab($(this).attr('href'));
        }
    });
    
    
    $('.eddditor-default-value').click(function(){
        $(this).parents('tr').find('input, textarea').val($(this).html().replace(/&lt;/g, '<').replace(/&gt;/g, '>'));
    });
    
    
    var update_default_row_dropdown = function() {
        $('#eddditor-row-layouts input[type="checkbox"]').each(function(){
            var layout = $(this).data('layout');
            var $option = $('#eddditor-default-row-layout').children('option[value="' + layout + '"]');
            if ($(this).is(':checked')) {
                $option.show();
            } else {
                $option.hide();
                if ($option.is(':selected')) {
                    $('#eddditor-default-row-layout').children('option:visible').first().prop('selected', true);
                    display_default_row_option_message();
                }
            }
        });
    };
    
    
    $('#eddditor-row-layouts input[type="checkbox"]').change(function(){
        if ($('#eddditor-row-layouts input[type="checkbox"]:checked').length === 0) {
            $(this).prop('checked', true);
            return;
        }
        update_default_row_dropdown();
    });
    update_default_row_dropdown();
    
    
    var display_default_row_option_message = function() {
        var layout = $('#eddditor-default-row-layout').val();
        $('#eddditor-row-layouts .eddditor-default-row-layout-message').hide();
        $('#eddditor-row-layouts input[data-layout="' + layout + '"]')
            .siblings('.eddditor-row-layout-option')
            .children('.eddditor-default-row-layout-message')
            .show();
    };
    
    
    $('#eddditor-default-row-layout').change(function(){
        display_default_row_option_message();
    });
    display_default_row_option_message();

});