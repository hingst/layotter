export interface BackendData {
    content: Post,
    postData: PostData,
    configuration: Configuration,
    savedLayouts: Array<Layout>,
    savedTemplates: Array<Element>,
    elementTypes: Array<ElementType>,
    i18n: Dictionary,
}

export interface PostData {
    id: number,
    type: string,
}

export interface Configuration {
    allowedRowLayouts: Array<string>,
    defaultRowLayout: string,
    postOptionsEnabled: boolean,
    rowOptionsEnabled: boolean,
    colOptionsEnabled: boolean,
    elementOptionsEnabled: boolean,
    postLayoutsEnabled: boolean,
    elementTemplatesEnabled: boolean,
}

export interface Post {
    options_id: number,
    rows: Array<Row>,
}

export interface Row {
    options_id: number,
    cols: Array<Column>,
}

export interface Column {
    options_id: number,
    width: string,
    elements: Array<Element>,
}

export interface ElementType {
    type: string,
    title: string,
    description: string,
    icon: string,
    order: number,
}

export interface Element {
    id: number,
    options_id: number,
    is_template: boolean,
    view: string,
}

export interface Layout {
    layout_id: number,
    name: string,
    json: string,
    time_created: number,
}

export interface Dictionary {
    [key: string]: string,
}