import Vue from 'vue'
import Editor from './components/editor.vue'
import {BackendData} from "./interfaces/backendData";

declare var layotterData: BackendData;

Vue.filter('translate', (id: string): string => {
    return layotterData.i18n[id] ?? id;
});

new Vue({
    el: '#layotter-container',
    components: {
        Editor,
    },
});