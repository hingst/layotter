export interface BackendData {
    postID: number,
    postType: string,
    contentStructure: Post,
    allowedRowLayouts: Array<string>,
    defaultRowLayout: string,
    savedLayouts: Array<Layout>,
    savedTemplates: Array<Element>,
    enablePostLayouts: boolean,
    enableElementTemplates: boolean,
    elementTypes: Array<ElementType>,
    isOptionsEnabled: IsOptionsEnabled,
    i18n: Dictionary,
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

export interface IsOptionsEnabled {
    post: boolean,
    row: boolean,
    col: boolean,
    element: boolean,
}

export interface Dictionary {
    [key: string]: string,
}