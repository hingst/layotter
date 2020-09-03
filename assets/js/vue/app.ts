import Vue from 'vue'
import store from './store'
import Editor from './components/editor.vue'
import TranslationService from './services/TranslationService';
import mixins from './mixins';

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
});