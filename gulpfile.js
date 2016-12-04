'use strict';

const gulp = require('gulp');
const autoprefixer = require('gulp-autoprefixer');
const concat = require('gulp-concat');
const sass = require('gulp-sass');
const cleanCSS = require('gulp-clean-css');
const rename = require("gulp-rename");
const ts = require('gulp-typescript');
const fs = require('fs');
const jsmin = require('gulp-jsmin');

const sassPath = './frontend/sass/';
const tsPath = './frontend/typescript/';

let jsFiles = [
    './node_modules/jquery/dist/jquery.min.js',
    './node_modules/bootstrap/dist/js/bootstrap.min.js'
];

gulp.task('sass-autoprefix-minify', () => {
        return gulp.src(sassPath + 'style.scss')
            .pipe(sass())
            .pipe(autoprefixer({
                browsers: ['last 2 versions'],
                cascade: false
            }))
            .pipe(cleanCSS({compatibility: 'ie8'}))
            .pipe(rename('compiled.css'))
            .pipe(gulp.dest(sassPath));
    }
);

gulp.task('concat-css', ['sass-autoprefix-minify'], () => {
    gulp.src([
        './node_modules/bootstrap/dist/css/bootstrap.min.css',
        sassPath + 'compiled.css'
    ])
        .pipe(concat('style.css'))
        .pipe(gulp.dest('./web/assets/css/'));
});

gulp.task('ts-minify', () => {
    return gulp.src(tsPath + 'script.ts')
        .pipe(ts({
            noImplicitAny: true,
            out: 'output.js'
        }))
        .pipe(jsmin())
        .pipe(gulp.dest(tsPath))
});

gulp.task('concat-js', ['ts-minify'], () => {
    fs.stat(tsPath + 'output.js', (err, stats) => {
        if (err === null && stats['size'] > 0) {
            jsFiles.push(tsPath + 'output.js');
        }
        gulp.src(jsFiles)
            .pipe(concat('main.js'))
            .pipe(gulp.dest('./web/assets/js/'));
    });
});

gulp.task('copy-fonts', () => {
    gulp.src('./node_modules/bootstrap/dist/fonts/*')
        .pipe(gulp.dest('web/assets/fonts'));
});

gulp.task('watch', () => {
    gulp.watch([], []);
});


gulp.task('run', [
    'concat-css',
    'concat-js',
    'copy-fonts'
]);