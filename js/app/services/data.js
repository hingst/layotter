app.service('data', function(){
    

    // use default post options for new posts
    if (eddditorData.contentStructure === null) {
        this.contentStructure = {
            options: eddditorData.options.post.defaults,
            rows: []
        };
    } else {
        this.contentStructure = angular.copy(eddditorData.contentStructure);
    }
    
    
    // empty templates
    this.templates = {};
    var defaultRowLayout = eddditorData.defaultRowLayout;


    // new element template
    this.templates.element = {
        type: undefined,
        values: false,
        view: '',
        options: eddditorData.options.element.defaults
    };


    // new col template
    this.templates.col = {
        elements: []
    };


    // set up default cols for new row template
    var defaultColCount = defaultRowLayout.split(' ').length;
    var defaultCols = [];
    for (var i = 0; i < defaultColCount; i++) {
        defaultCols.push(angular.copy(this.templates.col));
    }


    // new row template
    this.templates.row = {
        layout: defaultRowLayout,
        cols: defaultCols,
        options: eddditorData.options.row.defaults
    };
    
});