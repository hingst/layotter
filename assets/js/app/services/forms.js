/**
 * Provides methods for form overlay creation
 */
app.service('forms', function($http, $compile, $rootScope, $timeout){


    var _this = this;
    this.data = {}; // contains field data for the form that's currently being displayed


    angular.element(document).on('submit', '#layotter-edit', function(){
        // ACF compatibility
        // ACF uses an ignore flag when the normal post form is submitted, so that the
        // submit button triggers validation, not form submission. after validation has
        // completed successfully, acf fires a click event on $trigger, thus actually
        // submitting the form.
        // set ignore to 0, otherwise validation will only be called the first time a
        // form is submitted.
        acf.validation.ignore = 0;
        acf.validation.$trigger = jQuery('#layotter-edit-submit');

        // prevent form changes while validation is running
        jQuery(':focus').blur();
        jQuery('.layotter-modal-loading-container').addClass('layotter-loading');
        jQuery('.layotter-modal-foot button').prop('disabled', true);
    });


    angular.element(document).on('submit', '.layotter-modal #post', function(){
        if (acf.validation.status) {
            angular.element('#layotter-edit-submit').trigger('click');
            return false;
        }
    });


    // when validation is complete: if there was an error, remove loading spinner and show form.
    // ACF 4 uses a simpler validation mechanism without AJAX, so this applies only to ACF 5 Pro
    if (layotterData.isACFPro) {
        acf.add_filter('validation_complete', function(json, $form) {
            if ($form.prop('id') == 'layotter-edit' && typeof json !== 'undefined' && !json.result) {
                jQuery('.layotter-modal-loading-container').removeClass('layotter-loading');
                jQuery('.layotter-modal-foot button').prop('disabled', false);
            }
            return json;
        });
    }


    /**
     * Display HTML content in the lightbox
     *
     * @param html HTML string
     */
    this.showHTML = function(html) {
        create(html);
    };


    /**
     * Fetch HTML via POST and display the result
     *
     * @param url URL to get content from
     * @param data POST data
     */
    this.fetchDataAndShowForm = function(url, data) {
        create(); // loading
        $http({
            url: url,
            method: 'POST',
            data: jQuery.param(data),
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(reply) {
            _this.data = reply;
            create(jQuery('#layotter-form').html());
        });
    };


    /**
     * Open up the lightbox and show passed HTML content
     */
    var create = function(html) {
        // animate if opening a new lightbox, don't animate if replacing another lightbox
        var animate = true;
        if (angular.element('#dennisbox').length) {
            angular.element('#dennisbox').remove();
            animate = false;
        }
        
        var box = angular.element('<div id="dennisbox"><div class="dennisbox-overlay"></div><div class="dennisbox-content"></div></div>');
        var topOffset = parseInt(angular.element(document).scrollTop() + angular.element(window).height() / 2); // vertical center of current screen
        var height = parseInt(angular.element(window).height() * 0.8); // 80% of viewport height
        var marginTop = animate ? (-height/2) + 10 : -height/2; // add a bit of an offset if animation is enabled

        box.appendTo('body');
        var contentBox = box.children('.dennisbox-content');

        contentBox
            .css('height', height)
            .css('width', 700)
            .css('margin-top', marginTop)
            .css('top', topOffset);

        // fade and slide in if animation is enabled
        if (animate) {
            contentBox
                .css('opacity', 0)
                .animate({ marginTop: -height/2, opacity: 1 }, 300);
        }

        // undefined content means "just show a loading spinner for now"
        if (typeof html === 'undefined') {
            contentBox.addClass('dennisbox-loading');
            return;
        } else if (typeof html === 'string') {
            contentBox.html(html);
        }

        // compile lightbox contents
        $timeout(function(){
            $rootScope.$apply($compile(angular.element('#dennisbox'))($rootScope));

            // setup javascript for fields
            if (layotterData.isACFPro) {
                acf.get_fields({}, jQuery('#layotter-form')).each(function() {
                    acf.do_action('ready_field', jQuery(this));
                    acf.do_action('ready_field/type=' + acf.get_field_type(jQuery(this)), jQuery(this));
                });
                acf.do_action('append', jQuery('#layotter-edit'));
            } else {
                jQuery(document).trigger('acf/setup_fields', [ jQuery('.layotter-modal #post') ]);
            }
        }, 1);
    };


    /**
     * Toggle fullscreen editing
     */
    this.toggleFullscreen = function() {
        var box = jQuery('#dennisbox .dennisbox-content');
        var isFullscreen = box.outerWidth() !== 700;
        var top, height, marginTop;

        if (isFullscreen) {
            top = parseInt(angular.element(document).scrollTop() + angular.element(window).height() / 2); // vertical center of current screen
            height = parseInt(angular.element(window).height() * 0.8); // 80% of viewport height
            marginTop = -height/2;
            box.animate({
                height: height,
                width: 700,
                marginTop: marginTop,
                top: top
            }, function() {
                jQuery('.layotter-modal-head-fullscreen-expand').show();
                jQuery('.layotter-modal-head-fullscreen-compress').hide();
            });
        } else {
            top = jQuery(document).scrollTop();
            box.animate({
                height: '100%',
                width: '100%',
                marginTop: 0,
                top: top
            }, function() {
                jQuery('.layotter-modal-head-fullscreen-expand').hide();
                jQuery('.layotter-modal-head-fullscreen-compress').show();
            });
        }
    };


    /**
     * Close the lightbox
     */
    this.close = function() {
        var box = angular.element('#dennisbox');
        if (box.length) {
            box.children().fadeOut(300, function(){
                angular.element(this).parent().remove();

                // ACF compatibility
                // unlock Wordpress save post form (remove loading spinner and enable form submit)
                // ACF 4 uses a simpler validation mechanism without AJAX, so this applies only to ACF 5 Pro
                if (layotterData.isACFPro) {
                    acf.validation.toggle(jQuery('#layotter-form'), 'unlock');
                }
            });

            // ACF compatibility
            // see create() for more info
            acf.validation.$trigger = null;
            acf.validation.ignore = 0;
        }
    }
    
    
});