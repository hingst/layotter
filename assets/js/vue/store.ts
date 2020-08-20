import Vue from 'vue';
import Vuex from 'vuex'
import {
    IColumn,
    IConfiguration, IDictionary,
    IElement,
    IElementType, IHistoryStep,
    ILayout,
    IPost,
    IPostInfo, IRow
} from './interfaces/IBackendData';

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        isLoading: false,
        content: {} as IPost,
        postInfo: {} as IPostInfo,
        configuration: {} as IConfiguration,
        savedLayouts: [] as Array<ILayout>,
        savedTemplates: [] as Array<IElement>,
        elementTypes: [] as Array<IElementType>,
        history: {
            steps: [] as Array<IHistoryStep>,
            deletedTemplates: [] as Array<Number>,
            currentStep: -1,
        },
        componentTemplates: {
            row: {} as IRow,
            column: {} as IColumn,
            element: {} as IElement,
        },
        i18n: {} as IDictionary,
    },
    actions: {
        pushStep(context, title: string): void {
            if (context.getters.canRedo) {
                context.state.history.steps.splice(context.state.history.currentStep + 1, context.state.history.steps.length);
            }

            context.state.history.steps.push({
                title: title,
                content: JSON.parse(JSON.stringify(context.state.content)),
            });

            context.state.history.currentStep++;
        },
        async undoStep(context): Promise<void> {
            if (context.getters.canUndo) {
                await context.dispatch('restoreStep', context.state.history.currentStep - 1);
            }
        },
        async redoStep(context): Promise<void> {
            if (context.getters.canRedo) {
                await context.dispatch('restoreStep', context.state.history.currentStep + 1);
            }
        },
        restoreStep(context, step: number): void {
            let restore = JSON.parse(JSON.stringify(context.state.history.steps[step].content));

            restore.rows.forEach((row: IRow) => {
                row.cols.forEach((column) => {
                    column.elements.forEach((element) => {
                        if (element.is_template && !element.template_deleted) {
                            if (context.state.history.deletedTemplates.indexOf(element.id) !== -1) {
                                element.is_template = false;
                                element.template_deleted = true;
                            }
                        }
                    });
                });
            });

            context.state.history.currentStep = step;
            context.state.content.options_id = restore.options_id;
            context.state.content.rows = restore.rows;
        },
    },
    getters: {
        canUndo(state): boolean {
            return (state.history.currentStep > 0);
        },
        canRedo(state): boolean {
            return (state.history.currentStep < state.history.steps.length - 1);
        },
        undoTitle(state): string {
            const message = state.history.steps[state.history.currentStep]
                ? state.history.steps[state.history.currentStep].title
                : '';
            return state.i18n.undo + ' ' + message;
        },
        redoTitle(state): string {
            const message = state.history.steps[state.history.currentStep + 1]
                ? state.history.steps[state.history.currentStep + 1].title
                : '';
            return state.i18n.redo + ' ' + message;
        },
    },
});