app.service('view', function() {

    this.showTemplates = function() {
        angular.element('#eddditor-templates').addClass('eddditor-visible');
    };

    this.hideTemplates = function() {
        angular.element('#eddditor-templates').removeClass('eddditor-visible');
    };

    this.toggleTemplates = function() {
        angular.element('#eddditor-templates').toggleClass('eddditor-visible');
    };
    
});