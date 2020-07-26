<template>
    <div class="layotter-rows" ui-sortable="rowSortableOptions" ng-model="data.rows">
        <div class="layotter-row layotter-row-{ {$index} } layotter-animate" ng-repeat="row in data.rows" ng-class="{ 'layotter-loading' : row.isLoading }">
            <div class="layotter-row-canvas">
                <div class="layotter-row-move">
                    <i class="fa fa-arrows-alt-v"></i><?php _e('Move row', 'layotter'); ?>
                </div>
                <div class="layotter-row-buttons">
                    <span ng-click="deleteRow($index)" title="<?php _e('Delete row', 'layotter'); ?>"><i class="fa fa-trash"></i></span>
                    <span ng-click="duplicateRow($index)" title="<?php _e('Duplicate row', 'layotter'); ?>"><i class="fa fa-copy"></i></span>
                    <span ng-click="editOptions('row', row)" ng-show="optionsEnabled.row" title="<?php _e('Row options', 'layotter'); ?>"><i class="fa fa-cog"></i></span>
                    <div class="layotter-row-select-layout" ng-if="allowedRowLayouts.length > 1">
                        <i class="fa fa-columns"></i>
                        <div class="layotter-row-select-layout-items">
                    <span class="layotter-row-layout-button" ng-repeat="colbutton in allowedRowLayouts" ng-class="{ 'layotter-row-layout-button-active': colbutton === row.layout }" ng-click="setRowLayout(row, colbutton)">
                        <span ng-repeat="width in colbutton.split(' ') track by $index" data-width="{ { width } }"></span>
                    </span>
                        </div>
                    </div>
                </div>
                <Columns></Columns>
            </div>
            <div class="layotter-add-row-button-wrapper">
                <span class="layotter-add-row-button" ng-click="addRow($index)"><i class="fa fa-plus"></i><?php _e('Add row', 'layotter'); ?></span>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';
import Columns from './columns.vue';

export default Vue.extend({
    components: {
        Columns
    }
});
</script>

<style lang="scss">
</style>