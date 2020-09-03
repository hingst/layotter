import {IBackendData, IColumn, IElement, IRow, IViewTemplates} from '../interfaces/IBackendData';
import Util from '../util';

declare var layotterData: IBackendData;

class TemplateService {
    private readonly templates: IViewTemplates;

    constructor() {
        let templates = {} as IViewTemplates;

        templates.element = {
            id: 0,
            view: '',
            options_id: 0,
            is_template: false,
        };

        templates.column = {
            elements: [],
            options_id: 0,
            width: '',
        };

        const defaultColumnCount = layotterData.configuration.defaultRowLayout.split(' ').length;
        const defaultColumns = [];
        for (let i = 0; i < defaultColumnCount; i++) {
            defaultColumns.push(Util.clone(templates.column));
        }

        templates.row = {
            layout: layotterData.configuration.defaultRowLayout,
            cols: defaultColumns,
            options_id: 0,
        };

        this.templates = templates;
    }

    public getRowTemplate(): IRow {
        return this.templates.row;
    }

    public getColumnTemplate(): IColumn {
        return this.templates.column;
    }
}

export default new TemplateService();
