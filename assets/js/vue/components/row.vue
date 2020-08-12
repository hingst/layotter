<template>
    <div :class="['layotter-row', 'layotter-animate', 'layotter-row-' + index, { 'layotter-loading' : row.isLoading }]">
        <div class="layotter-row-canvas">
            <div class="layotter-row-move">
                <i class="fa fa-arrows-alt-v"></i>{{ 'move_row' | translate }}
            </div>
            <div class="layotter-row-buttons">
                <span @click="deleteRow(index)" :title="'delete_row' | translate"><i class="fa fa-trash"></i></span>
                <span @click="duplicateRow(index)" :title="'duplicate_row' | translate"><i class="fa fa-copy"></i></span>
                <span @click="editOptions('row', row)" v-show="configuration.rowOptionsEnabled" :title="'row_options' | translate"><i class="fa fa-cog"></i></span>
                <div class="layotter-row-select-layout" v-show="configuration.allowedRowLayouts.length > 1">
                    <i class="fa fa-columns"></i>
                    <div class="layotter-row-select-layout-items">
                        <span v-for="colbutton in configuration.allowedRowLayouts" :class="['layotter-row-layout-button', { 'layotter-row-layout-button-active': colbutton === row.layout }]" @click="setRowLayout(row, colbutton)">
                            <span v-for="width in colbutton.split(' ')" :data-width="width"></span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="layotter-cols">
                <template v-for="(column, columnIndex) in row.cols">
                    <Column
                        :column="column"
                        :index="columnIndex"
                        :row="row"
                        :configuration="configuration"></Column>
                </template>
            </div>
        </div>
        <div class="layotter-add-row-button-wrapper">
            <span class="layotter-add-row-button" @click="addRow(index)"><i class="fa fa-plus"></i>{{ 'add_row' | translate }}</span>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';
import Column from './column.vue';
import {Configuration, Row} from '../interfaces/backendData';

export default Vue.extend({
    components: {
        Column
    },
    props: {
        row: {
            type: Object as () => Row,
        },
        index: {
            type: Number,
        },
        configuration: {
            type: Object as () => Configuration,
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