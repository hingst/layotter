/**
 * Things that change the view but have no effect on data
 */
app.service('view', function() {
    this.showTemplates = function() {
        jQuery('#layotter-templates').addClass('layotter-visible');
    };
    this.toggleTemplates = function() {
        jQuery('#layotter-templates').toggleClass('layotter-visible');
    };

    jQuery(window).scroll(function(){
        var scrolled = jQuery(document).scrollTop();
        var trigger = jQuery('#layotter-top-buttons-1').offset().top;
        var left = jQuery('#adminmenuwrap').width();
        if (scrolled > trigger) {
            jQuery('#layotter-top-buttons-2').addClass('layotter-visible').css('left', left);
        } else {
            jQuery('#layotter-top-buttons-2').removeClass('layotter-visible');
        }
    });
});