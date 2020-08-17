import Vue from 'vue'
import Editor from './components/editor.vue'
import {IBackendData} from "./interfaces/IBackendData";

declare var layotterData: IBackendData;

Vue.filter('translate', (id: string): string => {
    return layotterData.i18n[id] ?? id;
});

new Vue({
    el: '#layotter-container',
    components: {
        Editor,
    },
});