var app = angular.module('eddditor', ['ngAnimate', 'ngSanitize', 'ui.sortable']);


/**
 * Bootstrap the application
 */
angular.element(document).ready(function() {
    angular.bootstrap(document, ['eddditor']);
});


/**
 * Initialize stuff after bootstrapping
 */
app.run(function() {
    // show eddditor, hide loading spinner
    angular.element('#eddditor').show();
    angular.element('#eddditor-loading').hide();

    // make the scrollable saved elements list fill the viewport height
    var adjustTemplatesContainerHeight = function() {
        var templatesContainer = angular.element('#eddditor-templates .eddditor-elements');
        var height = angular.element('#eddditor-templates').height() - templatesContainer.position().top - 10;
        templatesContainer.height(height);
    };

    // adjust saved elements height on page load and browser resize
    adjustTemplatesContainerHeight();
    angular.element(window).on('resize', function() {
        adjustTemplatesContainerHeight();
    });
});


/**
 * Slide/fade in and out animations
 */
app.animation('.eddditor-animate', function() {
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