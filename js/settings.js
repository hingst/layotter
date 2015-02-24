jQuery(function($){
    
    
    $('.eddditor-default-value').click(function(){
        $(this).parents('tr').find('input, textarea').val($(this).html().replace(/&lt;/g, '<').replace(/&gt;/g, '>'));
    });
    
    
    var update_default_row_dropdown = function()
    {
        $('#eddditor-row-layouts input[type="checkbox"]').each(function(){
            var layout = $(this).data('layout');
            var $option = $('#eddditor-default-row-layout').children('option[value="' + layout + '"]');
            if($(this).is(':checked'))
            {
                $option.show();
            }
            else
            {
                $option.hide();
                if($option.is(':selected'))
                {
                    $('#eddditor-default-row-layout').children('option:visible').first().prop('selected', true);
                    display_default_row_option_message();
                }
            }
        });
    };
    
    
    $('#eddditor-row-layouts input[type="checkbox"]').change(function(){
        if($('#eddditor-row-layouts input[type="checkbox"]:checked').length === 0)
        {
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