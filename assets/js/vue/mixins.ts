import TranslationService from './services/TranslationService';

export default {
    methods: {
        translate(key: string): string {
            return TranslationService.translate(key);
        }
    },
}