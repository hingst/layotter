/**
 * Keep track of what's currently being edited
 */
app.service('state', ['forms', function(forms){


    var _this = this;
    this.showBackButton = false;
    var editing = {
        reset: function() {
            this.element = null;
            this.parent = null;
            this.index = -1;
            this.optionsType = null;
            _this.showBackButton = false;
            forms.close();
            forms.fieldsChanged = false;
            forms.listenForFieldChanges = false;
        }
    };
    editing.reset();


    /**
     * Reset to initial state (close forms and discard all state data)
     */
    this.reset = function() {
        editing.reset();
    };


    /**
     * Set/get which element is being edited
     */
    this.setElement = function(element) {
        editing.element = element;
    };
    this.getElement = function() {
        return editing.element;
    };


    /**
     * Set/get parent and index of the element thats currently being edited
     *
     * Required to insert newly created elements at the correct position
     */
    this.setParent = function(parent) {
        editing.parent = parent;
    };
    this.setIndex = function(index) {
        editing.index = index;
    };
    this.getParent = function() {
        return editing.parent;
    };
    this.getIndex = function() {
        return editing.index;
    };


    /**
     * Set/get type of the currently edited options (post, row, column or element options)
     */
    this.setOptionsType = function(type) {
        editing.optionsType = type;
    };
    this.getOptionsType = function() {
        return editing.optionsType;
    };


    /**
     * Define whether the back button should be displayed in the edit form (only if creating a new element)
     */
    this.setBackButton = function(bool) {
        _this.showBackButton = bool;
    };
    
}]);