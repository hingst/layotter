<template>
    <div :class="['layotter-col', 'layotter-col-' + index, { 'layotter-loading' : isLoading }]" :data-width="getColLayout(row, index)">
        <div :class="['layotter-col-buttons-wrapper', { 'layotter-always-visible': column.elements.length === 0 }]">
            <span class="layotter-col-button" @click="showNewElementTypes(column.elements, -1)" :title="'add_element' | translate"><i class="fa fa-plus"></i><span>{{ 'add_element' | translate }}</span></span>
            <div class="layotter-breaker">
                <span class="layotter-col-button" @click="editOptions('col', column)" v-show="configuration.colOptionsEnabled" :title="'column_options' | translate"><i class="fa fa-cog"></i><span>{{ 'column_options' | translate }}</span></span>
            </div>
        </div>

        <div class="layotter-elements" ui-sortable="elementSortableOptions" ng-model="column.elements">
            <Draggable v-model="column.elements"
                       group="elements"
                       :filter="'.layotter-element-buttons'"
                       :force-fallback="true"
                       :animation="300"
                       @end="$store.dispatch('pushStep', translate('move_element'))">
                <Element
                    v-for="(element, elementIndex) in column.elements"
                    :configuration="configuration"
                    :key="elementIndex"
                    :element="element"
                    :index="elementIndex"
                    :column="column"></Element>
            </Draggable>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';
import Draggable from 'vuedraggable';
import Element from './element.vue';
import {IColumn, IConfiguration, IRow} from '../interfaces/IBackendData';

export default Vue.extend({
    components: {
        Element,
        Draggable,
    },
    props: {
        configuration: {
            type: Object as () => IConfiguration,
        },
        column: {
            type: Object as () => IColumn,
        },
        index: {
            type: Number,
        },
        row: {
            type: Object as () => IRow,
        },
        rowIndex: {
            type: Number,
        },
    },
    data() {
        return {
            isLoading: false,
        };
    },
    methods: {
        getColLayout(row: IRow, index: number): string {
            return row.layout.split(' ')[index];
        },
        showNewElementTypes(elements: Array<Element>, index: number): void {
            console.log('showNewElementTypes', elements, index);
        },
        editOptions(type: string, column: IColumn): void {
            console.log('editOptions', type, column);
        },
    }
});
</script>

<style lang="scss">
</style>