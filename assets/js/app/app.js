var app = angular.module('layotter', ['ngAnimate', 'ngSanitize', 'ui.sortable']);


/**
 * Bootstrap the application
 */
jQuery(document).ready(function() {
    angular.bootstrap(document, ['layotter']);
});


/**
 * Initialize UI after bootstrapping
 */
app.run(function() {
    // show layotter, hide loading spinner
    jQuery('#layotter').show();
    jQuery('#layotter-loading').hide();

    // make the scrollable saved elements list fill the viewport height
    var adjustTemplatesContainerHeight = function() {
        var $templatesContainer = jQuery('#layotter-templates .layotter-elements');
        var height = jQuery('#layotter-templates').height() - $templatesContainer.position().top - 10;
        $templatesContainer.height(height);
    };

    // adjust saved elements height on page load and browser resize
    adjustTemplatesContainerHeight();
    jQuery(window).on('resize', function() {
        adjustTemplatesContainerHeight();
    });
});


/**
 * Slide/fade in and out animations
 */
app.animation('.layotter-animate', function() {
    return {
        enter: function(element, done) {
            jQuery(element).hide().css('visibility', 'hidden').slideDown(400, function() {
                jQuery(this).css('visibility', 'visible').hide().fadeIn(400, done);
            });
        },
        leave: function(element, done) {
            jQuery(element).fadeTo(400, 0).slideUp(400, done);
        }
    };
});


/**
 * Show/hide saved elements sidebar
 */
app.directive('toggleTemplates', function(view) {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            element.on('click', function(){
                view.toggleTemplates();
            });
        }
    };
});


/**
 * Show unsafe content (form fields) in raw HTML output
 */
app.filter('rawHtml', ['$sce', function($sce){
    return function(text) {
        return $sce.trustAsHtml(text);
    };
}]);