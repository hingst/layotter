<template>
    <div :class="['layotter-col', 'layotter-col-' + index, { 'layotter-loading' : column.isLoading }]" :data-width="getColLayout(row, index)">
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
                       :animation="300">
                <Element
                    v-for="(element, elementIndex) in column.elements"
                    :key="elementIndex"
                    :element="element"
                    :index="elementIndex"
                    :column="column"
                    :configuration="configuration"></Element>
            </Draggable>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';
import Draggable from 'vuedraggable';
import Element from './element.vue';
import {Column, Configuration, Row} from '../interfaces/backendData';

export default Vue.extend({
    components: {
        Element,
        Draggable,
    },
    props: {
        column: {
            type: Object as () => Column,
        },
        index: {
            type: Number,
        },
        row: {
            type: Object as () => Row,
        },
        rowIndex: {
            type: Number,
        },
        configuration: {
            type: Object as () => Configuration,
        },
    },
    methods: {
        getColLayout(row: Row, index: number): string {
            console.log('getColLayout', row, index);
            return '1/3';
        },
        showNewElementTypes(elements: Array<Element>, index: number): void {
            console.log('showNewElementTypes', elements, index);
        },
        editOptions(type: string, column: Column): void {
            console.log('editOptions', type, column);
        },
    }
});
</script>

<style lang="scss">
</style>