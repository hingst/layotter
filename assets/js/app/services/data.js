/**
 * Central provider for content structure and blank templates for rows, columns and elements
 */
app.service('data', function(){
    // use default post options for new posts
    if (layotterData.contentStructure === null) {
        this.contentStructure = {
            options_id: 0,
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
        id: 0,
        view: '',
        options_id: 0
    };


    // new col template
    this.templates.col = {
        elements: [],
        options_id: 0
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
        options_id: 0
    };
});