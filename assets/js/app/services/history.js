/**
 * Keep track of what's edited and enable undo
 */
app.service('history', function($animate, $timeout, data) {
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


    var updateData = function() {
        _this.data.canUndo = canUndo();
        _this.data.canRedo = canRedo();

        if (_this.data.canUndo) {
            _this.data.undoTitle = layotterData.i18n.history.undo + ' ' + steps[currentStep].title;
        } else {
            _this.data.undoTitle = '';
        }

        if (_this.data.canRedo) {
            _this.data.redoTitle = layotterData.i18n.history.redo + ' ' + steps[currentStep + 1].title;
        } else {
            _this.data.redoTitle = '';
        }
    };


    var refreshTemplates = function(content) {
        var contentClone = angular.copy(content);

        angular.forEach(contentClone.rows, function(row){
            angular.forEach(row.cols, function(col){
                angular.forEach(col.elements, function(element){
                    if (typeof element.template_id !== 'undefined' && !element.template_deleted) {
                        if (_this.deletedTemplates.indexOf(element.template_id) !== -1) {
                            element.template_id = -1;
                            element.template_deleted = true;
                        }
                    }
                });
            });
        });
        return contentClone;
    };


    var canUndo = function() {
        return (currentStep > 0);
    };


    var canRedo = function() {
        return (currentStep < steps.length - 1);
    };


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


    this.undoStep = function() {
        if (canUndo()) {
            $animate.enabled(false);

            currentStep--;
            var restore = angular.copy(steps[currentStep].content);
            restore = refreshTemplates(restore);
            data.contentStructure.options = restore.options;
            data.contentStructure.rows = restore.rows;
            updateData();

            $timeout(function(){
                $animate.enabled(true);
            }, 1);
        }
    };


    this.redoStep = function() {
        if (canRedo()) {
            $animate.enabled(false);

            currentStep++;
            var restore = angular.copy(steps[currentStep].content);
            restore = refreshTemplates(restore);
            data.contentStructure.options = restore.options;
            data.contentStructure.rows = restore.rows;
            updateData();

            $timeout(function(){
                $animate.enabled(true);
            }, 1);
        }
    };
});