const path = require('path');
const VueLoaderPlugin = require('vue-loader/lib/plugin');

const config = {
    entry: './assets/js/vue/app.ts',
    output: {
        path: path.resolve(__dirname, 'assets/js'),
        filename: 'vue.min.js',
    },
    resolve: {
        extensions: ['.ts', '.js', '.vue'],
        alias: {
            vue$: 'vue/dist/vue.esm.js',
        },
    },
    module: {
        rules: [
            {
                test: /\.vue$/,
                loader: 'vue-loader',
            },
            {
                test: /\.ts$/,
                use: {
                    loader: 'ts-loader',
                    'options': {
                        appendTsSuffixTo: [/\.vue$/],
                    },
                },
            },
            {
                test: /\.scss$/,
                use: [
                    'vue-style-loader',
                    'css-loader',
                    'sass-loader',
                ],
            },
        ],
    },
    plugins: [new VueLoaderPlugin()],
};

module.exports = config;
