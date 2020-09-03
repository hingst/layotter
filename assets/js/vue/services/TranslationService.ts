import {IBackendData, IDictionary} from '../interfaces/IBackendData';

declare var layotterData: IBackendData;

class TranslationService {
    private readonly translations: IDictionary;

    constructor() {
        this.translations = layotterData.i18n;
    }

    public translate(key: string): string {
        return this.translations[key] ?? key;
    }
}

export default new TranslationService();
