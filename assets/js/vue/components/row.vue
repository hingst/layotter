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
                        <span v-for="layout in configuration.allowedRowLayouts" :class="['layotter-row-layout-button', { 'layotter-row-layout-button-active': layout === row.layout }]" @click="setRowLayout(row, layout)">
                            <span v-for="width in layout.split(' ')" :data-width="width"></span>
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
                        :rowIndex="index"
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
import {IConfiguration, IPost, IRow, ITemplates} from '../interfaces/IBackendData';

export default Vue.extend({
    components: {
        Column
    },
    props: {
        row: {
            type: Object as () => IRow,
        },
        index: {
            type: Number,
        },
        post: {
            type: Object as () => IPost,
        },
        configuration: {
            type: Object as () => IConfiguration,
        },
        templates: {
            type: Object as () => ITemplates,
        },
    },
    methods: {
        addRow(index: number): void {
            this.$emit('addRow', index);
        },
        deleteRow(index: number): void {
            let hasElements = false;

            this.post.rows[index].cols.forEach((column) => {
                if (column.elements.length) {
                    hasElements = true;
                }
            });

            if (!hasElements) {
                this.post.rows.splice(index, 1);
                return;
            }

            if (confirm('DELETE ROW?')) {
                this.post.rows.splice(index, 1);
            }
        },
        duplicateRow(index: number): void {
            this.post.rows.splice(index, 0, JSON.parse(JSON.stringify(this.post.rows[index])));
        },
        editOptions(type: string, row: object): void {
            console.log('editOptions', type, row);
        },
        setRowLayout(row: IRow, layout: string): void {
            const oldColCount = this.row.layout.split(' ').length;
            const newColCount = layout.split(' ').length;
            row.layout = layout;

            if (newColCount > oldColCount) {
                for (let i = oldColCount; i < newColCount; i++) {
                    row.cols.push(JSON.parse(JSON.stringify(this.templates.column)));
                }
            } else {
                for (let i = newColCount; i < oldColCount; i++) {
                    row.cols[i].elements.forEach((element) => {
                        row.cols[newColCount - 1].elements.push(element);
                    });
                }
                row.cols.splice(newColCount);
            }
        },
    }
});
</script>

<style lang="scss">
</style>