app.service('state', function(forms){
    
    
    // keep state of what's currently being edited
    var _this = this;
    this.showBackButton = false;
    var editing = {
        reset: function() {
            this.element = null;
            this.parent = null;
            this.index = -1;
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


    this.getElement = function() {
        return editing.element;
    };


    this.getParent = function() {
        return editing.parent;
    };


    this.getIndex = function() {
        return editing.index;
    };


    this.setBackButton = function(bool) {
        _this.showBackButton = bool;
    };
    
});