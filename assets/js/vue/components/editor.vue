<template>
    <div id="layotter" :class="{ 'layotter-loading' : isLoading }">
        <div class="layotter-top-buttons" v-for="num in [1,2]" :id="'layotter-top-buttons-' + num">
            <div class="layotter-top-buttons-left">
                <span class="layotter-button" @click="editOptions('post', contentStructure)" v-show="optionsEnabled.post"><i class="fa fa-cog"></i>{{ 'options' | translate }}</span>
                <span class="layotter-button layotter-undo" @click="undoStep()" :class="{ 'layotter-disabled' : !history.canUndo }" :title="history.undoTitle"><i class="fa fa-undo"></i></span>
                <span class="layotter-button layotter-redo" @click="redoStep()" :class="{ 'layotter-disabled' : !history.canRedo }" :title="history.redoTitle"><i class="fa fa-redo"></i></span>
                <div class="layotter-save-layout-button-wrapper" v-show="enablePostLayouts">
                    <span class="layotter-button" @click="saveNewLayout()"><i class="fa fa-download"></i>{{ 'save_layout' | translate }}</span>
                </div>
                <span class="layotter-button" @click="loadLayout()" v-show="enablePostLayouts && savedLayouts.length"><i class="fa fa-upload"></i>{{ 'load_layout' | translate }}</span>
            </div>
            <div class="layotter-top-buttons-right">
                <span class="layotter-button" @click="toggleTemplates()" v-show="enableElementTemplates && savedTemplates.length"><i class="fa fa-star"></i>{{ 'element_templates' | translate }}</span>
            </div>
        </div>

        <div class="layotter-get-started-buttons">
            <span class="layotter-add-row-button" @click="addRow(-1)" :class="{ 'layotter-large': contentStructure.rows.length === 0 }">
                <span v-show="contentStructure.rows.length"><i class="fa fa-plus"></i>{{ 'add_row' | translate }}</span>
                <span v-show="!contentStructure.rows.length"><i class="fa fa-plus"></i>{{ 'add_first_row' | translate }}</span>
            </span>
            <div class="layotter-breaker">
        <span class="layotter-load-layout-button" @click="loadLayout()" v-show="enablePostLayouts && savedLayouts.length && !contentStructure.rows.length" :class="{ 'layotter-hidden': contentStructure.rows.length !== 0 }">
            <i class="fa fa-upload"></i>{{ 'start_with_layout' | translate }}
        </span>
            </div>
        </div>

        <Rows
            :rows="contentStructure.rows"
            :allowed-layouts="allowedRowLayouts"
            :options-enabled="optionsEnabled"
            :enable-element-templates="enableElementTemplates"></Rows>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';
import Rows from './rows.vue';
import {BackendData, Dictionary, Element, IsOptionsEnabled, Layout, Post} from "../interfaces/backendData";

declare var layotterData: BackendData;

export default Vue.extend({
    components: {
        Rows,
    },
    data() {
        return {
            isLoading: false,
            contentStructure: {} as Post,
            allowedRowLayouts: [] as string[],
            optionsEnabled: {} as IsOptionsEnabled,
            enablePostLayouts: false,
            enableElementTemplates: false,
            history: {
                canUndo: false,
                undoTitle: '',
                canRedo: false,
                redoTitle: '',
            },
            savedLayouts: [] as Array<Layout>,
            savedTemplates: [] as Array<Element>,
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
    },
});
</script>

<style lang="scss">
</style>