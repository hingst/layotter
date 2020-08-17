<template>
    <div id="layotter" :class="{ 'layotter-loading' : $store.state.isLoading }">
        <div class="layotter-top-buttons" v-for="num in [1,2]" :id="'layotter-top-buttons-' + num">
            <div class="layotter-top-buttons-left">
                <span class="layotter-button" @click="editOptions('post', content)" v-show="$store.state.configuration.postOptionsEnabled"><i class="fa fa-cog"></i>{{ 'options' | translate }}</span>
                <span class="layotter-button layotter-undo" @click="undoStep()" :class="{ 'layotter-disabled' : !canUndo() }" :title="$store.state.history.undoTitle"><i class="fa fa-undo"></i></span>
                <span class="layotter-button layotter-redo" @click="redoStep()" :class="{ 'layotter-disabled' : !canRedo() }" :title="$store.state.history.redoTitle"><i class="fa fa-redo"></i></span>
                <div class="layotter-save-layout-button-wrapper" v-show="$store.state.configuration.postLayoutsEnabled">
                    <span class="layotter-button" @click="saveNewLayout()"><i class="fa fa-download"></i>{{ 'save_layout' | translate }}</span>
                </div>
                <span class="layotter-button" @click="loadLayout()" v-show="$store.state.configuration.postLayoutsEnabled && $store.state.savedLayouts.length"><i class="fa fa-upload"></i>{{ 'load_layout' | translate }}</span>
            </div>
            <div class="layotter-top-buttons-right">
                <span class="layotter-button" @click="toggleTemplates()" v-show="$store.state.configuration.elementTemplatesEnabled && $store.state.savedTemplates.length"><i class="fa fa-star"></i>{{ 'element_templates' | translate }}</span>
            </div>
        </div>

        <div class="layotter-get-started-buttons">
            <span class="layotter-add-row-button" @click="addRow(-1)" :class="{ 'layotter-large': $store.state.content.rows.length === 0 }">
                <span v-show="$store.state.content.rows.length"><i class="fa fa-plus"></i>{{ 'add_row' | translate }}</span>
                <span v-show="!$store.state.content.rows.length"><i class="fa fa-plus"></i>{{ 'add_first_row' | translate }}</span>
            </span>
            <div class="layotter-breaker">
        <span class="layotter-load-layout-button" @click="loadLayout()" v-show="$store.state.configuration.postLayoutsEnabled && $store.state.savedLayouts.length && !$store.state.content.rows.length" :class="{ 'layotter-hidden': $store.state.content.rows.length !== 0 }">
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
                       @end="pushStep($store.state.i18n.move_row)">
                <Row
                    v-for="(row, rowIndex) in $store.state.content.rows"
                    :key="rowIndex"
                    :row="row"
                    :index="rowIndex"
                    :post="$store.state.content"
                    @addRow="addRow"
                    @pushStep="pushStep"></Row>
            </Draggable>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';
import Draggable from 'vuedraggable';
import Row from './row.vue';
import {IPost, IRow} from '../interfaces/IBackendData';

export default Vue.extend({
    components: {
        Row,
        Draggable,
    },
    mounted() {
        this.pushStep('');
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
            this.$store.state.content.rows.splice(afterIndex + 1, 0, JSON.parse(JSON.stringify(this.$store.state.templates.row)));
            this.pushStep(this.$store.state.i18n.add_row);
        },
        updateData(): void {
            this.$store.state.history.canUndo = this.canUndo();
            this.$store.state.history.canRedo = this.canRedo();

            if (this.$store.state.history.canUndo) {
                this.$store.state.history.undoTitle = this.$store.state.i18n.undo + ' ' + this.$store.state.history.steps[this.$store.state.history.currentStep].title;
            } else {
                this.$store.state.history.undoTitle = '';
            }

            if (this.$store.state.history.canRedo) {
                this.$store.state.history.redoTitle = this.$store.state.i18n.redo + ' ' + this.$store.state.history.steps[this.$store.state.history.currentStep + 1].title;
            } else {
                this.$store.state.history.redoTitle = '';
            }
        },
        refreshTemplates(content: IPost): void {
            const contentClone = JSON.parse(JSON.stringify(content));

            contentClone.rows.forEach((row: IRow) => {
                row.cols.forEach((column) => {
                    column.elements.forEach((element) => {
                        if (element.is_template && !element.template_deleted) {
                            if (this.$store.state.deletedTemplates.indexOf(element.id) !== -1) {
                                element.is_template = false;
                                element.template_deleted = true;
                            }
                        }
                    });
                });
            });
            return contentClone;
        },
        canUndo(): boolean {
            return (this.$store.state.history.currentStep > 0);
        },
        canRedo(): boolean {
            return (this.$store.state.history.currentStep < this.$store.state.history.steps.length - 1);
        },
        pushStep(title: string): void {
            // remove all steps that have previously been undone
            if (this.canRedo()) {
                this.$store.state.history.steps.splice(this.$store.state.history.currentStep + 1, this.$store.state.history.steps.length);
            }

            this.$store.state.history.steps.push({
                title : title,
                content: JSON.parse(JSON.stringify(this.$store.state.content)),
            });
            this.$store.state.history.currentStep++;
            this.updateData();
        },
        undoStep(): void {
            if (this.canUndo()) {
                this.$store.state.history.currentStep--;
                let restore = JSON.parse(JSON.stringify(this.$store.state.history.steps[this.$store.state.history.currentStep].content));
                restore = this.refreshTemplates(restore);
                this.$store.state.content.options_id = restore.options_id;
                this.$store.state.content.rows = restore.rows;
                this.updateData();
            }
        },
        redoStep(): void {
            if (this.canRedo()) {
                this.$store.state.history.currentStep++;
                let restore = JSON.parse(JSON.stringify(this.$store.state.history.steps[this.$store.state.history.currentStep].content));
                restore = this.refreshTemplates(restore);
                this.$store.state.content.options_id = restore.options_id;
                this.$store.state.content.rows = restore.rows;
                this.updateData();
            }
        },
    },
});
</script>

<style lang="scss">
</style>