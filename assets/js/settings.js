jQuery(function($){


    var selectTab = function(tab) {
        $('.nav-tab').removeClass('nav-tab-active');
        $('.nav-tab[href="' + tab + '"]').addClass('nav-tab-active');
        $('#layotter-last-edited-tab').val(tab);
        $('.layotter-settings-tab-content:visible').hide();
        $(tab).show();
        if ($('#layotter-settings-saved-notice').length) {
            $('#layotter-settings-saved-notice').css('visibility', 'hidden').css('display', 'block').slideUp(400, function(){
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
    
    
    $('.layotter-default-value').click(function(){
        $(this).parents('tr').find('input, textarea').val($(this).html().replace(/&lt;/g, '<').replace(/&gt;/g, '>'));
    });
    
    
    var update_default_row_dropdown = function() {
        $('#layotter-row-layouts input[type="checkbox"]').each(function(){
            var layout = $(this).data('layout');
            var $option = $('#layotter-default-row-layout').children('option[value="' + layout + '"]');
            if ($(this).is(':checked')) {
                $option.show();
            } else {
                $option.hide();
                if ($option.is(':selected')) {
                    $('#layotter-default-row-layout').children('option:visible').first().prop('selected', true);
                    display_default_row_option_message();
                }
            }
        });
    };
    
    
    $('#layotter-row-layouts input[type="checkbox"]').change(function(){
        if ($('#layotter-row-layouts input[type="checkbox"]:checked').length === 0) {
            $(this).prop('checked', true);
            return;
        }
        update_default_row_dropdown();
    });
    update_default_row_dropdown();
    
    
    var display_default_row_option_message = function() {
        var layout = $('#layotter-default-row-layout').val();
        $('#layotter-row-layouts .layotter-default-row-layout-message').hide();
        $('#layotter-row-layouts input[data-layout="' + layout + '"]')
            .siblings('.layotter-row-layout-option')
            .children('.layotter-default-row-layout-message')
            .show();
    };
    
    
    $('#layotter-default-row-layout').change(function(){
        display_default_row_option_message();
    });
    display_default_row_option_message();

});