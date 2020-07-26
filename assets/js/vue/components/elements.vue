<template>
    <div class="layotter-elements" ui-sortable="elementSortableOptions" ng-model="col.elements">
        <div class="layotter-element layotter-element-{ {$index} } layotter-animate" data-id="{ { element.id } }" ng-repeat="element in col.elements" ng-class="{ 'layotter-loading' : element.isLoading, 'layotter-highlight' : element.isHighlighted }">
            <div class="layotter-element-canvas">
                <div class="layotter-element-buttons">
                    <span class="layotter-element-button" ng-click="deleteElement(col.elements, $index)" :title="'delete_element' | translate"><i class="fa fa-trash"></i></span>
                    <span class="layotter-element-button" ng-hide="element.is_template && !element.template_deleted" ng-click="editElement(element)" :title="'edit_element' | translate"><i class="fa fa-edit"></i></span>
                    <div class="layotter-element-dropdown">
                        <i class="fa fa-caret-down"></i>
                        <div class="layotter-element-dropdown-items">
                            <span ng-click="editOptions('element', element)" ng-show="optionsEnabled.element"><i class="fa fa-cog"></i>{{ 'element_options' | translate }}</span>
                            <span ng-click="duplicateElement(col.elements, $index)"><i class="fa fa-copy"></i>{{ 'duplicate_element' | translate }}</span>
                            <span ng-hide="element.is_template && !element.template_deleted" ng-click="saveNewTemplate(element)" ng-if="enableElementTemplates"><i class="fa fa-star"></i>{{ 'save_as_template' | translate }}</span>
                        </div>
                    </div>
                </div>
                <div class="layotter-element-message" ng-show="element.is_template && !element.template_deleted">
                    {{ 'this_is_a_template' | translate }}
                </div>
                <div class="layotter-element-content" ng-bind-html="element.view"></div>
            </div>
            <div class="layotter-col-buttons-wrapper">
                <span class="layotter-col-button" ng-click="showNewElementTypes(col.elements, $index)" :title="'add_element' | translate"><i class="fa fa-plus"></i></span>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';

export default Vue.extend({
});
</script>

<style lang="scss">
</style>