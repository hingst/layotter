import Vue from 'vue'
import store from './store'
import Editor from './components/editor.vue'
import {IBackendData} from './interfaces/IBackendData';
import TranslationService from './services/TranslationService';
import mixins from './mixins';

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
    },
});