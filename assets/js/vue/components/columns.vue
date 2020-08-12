<template>
    <div class="layotter-cols">
        <div v-for="(col, colIndex) in columns" :class="['layotter-col', 'layotter-col-' + colIndex, { 'layotter-loading' : col.isLoading }]" :data-width="getColLayout(row, colIndex)">
            <div :class="['layotter-col-buttons-wrapper', { 'layotter-always-visible': col.elements.length === 0 }]">
                <span class="layotter-col-button" @click="showNewElementTypes(col.elements, -1)" :title="'add_element' | translate"><i class="fa fa-plus"></i><span>{{ 'add_element' | translate }}</span></span>
                <div class="layotter-breaker">
                    <span class="layotter-col-button" @click="editOptions('col', col)" v-show="configuration.colOptionsEnabled" :title="'column_options' | translate"><i class="fa fa-cog"></i><span>{{ 'column_options' | translate }}</span></span>
                </div>
            </div>

            <Elements
                :column="col"
                :configuration="configuration"></Elements>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';
import Elements from './elements.vue';
import {Column, Configuration, Element, Row} from '../interfaces/backendData';

export default Vue.extend({
    components: {
        Elements,
    },
    props: {
        columns: {
            type: Array as () => Array<Column>,
        },
        row: {
            type: Object as () => Row,
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
            console.log('showNewElementTypes');
        },
        editOptions(type: string, column: Column): void {
            console.log('editOptions', type, column);
        },
    }
});
</script>

<style lang="scss">
</style>