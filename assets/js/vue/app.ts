import Vue from 'vue'
import store from './store'
import Editor from './components/editor.vue'
import {IBackendData} from './interfaces/IBackendData';

declare var layotterData: IBackendData;

Vue.filter('translate', (id: string): string => {
    return layotterData.i18n[id] ?? id;
});

new Vue({
    el: '#layotter-container',
    components: {
        Editor,
    },
    store: store,
    created(): void {
        this.$store.state.content = layotterData.content;
        this.$store.state.postInfo = layotterData.postInfo;
        this.$store.state.configuration = layotterData.configuration;
        this.$store.state.savedLayouts = layotterData.savedLayouts;
        this.$store.state.savedTemplates = layotterData.savedTemplates;
        this.$store.state.elementTypes = layotterData.elementTypes;
        this.$store.state.i18n = layotterData.i18n;

        this.$store.state.componentTemplates.element = {
            id: 0,
            view: '',
            options_id: 0,
            is_template: false,
        };

        this.$store.state.componentTemplates.column = {
            elements: [],
            options_id: 0,
            width: '',
        };

        const defaultColumnCount = this.$store.state.configuration.defaultRowLayout.split(' ').length;
        const defaultColumns = [];
        for (let i = 0; i < defaultColumnCount; i++) {
            defaultColumns.push(JSON.parse(JSON.stringify(this.$store.state.componentTemplates.column)));
        }

        this.$store.state.componentTemplates.row = {
            layout: this.$store.state.configuration.defaultRowLayout,
            cols: defaultColumns,
            options_id: 0,
        };
    },
});