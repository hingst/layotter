import Vue from 'vue';
import Vuex from 'vuex'
import {IHistoryStep, IPost,} from './interfaces/IBackendData';
import Util from './util';

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        content: {} as IPost,
        history: {
            steps: [] as Array<IHistoryStep>,
            deletedTemplates: [] as Array<Number>,
            currentStep: -1,
        },
    },
    actions: {
        pushStep(context, title: string): void {
            if (context.getters.canRedo) {
                context.state.history.steps.splice(context.state.history.currentStep + 1, context.state.history.steps.length);
            }

            context.state.history.steps.push({
                title: title,
                content: Util.clone(context.state.content),
            });

            context.state.history.currentStep++;
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
            return state.history.steps[state.history.currentStep]
                ? state.history.steps[state.history.currentStep].title
                : '';
        },
        redoTitle(state): string {
            return state.history.steps[state.history.currentStep + 1]
                ? state.history.steps[state.history.currentStep + 1].title
                : '';
        },
    },
});