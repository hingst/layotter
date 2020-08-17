<template>
    <div id="layotter" :class="{ 'layotter-loading' : isLoading }">
        <div class="layotter-top-buttons" v-for="num in [1,2]" :id="'layotter-top-buttons-' + num">
            <div class="layotter-top-buttons-left">
                <span class="layotter-button" @click="editOptions('post', content)" v-show="configuration.postOptionsEnabled"><i class="fa fa-cog"></i>{{ 'options' | translate }}</span>
                <span class="layotter-button layotter-undo" @click="undoStep()" :class="{ 'layotter-disabled' : !history.canUndo }" :title="history.undoTitle"><i class="fa fa-undo"></i></span>
                <span class="layotter-button layotter-redo" @click="redoStep()" :class="{ 'layotter-disabled' : !history.canRedo }" :title="history.redoTitle"><i class="fa fa-redo"></i></span>
                <div class="layotter-save-layout-button-wrapper" v-show="configuration.postLayoutsEnabled">
                    <span class="layotter-button" @click="saveNewLayout()"><i class="fa fa-download"></i>{{ 'save_layout' | translate }}</span>
                </div>
                <span class="layotter-button" @click="loadLayout()" v-show="configuration.postLayoutsEnabled && savedLayouts.length"><i class="fa fa-upload"></i>{{ 'load_layout' | translate }}</span>
            </div>
            <div class="layotter-top-buttons-right">
                <span class="layotter-button" @click="toggleTemplates()" v-show="configuration.elementTemplatesEnabled && savedTemplates.length"><i class="fa fa-star"></i>{{ 'element_templates' | translate }}</span>
            </div>
        </div>

        <div class="layotter-get-started-buttons">
            <span class="layotter-add-row-button" @click="addRow(-1)" :class="{ 'layotter-large': content.rows.length === 0 }">
                <span v-show="content.rows.length"><i class="fa fa-plus"></i>{{ 'add_row' | translate }}</span>
                <span v-show="!content.rows.length"><i class="fa fa-plus"></i>{{ 'add_first_row' | translate }}</span>
            </span>
            <div class="layotter-breaker">
        <span class="layotter-load-layout-button" @click="loadLayout()" v-show="configuration.postLayoutsEnabled && savedLayouts.length && !content.rows.length" :class="{ 'layotter-hidden': content.rows.length !== 0 }">
            <i class="fa fa-upload"></i>{{ 'start_with_layout' | translate }}
        </span>
            </div>
        </div>

        <div class="layotter-rows">
            <Draggable v-model="content.rows"
                       group="rows"
                       :handle="'.layotter-row-move'"
                       :force-fallback="true"
                       :animation="300"
                       :direction="'vertical'">
                <Row
                    v-for="(row, rowIndex) in content.rows"
                    :key="rowIndex"
                    :row="row"
                    :index="rowIndex"
                    :configuration="configuration"
                    :templates="templates"
                    @addRow="addRow"
                    @deleteRow="deleteRow"></Row>
            </Draggable>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';
import Draggable from 'vuedraggable';
import Row from './row.vue';
import {
    IBackendData, IColumn,
    IConfiguration,
    IElement,
    IElementType,
    ILayout,
    IPost,
    IPostData,
    IRow
} from '../interfaces/IBackendData';

declare var layotterData: IBackendData;

export default Vue.extend({
    components: {
        Row,
        Draggable,
    },
    data() {
        return {
            isLoading: false,
            content: {} as IPost,
            postData: {} as IPostData,
            configuration: {} as IConfiguration,
            savedLayouts: [] as Array<ILayout>,
            savedTemplates: [] as Array<IElement>,
            elementTypes: [] as Array<IElementType>,
            history: {
                canUndo: false,
                undoTitle: '',
                canRedo: false,
                redoTitle: '',
            },
            templates: {
                row: {} as IRow,
                column: {} as IColumn,
                element: {} as IElement,
            },
        }
    },
    created(): void {
        this.content = layotterData.content;
        this.postData = layotterData.postData;
        this.configuration = layotterData.configuration;
        this.savedLayouts = layotterData.savedLayouts;
        this.savedTemplates = layotterData.savedTemplates;
        this.elementTypes = layotterData.elementTypes;

        this.templates.element = {
            id: 0,
            view: '',
            options_id: 0,
            is_template: false,
        };

        this.templates.column = {
            elements: [],
            options_id: 0,
            width: '',
        };

        const defaultColumnCount = this.configuration.defaultRowLayout.split(' ').length;
        const defaultColumns = [];
        for (let i = 0; i < defaultColumnCount; i++) {
            defaultColumns.push(JSON.parse(JSON.stringify(this.templates.column)));
        }

        this.templates.row = {
            layout: this.configuration.defaultRowLayout,
            cols: defaultColumns,
            options_id: 0,
        };
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
            this.content.rows.splice(afterIndex + 1, 0, JSON.parse(JSON.stringify(this.templates.row)));
        },
        deleteRow(index: number): void {
            let hasElements = false;

            this.content.rows[index].cols.forEach((column) => {
                if (column.elements.length) {
                    hasElements = true;
                }
            });

            if (!hasElements) {
                this.content.rows.splice(index, 1);
                return;
            }

            if (confirm('DELETE ROW?')) {
                this.content.rows.splice(index, 1);
            }
        },
    },
});
</script>

<style lang="scss">
</style>