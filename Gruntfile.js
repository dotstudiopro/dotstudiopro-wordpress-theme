module.exports = function(grunt) {
		grunt.initConfig({
				pkg: grunt.file.readJSON('package.json'),
				watch: {
						sass: {
								files: ['src/scss/**/*.{scss,sass}'],
								tasks: ['sass:dist']
						},
						livereload: {
								files: ['*.html', '*.php', 'js/**/*.{js,json}', 'css/*.css', 'img/**/*.{png,jpg,jpeg,gif,webp,svg}'],
								options: {
										livereload: true
								}
						},
						uglify: {
								files: ['src/js/**/*.js'],
								tasks: ['uglify']
						}
				},
				sass: {
						options: {
								sourceMap: false,
								outputStyle: 'compressed'
						},
						dist: {
								files: [{
										'css/owl.carousel.min.css': 'src/scss/owl.carousel.scss'
								}, {
										'css/owl.carousel.admin.min.css': 'src/scss/owl.carousel.admin.scss'
								}, ]
						}
				},
				uglify: {
						my_target: {
								files: [{
										'js/owl.carousel.admin.min.js': ['src/js/owl.carousel.admin.js']
								}, {
										'js/owl.carousel.min.js': ['src/js/owl.carousel.js']
								}, {
										'js/owl.carousel.custom.min.js': ['src/js/owl.carousel.custom.js']
								}, {
										'js/channel.video.functions.min.js': ['src/js/channel.video.functions.js']
								}, {
										'js/channel.display.functions.min.js': ['src/js/channel.display.functions.js']
								}, ]
						}
				}

		});
		grunt.loadNpmTasks('grunt-sass');
		grunt.loadNpmTasks('grunt-contrib-watch');
		grunt.loadNpmTasks('grunt-contrib-uglify');

		grunt.registerTask('default', ['sass:dist', 'watch']);


};