const gulp = require('gulp'),
	sass = require('gulp-sass'),
	sourcemaps = require('gulp-sourcemaps'),
	minifyjs = require('gulp-minify'),
	minifycss = require('gulp-minify-css'),
	rename = require('gulp-rename'),
	fs = require('fs-extra');

gulp.task('sass', function () {
	return gulp.src('./sass/**/*.scss')
		.pipe(sass({
			outputStyle: 'compressed'
		}).on('error', sass.logError))
		.pipe(rename(function (path) {
			path.extname = ".min" + path.extname;
		}))
		.pipe(gulp.dest('./css'));
});

gulp.task('sass-dev', function () {
	return gulp.src('./sass/**/*.scss')
		.pipe(sourcemaps.init())
		.pipe(sass().on('error', sass.logError))
		.pipe(sourcemaps.write())
		.pipe(gulp.dest('./css-dev'));
});

gulp.task('css', function () {

	return new Promise(function (resolve, reject) {
		try {
			// Check to see if we have any CSS that was put into the main CSS folder that is unminified (as far as we can tell)
			// and move it to our unminified folder, so we create a minified version for upload
			fs.readdirSync('./css').forEach(fileName => {
				const isDir = fs.existsSync('./css/' + fileName) && fs.lstatSync('./css/' + fileName).isDirectory();
				// Ensure that we are looking only at css files, not minified files, and not directories
				if (fileName.indexOf(".css") > -1 && fileName.indexOf(".min.css") < 0 && !isDir) {
					fs.moveSync('./css/' + fileName, './css/unminified/' + fileName)
				}
			});
			// Minify our files
			gulp.src('./css/unminified/*.css')
				.pipe(minifycss({
					level: 2
				}))
				.pipe(rename(function (path) {
					path.extname = ".min.css";
				}))
				.pipe(gulp.dest('./css'));
			// Remove the unminified versions that cleanCSS creates
			fs.readdirSync('./css').forEach(fileName => {
				const isDir = fs.existsSync('./css/' + fileName) && fs.lstatSync('./css/' + fileName).isDirectory();
				// Ensure that we are looking only at css files, not minified files, and not directories
				if (fileName.indexOf(".css") > -1 && fileName.indexOf(".min.css") < 0 && !isDir) {
					fs.unlinkSync('./css/' + fileName);
				}
			});
			resolve("Done");
		} catch(e) {
			reject(e.message);
		}
	});
});