const path = require('path');

module.exports = {
    entry: [
        __dirname + '/src/js/simple_lineup.js',
        __dirname + '/src/scss/simple_lineup.scss'
    ],
    output: {
        path: path.resolve(__dirname, 'dist'), 
        filename: 'js/simple_lineup.min.js',
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: [],
            }, {
                test: /\.scss$/,
                exclude: /node_modules/,
                use: [
                    {
                        loader: 'file-loader',
                        options: { outputPath: 'css/', name: 'simple_lineup.min.css'}
                    },
                    'sass-loader'
                ]
            }
        ]
    }
};
