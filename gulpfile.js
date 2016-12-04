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


gulp.task('run', () => {

    }
);


gulp.task('watch', () => {
    gulp.watch([], []);
});
