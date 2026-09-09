const webpack = require('webpack');
const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');

let config = {
  entry: {
    theme: ['./js/admin.js', './css/style.scss'],
  },
  output: {
    path: path.resolve(__dirname, '../public/assets'),
    filename: '[name].js',
  },
  // theme.css.map points the browser dev tools back to the SCSS partial and line; the CSS is
  // left expanded because node-sass maps a compressed output to a single line. Maps are
  // ignored by git (see ../.gitignore). JS is minified without a map to keep the build fast.
  devtool: 'source-map',
  module: {
    rules: [
      {
        test: /\.js/,
        loader: 'babel-loader',
      },
      {
        test: /\.scss$/,
        use:[
            MiniCssExtractPlugin.loader,
            {loader: 'css-loader', options: {sourceMap: true}},
            {loader: 'postcss-loader', options: {sourceMap: true}},
            {loader: 'sass-loader', options: {sourceMap: true, sassOptions: {outputStyle: 'expanded'}}},
          ],
      },
      {
        test: /.(png|woff(2)?|eot|otf|ttf|svg|gif)(\?[a-z0-9=\.]+)?$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              name: '../css/[hash].[ext]',
            },
          },
        ],
      },
      {
        test: /\.css$/,
        use: [MiniCssExtractPlugin.loader, 'style-loader', 'css-loader', 'postcss-loader'],
      },
    ],
  },
  externals: {
    $: '$',
    // jquery: 'jQuery',
  },
  resolve: {
    // one jQuery for everybody: jquery-ui ships its own copy, its widgets must attach to ours
    alias: {
      jquery: path.resolve(__dirname, 'node_modules/jquery'),
    },
  },
  plugins: [
    new MiniCssExtractPlugin({filename: path.join('..', 'css', '[name].css')}),
  ]
};

if (process.env.NODE_ENV === 'production') {
  config.optimization = {
    minimizer: [
      new UglifyJsPlugin({
        sourceMap: false,
        uglifyOptions: {
          compress: {
            sequences: true,
            conditionals: true,
            booleans: true,
            if_return: true,
            join_vars: true,
            drop_console: true,
          },
          output: {
            comments: false,
          },
        }
      })
    ]
  }
}

module.exports = config;
