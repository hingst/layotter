import Vue from 'vue'
import Vuex from 'vuex'
import Editor from './components/editor.vue'
import {
    IBackendData, IColumn,
    IConfiguration, IDictionary,
    IElement,
    IElementType,
    ILayout,
    IPost,
    IPostData, IRow
} from "./interfaces/IBackendData";

declare var layotterData: IBackendData;

Vue.use(Vuex);

Vue.filter('translate', (id: string): string => {
    return layotterData.i18n[id] ?? id;
});

const store = new Vuex.Store({
    state: {
        isLoading: false,
        content: {} as IPost,
        postData: {} as IPostData,
        configuration: {} as IConfiguration,
        savedLayouts: [] as Array<ILayout>,
        savedTemplates: [] as Array<IElement>,
        elementTypes: [] as Array<IElementType>,
        history: {
            canUndo: false,
            undoTitle: '',
            canRedo: false,
            redoTitle: '',
            steps: [] as Array<IPost>,
            deletedTemplates: [],
            currentStep: -1,
        },
        templates: {
            row: {} as IRow,
            column: {} as IColumn,
            element: {} as IElement,
        },
        i18n: {} as IDictionary,
    },
});

new Vue({
    el: '#layotter-container',
    components: {
        Editor,
    },
    store: store,
    created(): void {
        this.$store.state.content = layotterData.content;
        this.$store.state.postData = layotterData.postData;
        this.$store.state.configuration = layotterData.configuration;
        this.$store.state.savedLayouts = layotterData.savedLayouts;
        this.$store.state.savedTemplates = layotterData.savedTemplates;
        this.$store.state.elementTypes = layotterData.elementTypes;
        this.$store.state.i18n = layotterData.i18n;

        this.$store.state.templates.element = {
            id: 0,
            view: '',
            options_id: 0,
            is_template: false,
        };

        this.$store.state.templates.column = {
            elements: [],
            options_id: 0,
            width: '',
        };

        const defaultColumnCount = this.$store.state.configuration.defaultRowLayout.split(' ').length;
        const defaultColumns = [];
        for (let i = 0; i < defaultColumnCount; i++) {
            defaultColumns.push(JSON.parse(JSON.stringify(this.$store.state.templates.column)));
        }

        this.$store.state.templates.row = {
            layout: this.$store.state.configuration.defaultRowLayout,
            cols: defaultColumns,
            options_id: 0,
        };
    },
});