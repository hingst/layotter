<div id="eddditor" ng-controller="EditorCtrl" ng-class="{ 'eddditor-loading' : data.isLoading }">
    <span class="eddditor-button eddditor-post-options-button" ng-click="editOptions(data)" ng-show="optionsEnabled.post"><?php _e('Post options', 'eddditor'); ?></span>
    <span class="eddditor-button eddditor-templates-button" toggle-templates><?php _e('Element gallery', 'eddditor'); ?></span>
    <span class="eddditor-button eddditor-add-row-button" ng-click="addRow(-1)" ng-class="{ 'eddditor-add-row-button-large': data.rows.length === 0 }">
        <span ng-show="data.rows.length"><?php _e('Add row', 'eddditor'); ?></span>
        <span ng-hide="data.rows.length"><?php _e('Add your first row to get started', 'eddditor'); ?></span>
    </span>
    <div class="eddditor-rows" ui-sortable="rowSortableOptions" ng-model="data.rows">
        <div class="eddditor-row eddditor-animate" ng-repeat="row in data.rows" ng-class="{ 'eddditor-loading' : row.isLoading }">
            <div class="eddditor-row-canvas">
                <div class="eddditor-row-head">
                    <span class="eddditor-row-move" title="<?php _e('Move row', 'eddditor'); ?>"><?php _e('Move row', 'eddditor'); ?></span>
                    <div class="eddditor-row-cols">
                        <span class="eddditor-row-layout-button" ng-repeat="colbutton in allowedRowLayouts" ng-class="{ 'eddditor-row-layout-button-active': colbutton.layout === row.layout }" ng-click="setRowLayout(row, colbutton.layout)" data-layout="{{ colbutton.layout }}" title="{{ colbutton.title }}">{{ colbutton.title }}</span>
                    </div>
                    <span class="eddditor-row-delete" ng-click="deleteRow($index)" title="<?php _e('Delete row', 'eddditor'); ?>"><?php _e('Delete row', 'eddditor'); ?></span>
                    <span class="eddditor-row-duplicate" ng-click="duplicateRow($index)" title="<?php _e('Duplicate row', 'eddditor'); ?>"><?php _e('Duplicate row', 'eddditor'); ?></span>
                    <span class="eddditor-row-options" ng-click="editOptions(row)" ng-show="optionsEnabled.row" title="<?php _e('Row options', 'eddditor'); ?>"><?php _e('Options', 'eddditor'); ?></span>
                </div>
                <div class="eddditor-cols">
                    <div class="eddditor-col {{ 'eddditor-col-' + getColLayout(row, $index) }}" ng-repeat="col in row.cols">
                        <span class="eddditor-button eddditor-add-element-button" ng-click="showNewElementTypes(col.elements, -1)"><?php _e('Add element', 'eddditor'); ?></span>
                        <div class="eddditor-elements" ui-sortable="elementSortableOptions" ng-model="col.elements">
                            <div class="eddditor-element eddditor-animate" ng-repeat="element in col.elements" ng-init="watchTemplate(element)" ng-class="{ 'eddditor-loading' : element.isLoading, 'eddditor-highlight' : element.isHighlighted }">
                                <div class="eddditor-element-canvas">
                                    <div class="eddditor-element-head">
                                        <span class="eddditor-element-delete" ng-click="deleteElement(col.elements, $index)" title="<?php _e('Delete element', 'eddditor'); ?>"><?php _e('Delete element', 'eddditor'); ?></span>
                                        <span class="eddditor-element-edit" ng-hide="element.template !== undefined" ng-click="editElement(element)" title="<?php _e('Edit element', 'eddditor'); ?>"><?php _e('Edit element', 'eddditor'); ?></span>
                                        <div class="eddditor-element-dropdown">
                                            <div class="eddditor-element-dropdown-items">
                                                <span class="eddditor-element-options" ng-click="editOptions(element)" ng-show="optionsEnabled.element"><?php _e('Element options', 'eddditor'); ?></span>
                                                <span class="eddditor-element-duplicate" ng-click="duplicateElement(col.elements, $index)"><?php _e('Duplicate element', 'eddditor'); ?></span>
                                                <span class="eddditor-element-favorite" ng-hide="element.template !== undefined" ng-click="saveNewTemplate(element)"><?php _e('Add to gallery', 'eddditor'); ?></span>
                                            </div>
                                        </div>
                                        <span class="eddditor-element-move"><?php _e('Move', 'eddditor'); ?></span>
                                    </div>
                                    <div class="eddditor-element-message" ng-show="element.template !== undefined">
                                        <?php _e('This is a template.', 'eddditor'); ?> <a href="#">[?]</a>
                                    </div>
                                    <div class="eddditor-element-content" ng-bind-html="element.view"></div>
                                </div>
                                <span class="eddditor-button eddditor-add-element-button" ng-click="showNewElementTypes(col.elements, $index)"><?php _e('Add element', 'eddditor'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <span class="eddditor-button eddditor-add-row-button" ng-click="addRow($index)"><?php _e('Add row', 'eddditor'); ?></span>
        </div>
    </div>
</div>
<div id="eddditor-loading">
    <span><?php _e('Eddditor loading &hellip;', 'eddditor'); ?></span>
</div>
<script type="text/ng-template" id="eddditor-add-element">
<?php
require dirname(__FILE__) . '/add_element.php';
?>
</script>
<script type="text/ng-template" id="eddditor-modal-confirm">
<?php 
require dirname(__FILE__) . '/confirm.php';
?>
</script>