app.service('forms', function($http, $compile, $rootScope, $timeout){


    angular.element(document).on('click', '#eddditor-edit-fake-submit', function(){
        // acf compatibility
        // acf uses an ignore flag when the normal post form is submitted, so that the
        // submit button triggers validation, not form submission. after validation has
        // completed successfully, acf fires a click event on $trigger, thus actually
        // submitting the form.
        // set ignore to 0, otherwise validation will only be called the first time a
        // form is submitted.
        acf.validation.ignore = 0;
        acf.validation.$trigger = jQuery('#eddditor-edit-submit');
    })


    /**
     * Display HTML content in the lightbox
     *
     * @param html HTML string
     */
    this.show = function(html) {
        create(html);
    };


    /**
     * Fetch HTML via GET and display the result
     *
     * @param url URL to get content from
     */
    this.get = function(url) {
        create(); // loading
        $http.get(url).success(function(reply){
            create(reply);
        });
    };


    /**
     * Fetch HTML via POST and display the result
     *
     * @param url URL to get content from
     * @param data POST data
     */
    this.post = function(url, data) {
        create(); // loading
        $http.post(url, data).success(function(reply){
            create(reply);
        });
    };


    /**
     * Open up the lightbox - internal use only
     *
     * @param content HTML string to be displayed
     */
    var create = function(content) {
        // animate if opening a new lightbox, don't animate if replacing another lightbox
        var animate = true;
        if (angular.element('#dennisbox').length) {
            angular.element('#dennisbox').remove();
            animate = false;
        }
        
        var box = angular.element('<div id="dennisbox"><div class="dennisbox-overlay"></div><div class="dennisbox-content"></div></div>');
        var topOffset = parseInt(angular.element(document).scrollTop() + angular.element(window).height() / 2); // vertical center of current screen
        var height = parseInt(angular.element(window).height() * 0.8); // 80% of viewport height
        var marginTop = animate ? (-height/2) + 10 : -height/2; // initially add with a bit of an offset if animation is enabled
        
        box.appendTo('body')
            .find('.dennisbox-content')
                .css('height', height)
                .css('width', 700)
                .css('margin-top', marginTop)
                .css('top', topOffset);

        // gracefully remove top offset and fade in if animation is enabled
        if (animate) {
            box.find('.dennisbox-content')
                .css('opacity', 0)
                .animate({ marginTop: -height/2, opacity: 1 }, 300);
        }

        // undefined content means "just show a loading spinner for now"
        if (typeof content === 'undefined') {
            box.find('.dennisbox-content')
                .addClass('dennisbox-loading');
            return;
        } else {
            box.find('.dennisbox-content')
                .html(content);
        }

        // compile lightbox contents
        $timeout(function(){
            $rootScope.$apply($compile(angular.element('#dennisbox'))($rootScope));

            // setup javascript for fields
            acf.get_fields({}, jQuery('#eddditor-form')).each(function(){
                acf.do_action('ready_field', jQuery(this));
                acf.do_action('ready_field/type=' + acf.get_field_type(jQuery(this)), jQuery(this));
            });
            acf.do_action('append', jQuery('#eddditor-edit'));
        }, 1);
    };


    /**
     * Close the lightbox
     */
    this.close = function() {
        // acf compatibility - see create() for more info
        acf.validation.$trigger = null;
        acf.validation.ignore = 0;
        
        var box = angular.element('#dennisbox');
        if (box.length) {
            box.children().fadeOut(300, function(){
                angular.element(this).parent().remove();
            });
        }
    }
    
    
});