export interface IBackendData {
    content: IPost,
    postInfo: IPostInfo,
    configuration: IConfiguration,
    savedLayouts: Array<ILayout>,
    savedTemplates: Array<IElement>,
    elementTypes: Array<IElementType>,
    i18n: IDictionary,
}

export interface IPostInfo {
    id: number,
    type: string,
}

export interface IConfiguration {
    allowedRowLayouts: Array<string>,
    defaultRowLayout: string,
    postOptionsEnabled: boolean,
    rowOptionsEnabled: boolean,
    colOptionsEnabled: boolean,
    elementOptionsEnabled: boolean,
    postLayoutsEnabled: boolean,
    elementTemplatesEnabled: boolean,
}

export interface IPost {
    options_id: number,
    rows: Array<IRow>,
}

export interface IRow {
    layout: string,
    options_id: number,
    cols: Array<IColumn>,
}

export interface IColumn {
    options_id: number,
    width: string,
    elements: Array<IElement>,
}

export interface IElementType {
    type: string,
    title: string,
    description: string,
    icon: string,
    order: number,
}

export interface IElement {
    id: number,
    options_id: number,
    is_template: boolean,
    template_deleted: boolean,
    view: string,
}

export interface ILayout {
    layout_id: number,
    name: string,
    json: string,
    time_created: number,
}

export interface IDictionary {
    [key: string]: string,
}

export interface IHistoryStep {
    title: string,
    content: Array<IPost>,
}