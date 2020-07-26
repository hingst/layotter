export interface LayotterData {
    postID: number,
    postType: string,
    contentStructure: {
        options_id: number,
        rows: Array<object>
    },
    allowedRowLayouts: Array<object>,
    defaultRowLayout: string,
    savedLayouts: Array<object>,
    savedTemplates: Array<object>,
    enablePostLayouts: boolean,
    enableElementTemplates: boolean,
    elementTypes: Array<object>,
    isOptionsEnabled: {
        post: boolean,
        row: boolean,
        col: boolean,
        element: boolean,
    },
    i18n: Array<object>,
}