const { src, dest, parallel, watch, registry } = require('gulp');
const sass = require("gulp-sass");
const minifyCSS = require('gulp-csso');
const concat = require('gulp-concat');

const PLUGIN_NAME = 'simple_lineup';

const PATH = {
    CSS: {
        SRC: "css/src/*.scss",
        DEST: "css"
    },
    JS: {
        SRC: "js/src/*.js",
        DEST: "js"
    }
};

function css(path, stats) {
	// Compiles SCSS > CSS.
	return src(PATH.CSS.SRC)
		.pipe(sass())
		.pipe(minifyCSS())
		.pipe(dest(PATH.CSS.DEST));
}

function js() {
	// Concates and minifies JS.
	return src(PATH.JS.SRC, { sourcemaps: true })
		.pipe(concat(PLUGIN_NAME + '.min.js'))
		.pipe(dest(PATH.JS.DEST, { sourcemaps: true }));
}

function srcWatch(){
	watch([PATH.CSS.SRC], { events: 'all', ignoreInitial: false }, registry().get('css'));
	watch([PATH.JS.SRC], { events: 'all', ignoreInitial: false }, registry().get('js'));
}

exports.js = js;
exports.css = css;
exports.watch = srcWatch;
exports.default = parallel(css, js);
