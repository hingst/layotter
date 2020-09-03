import Vue from 'vue'
import store from './store'
import Editor from './components/editor.vue'
import {IBackendData} from './interfaces/IBackendData';
import TranslationService from './services/TranslationService';
import mixins from './mixins';
import Util from './util';

declare var layotterData: IBackendData;

Vue.filter('translate', (key: string): string => {
    return TranslationService.translate(key);
});

Vue.mixin(mixins);

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
        this.$store.state.availableElementTypes = layotterData.availableElementTypes;

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
            defaultColumns.push(Util.clone(this.$store.state.componentTemplates.column));
        }

        this.$store.state.componentTemplates.row = {
            layout: this.$store.state.configuration.defaultRowLayout,
            cols: defaultColumns,
            options_id: 0,
        };
    },
});