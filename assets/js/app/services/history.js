/**
 * Keep track of what's edited and enable undo and redo
 */
app.service('history', ['$animate', '$timeout', 'data', function($animate, $timeout, data) {
    var _this = this;
    var steps = [];
    var currentStep = -1;
    
    this.data = {};
    this.data.canUndo = false;
    this.data.undoTitle = '';
    this.data.canRedo = false;
    this.data.redoTitle = '';

    // keep track of deleted template IDs to know which elements to update when undoing/redoing a step
    this.deletedTemplates = [];


    /**
     * Updates information used to generate the UI, like descriptions for the last step
     */
    var updateData = function() {
        _this.data.canUndo = canUndo();
        _this.data.canRedo = canRedo();

        if (_this.data.canUndo) {
            _this.data.undoTitle = window.layotterData.i18n.history.undo + ' ' + steps[currentStep].title;
        } else {
            _this.data.undoTitle = '';
        }

        if (_this.data.canRedo) {
            _this.data.redoTitle = window.layotterData.i18n.history.redo + ' ' + steps[currentStep + 1].title;
        } else {
            _this.data.redoTitle = '';
        }
    };


    /**
     * Scenario: an instance of an element template is deleted, then the actual template is deleted, then the
     *           instance deletion is undone
     *
     * An error will occur in this situation because the template data is not available anymore. Therefore we'll
     * keep track of deleted templates and turn the template instance into a regular element on undeletion.
     */
    var refreshTemplates = function(content) {
        var contentClone = angular.copy(content);

        angular.forEach(contentClone.rows, function(row){
            angular.forEach(row.cols, function(col){
                angular.forEach(col.elements, function(element){
                    if (element.is_template && !element.template_deleted) {
                        if (_this.deletedTemplates.indexOf(element.id) !== -1) {
                            element.is_template = false;
                            element.template_deleted = true;
                        }
                    }
                });
            });
        });
        return contentClone;
    };


    /**
     * Check if undo is available
     */
    var canUndo = function() {
        return (currentStep > 0);
    };


    /**
     * Check if redo is available
     */
    var canRedo = function() {
        return (currentStep < steps.length - 1);
    };


    /**
     * Add an undo-able step, must be called after any changes to the content structure
     */
    this.pushStep = function(title) {
        // remove all steps that have previously been undone
        if (canRedo()) {
            steps.splice(currentStep + 1, steps.length);
        }

        steps.push({
            title : title,
            content: angular.copy(data.contentStructure)
        });
        currentStep++;
        updateData();
    };
    this.pushStep('Loaded page');


    /**
     * Undo a step
     */
    this.undoStep = function() {
        if (canUndo()) {
            $animate.enabled(false);

            currentStep--;
            var restore = angular.copy(steps[currentStep].content);
            restore = refreshTemplates(restore);
            data.contentStructure.options_id = restore.options_id;
            data.contentStructure.rows = restore.rows;
            updateData();

            $timeout(function(){
                $animate.enabled(true);
            }, 1);
        }
    };


    /**
     * Redo a step
     */
    this.redoStep = function() {
        if (canRedo()) {
            $animate.enabled(false);

            currentStep++;
            var restore = angular.copy(steps[currentStep].content);
            restore = refreshTemplates(restore);
            data.contentStructure.options_id = restore.options_id;
            data.contentStructure.rows = restore.rows;
            updateData();

            $timeout(function(){
                $animate.enabled(true);
            }, 1);
        }
    };
}]);