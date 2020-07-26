<template>
    <div class="layotter-rows" ui-sortable="rowSortableOptions" ng-model="data.rows">
        <div v-for="(row, rowIndex) in rows" :class="['layotter-row', 'layotter-animate', 'layotter-row-' + rowIndex, { 'layotter-loading' : row.isLoading }]">
            <div class="layotter-row-canvas">
                <div class="layotter-row-move">
                    <i class="fa fa-arrows-alt-v"></i>{{ 'move_row' | translate }}
                </div>
                <div class="layotter-row-buttons">
                    <span @click="deleteRow(rowIndex)" :title="'delete_row' | translate"><i class="fa fa-trash"></i></span>
                    <span @click="duplicateRow(rowIndex)" :title="'duplicate_row' | translate"><i class="fa fa-copy"></i></span>
                    <span @click="editOptions('row', row)" v-show="optionsEnabled.rows" :title="'row_options' | translate"><i class="fa fa-cog"></i></span>
                    <div class="layotter-row-select-layout" v-show="allowedLayouts.length > 1">
                        <i class="fa fa-columns"></i>
                        <div class="layotter-row-select-layout-items">
                            <span v-for="colbutton in allowedLayouts" :class="['layotter-row-layout-button', { 'layotter-row-layout-button-active': colbutton === row.layout }]" @click="setRowLayout(row, colbutton)">
                                <span v-for="width in colbutton.split(' ')" :data-width="width"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <Columns
                    :columns="row.cols"
                    :options-enabled="optionsEnabled"
                    :row="row"
                    :enable-element-templates="enableElementTemplates"></Columns>
            </div>
            <div class="layotter-add-row-button-wrapper">
                <span class="layotter-add-row-button" @click="addRow(rowIndex)"><i class="fa fa-plus"></i>{{ 'add_row' | translate }}</span>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';
import Columns from './columns.vue';
import {IsOptionsEnabled, Row} from "../interfaces/backendData";

export default Vue.extend({
    components: {
        Columns
    },
    props: {
        enableElementTemplates: {
            type: Boolean,
        },
        rows: {
            type: Array as () => Array<Row>,
        },
        optionsEnabled: {
            type: Object as () => IsOptionsEnabled,
        },
        allowedLayouts: {
            type: Array as () => Array<string>,
        },
    },
    methods: {
        addRow(index: number): void {
            console.log('addRow', index);
        },
        deleteRow(index: number): void {
            console.log('deleteRow', index);
        },
        duplicateRow(index: number): void {
            console.log('duplicateRow', index);
        },
        editOptions(type: string, row: object): void {
            // EMIT EVENT
            console.log('editOptions', type, row);
        },
        setRowLayout(row: object, colButton: object): void {
            console.log('setRowLayout', row, colButton);
        },
    }
});
</script>

<style lang="scss">
</style>