<template>
    <div class="layotter-cols">
        <div class="layotter-col layotter-col-{ {$index} }" ng-class="{ 'layotter-loading' : col.isLoading }" ng-repeat="col in row.cols" data-width="{ { getColLayout(row, $index) } }">
            <div class="layotter-col-buttons-wrapper" ng-class="{ 'layotter-always-visible': col.elements.length === 0 }">
                <span class="layotter-col-button" ng-click="showNewElementTypes(col.elements, -1)" :title="'add_element' | translate"><i class="fa fa-plus"></i><span>{{ 'add_element' | translate }}</span></span>
                <div class="layotter-breaker">
                    <span class="layotter-col-button" ng-click="editOptions('col', col)" ng-show="optionsEnabled.col" :title="'column_options' | translate"><i class="fa fa-cog"></i><span>{{ 'column_options' | translate }}</span></span>
                </div>
            </div>

            <Elements></Elements>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';
import Elements from './elements.vue';

export default Vue.extend({
    components: {
        Elements,
    }
});
</script>

<style lang="scss">
</style>