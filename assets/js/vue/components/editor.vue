<template>
    <div id="layotter" :class="{ 'layotter-loading' : isLoading }">
        <div class="layotter-top-buttons" v-for="num in [1,2]" :id="'layotter-top-buttons-' + num">
            <div class="layotter-top-buttons-left">
                <span class="layotter-button" @click="editOptions('post', content)" v-show="configuration.postOptionsEnabled"><i class="fa fa-cog"></i>{{ 'options' | translate }}</span>
                <span class="layotter-button layotter-undo" @click="undoStep()" :class="{ 'layotter-disabled' : !$store.getters.canUndo }" :title="translate('undo') + ' ' + $store.getters.undoTitle"><i class="fa fa-undo"></i></span>
                <span class="layotter-button layotter-redo" @click="redoStep()" :class="{ 'layotter-disabled' : !$store.getters.canRedo }" :title="translate('redo') + ' ' + $store.getters.redoTitle"><i class="fa fa-redo"></i></span>
                <div class="layotter-save-layout-button-wrapper" v-show="configuration.postLayoutsEnabled">
                    <span class="layotter-button" @click="saveNewLayout()"><i class="fa fa-download"></i>{{ 'save_layout' | translate }}</span>
                </div>
                <span class="layotter-button" @click="loadLayout()" v-show="configuration.postLayoutsEnabled && configuration.savedLayouts.length"><i class="fa fa-upload"></i>{{ 'load_layout' | translate }}</span>
            </div>
            <div class="layotter-top-buttons-right">
                <span class="layotter-button" @click="toggleTemplates()" v-show="configuration.elementTemplatesEnabled && configuration.savedTemplates.length"><i class="fa fa-star"></i>{{ 'element_templates' | translate }}</span>
            </div>
        </div>

        <div class="layotter-get-started-buttons">
            <span class="layotter-add-row-button" @click="addRow(-1)" :class="{ 'layotter-large': $store.state.content.rows.length === 0 }">
                <span v-show="$store.state.content.rows.length"><i class="fa fa-plus"></i>{{ 'add_row' | translate }}</span>
                <span v-show="!$store.state.content.rows.length"><i class="fa fa-plus"></i>{{ 'add_first_row' | translate }}</span>
            </span>
            <div class="layotter-breaker">
        <span class="layotter-load-layout-button" @click="loadLayout()" v-show="configuration.postLayoutsEnabled && configuration.savedLayouts.length && !$store.state.content.rows.length" :class="{ 'layotter-hidden': $store.state.content.rows.length !== 0 }">
            <i class="fa fa-upload"></i>{{ 'start_with_layout' | translate }}
        </span>
            </div>
        </div>

        <div class="layotter-rows">
            <Draggable v-model="$store.state.content.rows"
                       group="rows"
                       :handle="'.layotter-row-move'"
                       :force-fallback="true"
                       :animation="300"
                       :direction="'vertical'"
                       @end="$store.dispatch('pushStep', translate('move_row'))">
                <Row
                    v-for="(row, rowIndex) in $store.state.content.rows"
                    :key="rowIndex"
                    :configuration="configuration"
                    :row="row"
                    :index="rowIndex"
                    :post="$store.state.content"
                    @addRow="addRow"></Row>
            </Draggable>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';
import Draggable from 'vuedraggable';
import Row from './row.vue';
import TranslationService from '../services/TranslationService';
import Util from '../util';
import TemplateService from '../services/TemplateService';
import {IBackendData, IConfiguration, IRow} from '../interfaces/IBackendData';

declare var layotterData: IBackendData;

export default Vue.extend({
    components: {
        Row,
        Draggable,
    },
    data() {
        return {
            configuration: {} as IConfiguration,
            isLoading: false,
        };
    },
    created() {
        this.$store.state.content = layotterData.content;
        this.configuration = layotterData.configuration;
    },
    mounted() {
        this.$store.dispatch('pushStep', '');
    },
    methods: {
        editOptions(type: string, item: object): void {
            console.log('editOptions', type, item);
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
            this.$store.state.content.rows.splice(afterIndex + 1, 0, Util.clone(TemplateService.getRowTemplate()));
            this.$store.dispatch('pushStep', TranslationService.translate('add_row'));
        },
        undoStep(): void {
            if (this.$store.getters.canUndo) {
                this.restoreStep(this.$store.state.history.currentStep - 1);
            }
        },
        redoStep(): void {
            if (this.$store.getters.canRedo) {
                this.restoreStep(this.$store.state.history.currentStep + 1);
            }
        },
        restoreStep(index: number): void {
            let restore = Util.clone(this.$store.state.history.steps[index].content);

            restore.rows.forEach((row: IRow) => {
                row.cols.forEach((column) => {
                    column.elements.forEach((element) => {
                        if (element.is_template && !element.template_deleted) {
                            if (this.$store.state.history.deletedTemplates.indexOf(element.id) !== -1) {
                                element.is_template = false;
                                element.template_deleted = true;
                            }
                        }
                    });
                });
            });

            this.$store.state.history.currentStep = index;
            this.$store.state.content.options_id = restore.options_id;
            this.$store.state.content.rows = restore.rows;
        },
    },
});
</script>

<style lang="scss">
</style>