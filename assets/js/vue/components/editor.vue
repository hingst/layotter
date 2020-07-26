<template>
    <div id="layotter" :class="{ 'layotter-loading' : isLoading }">
        <div class="layotter-top-buttons" v-for="num in [1,2]" :id="'layotter-top-buttons-' + num">
            <div class="layotter-top-buttons-left">
                <span class="layotter-button" @click="editOptions('post', contentStructure)" v-show="optionsEnabled.post"><i class="fa fa-cog"></i><?php _e('Options', 'layotter'); ?></span>
                <span class="layotter-button layotter-undo" @click="undoStep()" :class="{ 'layotter-disabled' : !history.canUndo }" :title="history.undoTitle"><i class="fa fa-undo"></i></span>
                <span class="layotter-button layotter-redo" @click="redoStep()" :class="{ 'layotter-disabled' : !history.canRedo }" :title="history.redoTitle"><i class="fa fa-redo"></i></span>
                <div class="layotter-save-layout-button-wrapper" v-show="enablePostLayouts">
                    <span class="layotter-button" @click="saveNewLayout()"><i class="fa fa-download"></i><?php _e('Save layout', 'layotter'); ?></span>
                </div>
                <span class="layotter-button" @click="loadLayout()" v-show="enablePostLayouts && savedLayouts.length"><i class="fa fa-upload"></i><?php _e('Load layout', 'layotter'); ?></span>
            </div>
            <div class="layotter-top-buttons-right">
                <span class="layotter-button" @click="toggleTemplates()" v-show="enableElementTemplates && savedTemplates.length"><i class="fa fa-star"></i><?php _e('Element templates', 'layotter'); ?></span>
            </div>
        </div>

        <div class="layotter-get-started-buttons">
            <span class="layotter-add-row-button" @click="addRow(-1)" :class="{ 'layotter-large': contentStructure.rows.length === 0 }">
                <span v-show="contentStructure.rows.length"><i class="fa fa-plus"></i><?php _e('Add row', 'layotter'); ?></span>
                <span v-show="!contentStructure.rows.length"><i class="fa fa-plus"></i><?php _e('Add your first row to get started', 'layotter'); ?></span>
            </span>
            <div class="layotter-breaker">
        <span class="layotter-load-layout-button" @click="loadLayout()" v-show="enablePostLayouts && savedLayouts.length && !contentStructure.rows.length" :class="{ 'layotter-hidden': contentStructure.rows.length !== 0 }">
            <i class="fa fa-upload"></i><?php _e('Or start with a layout that you created earlier', 'layotter'); ?>
        </span>
            </div>
        </div>

        <Rows></Rows>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';
import Rows from './rows.vue';
import {LayotterData} from "../interfaces/layotterData";

declare var layotterData: LayotterData;

export default Vue.extend({
    components: {
        Rows,
    },
    data() {
        return {
            isLoading: false,
            contentStructure: {},
            allowedRowLayouts: [] as object[],
            optionsEnabled: {},
            enablePostLayouts: false,
            enableElementTemplates: false,
            history: {
                canUndo: false,
                undoTitle: '',
                canRedo: false,
                redoTitle: '',
            },
            savedLayouts: [] as object[],
            savedTemplates: [] as object[],
        }
    },
    created(): void {
        this.contentStructure = layotterData.contentStructure;
        this.allowedRowLayouts = layotterData.allowedRowLayouts;
        this.optionsEnabled = {
            post: layotterData.isOptionsEnabled.post,
            row: layotterData.isOptionsEnabled.row,
            col: layotterData.isOptionsEnabled.col,
            element: layotterData.isOptionsEnabled.element,
        };
        this.enablePostLayouts = layotterData.enablePostLayouts;
        this.enableElementTemplates = layotterData.enableElementTemplates;
    },
    methods: {
        editOptions(type: string, item: object): void {
            console.log('editOptions', type, item);
        },
        undoStep(): void {
            console.log('undoStep');
        },
        redoStep(): void {
            console.log('redoStep');
        },
        saveNewLayout(): void {
            console.log('saveNewLayout');
        },
        loadLayout(): void {
            console.log('loadLayout');
        },
        toggleTemplates(): void {
            console.log('toggleTemplates');
        },
        addRow(afterIndex: number): void {
            console.log('addRow', afterIndex);
        },
    }
});
</script>

<style lang="scss">
</style>