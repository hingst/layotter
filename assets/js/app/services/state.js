/**
 * Keep track of what's currently being edited
 */
app.service('state', function(forms){
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
        }
    };
    editing.reset();


    this.reset = function() {
        editing.reset();
    };


    this.setElement = function(element) {
        editing.element = element;
    };


    this.setParent = function(parent) {
        editing.parent = parent;
    };


    this.setIndex = function(index) {
        editing.index = index;
    };


    this.setOptionsType = function(type) {
        editing.optionsType = type;
    };


    this.getElement = function() {
        return editing.element;
    };


    this.getParent = function() {
        return editing.parent;
    };


    this.getIndex = function() {
        return editing.index;
    };


    this.getOptionsType = function() {
        return editing.optionsType;
    };


    this.setBackButton = function(bool) {
        _this.showBackButton = bool;
    };
    
});