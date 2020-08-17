<template>
    <div id="layotter" :class="{ 'layotter-loading' : $store.state.isLoading }">
        <div class="layotter-top-buttons" v-for="num in [1,2]" :id="'layotter-top-buttons-' + num">
            <div class="layotter-top-buttons-left">
                <span class="layotter-button" @click="editOptions('post', content)" v-show="$store.state.configuration.postOptionsEnabled"><i class="fa fa-cog"></i>{{ 'options' | translate }}</span>
                <span class="layotter-button layotter-undo" @click="$store.dispatch('undoStep')" :class="{ 'layotter-disabled' : !$store.getters.canUndo }" :title="$store.getters.undoTitle"><i class="fa fa-undo"></i></span>
                <span class="layotter-button layotter-redo" @click="$store.dispatch('redoStep')" :class="{ 'layotter-disabled' : !$store.getters.canRedo }" :title="$store.getters.redoTitle"><i class="fa fa-redo"></i></span>
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
                       @end="$store.dispatch('pushStep', $store.state.i18n.move_row)">
                <Row
                    v-for="(row, rowIndex) in $store.state.content.rows"
                    :key="rowIndex"
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
import {IPost, IRow} from '../interfaces/IBackendData';

export default Vue.extend({
    components: {
        Row,
        Draggable,
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
            this.$store.state.content.rows.splice(afterIndex + 1, 0, JSON.parse(JSON.stringify(this.$store.state.templates.row)));
            this.$store.dispatch('pushStep', this.$store.state.i18n.add_row);
        },
    },
});
</script>

<style lang="scss">
</style>