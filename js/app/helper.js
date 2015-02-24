// take JSON-encoded page structure and return concatenated values of all fields that have a string value
function extractSearchDump(contentStructure) {
    var dump = '';
    angular.forEach(contentStructure.rows, function(row){
        angular.forEach(row.cols, function(col){
            angular.forEach(col.elements, function(element){
                angular.forEach(element.values, function(fieldValue){
                    if(fieldValue !== '' && !isJSON(fieldValue))
                    {
                        dump += escapeHTML(fieldValue) + ' ';
                    }
                });
            });
        });
    });
    return dump;
}

// replace special HTML characters for output where HTML is not allowed, e.g. in a form element
function escapeHTML(text) {
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

// check if a string contains valid JSON data
function isJSON(json) {
    try {
        angular.fromJson(json);
    } catch (e) {
        return false;
    }
    return true;
}