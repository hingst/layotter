app.service('view', function() {

    this.showTemplates = function() {
        angular.element('#layotter-templates').addClass('layotter-visible');
    };

    this.hideTemplates = function() {
        angular.element('#layotter-templates').removeClass('layotter-visible');
    };

    this.toggleTemplates = function() {
        angular.element('#layotter-templates').toggleClass('layotter-visible');
    };
    
});