app.service('data', function(){
    

    // use default post options for new posts
    if (layotterData.contentStructure === null) {
        this.contentStructure = {
            options: layotterData.options.post.defaults,
            rows: []
        };
    } else {
        this.contentStructure = angular.copy(layotterData.contentStructure);
    }
    
    
    // empty templates
    this.templates = {};
    var defaultRowLayout = layotterData.defaultRowLayout;


    // new element template
    this.templates.element = {
        type: undefined,
        values: [],
        view: '',
        options: layotterData.options.element.defaults
    };


    // new col template
    this.templates.col = {
        elements: [],
        options: layotterData.options.col.defaults
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
        options: layotterData.options.row.defaults
    };
    
});