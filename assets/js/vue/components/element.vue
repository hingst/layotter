<template>
    <div :class="['layotter-element', 'layotter-animate', 'layotter-element-' + index, { 'layotter-loading' : element.isLoading, 'layotter-highlight' : element.isHighlighted }]" :data-id="element.id">
        <div class="layotter-element-canvas">
            <div class="layotter-element-buttons">
                <span class="layotter-element-button" @click="deleteElement(column.elements, index)" :title="'delete_element' | translate"><i class="fa fa-trash"></i></span>
                <span class="layotter-element-button" v-show="!element.is_template || element.template_deleted" @click="editElement(element)" :title="'edit_element' | translate"><i class="fa fa-edit"></i></span>
                <div class="layotter-element-dropdown">
                    <i class="fa fa-caret-down"></i>
                    <div class="layotter-element-dropdown-items">
                        <span @click="editOptions('element', element)" v-show="configuration.elementOptionsEnabled"><i class="fa fa-cog"></i>{{ 'element_options' | translate }}</span>
                        <span @click="duplicateElement(column.elements, index)"><i class="fa fa-copy"></i>{{ 'duplicate_element' | translate }}</span>
                        <span v-show="configuration.elementTemplatesEnabled && !element.is_template || element.template_deleted" @click="saveNewTemplate(element)"><i class="fa fa-star"></i>{{ 'save_as_template' | translate }}</span>
                    </div>
                </div>
            </div>
            <div class="layotter-element-message" v-show="element.is_template && !element.template_deleted">
                {{ 'this_is_a_template' | translate }}
            </div>
            <div class="layotter-element-content" v-html="element.view"></div>
        </div>
        <div class="layotter-col-buttons-wrapper">
            <span class="layotter-col-button" @click="showNewElementTypes(column.elements, index)" :title="'add_element' | translate"><i class="fa fa-plus"></i></span>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';
import {Column, Configuration, Element} from '../interfaces/backendData';

export default Vue.extend({
    props: {
        element: {
            type: Object as () => Element,
        },
        index: {
            type: Number,
        },
        column: {
            type: Object as () => Column,
        },
        configuration: {
            type: Object as () => Configuration,
        },
    },
    methods: {
        deleteElement(elements: Array<Element>, index: number): void {
            console.log('deleteElement', elements, index);
        },
        duplicateElement(elements: Array<Element>, index: number): void {
            console.log('duplicateElement', elements, index);
        },
        showNewElementTypes(elements: Array<Element>, index: number): void {
            console.log('showNewElementTypes', elements, index);
        },
        editElement(element: Element): void {
            console.log('editElement', element);
        },
        editOptions(type: string, element: Element): void {
            console.log('editOptions', type, element);
        },
        saveNewTemplate(element: Element): void {
            console.log('saveNewTemplate', element);
        },
    },
});
</script>

<style lang="scss">
</style>