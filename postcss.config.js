const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');
const colorFunction = require('postcss-color-function');

module.exports = {
  plugins: [colorFunction, autoprefixer, cssnano],
};
