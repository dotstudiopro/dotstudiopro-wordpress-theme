module.exports = function(grunt) {
		grunt.initConfig({
				pkg: grunt.file.readJSON('package.json'),
				watch: {
						uglify: {
								files: ['js/original/dotstudio.plugin.admin.js', 'js/original/jquery.gridder.js', 'js/original/owl.carousel.js', 'js/original/channel.functions.js', 'js/original/channel.video.functions.js', 'js/original/channel.display.functions.js'],
								tasks: ['uglify']
						},
						livereload: {
								files: ['*.html', '*.php', 'js/**/*.{js,json}', 'css/*.css', 'img/**/*.{png,jpg,jpeg,gif,webp,svg}'],
								options: {
										livereload: true
								}
						}
				},
				uglify: {
						my_target: {
								files: [{
										'js/dotstudio.plugin.admin.min.js': ['js/original/dotstudio.plugin.admin.js']
								}, {
										'js/jquery.gridder.min.js': ['js/original/jquery.gridder.js']
								}, {
										'js/owl.carousel.min.js': ['js/original/owl.carousel.js']
								}, {
										'js/channel.functions.min.js': ['js/original/channel.functions.js']
								}, {
										'js/channel.video.functions.min.js': ['js/original/channel.video.functions.js']
								}, {
										'js/channel.display.functions.min.js': ['js/original/channel.display.functions.js']
								}, ]
						}
				}

		});
		grunt.loadNpmTasks('grunt-sass');
		grunt.loadNpmTasks('grunt-contrib-watch');
		grunt.loadNpmTasks('grunt-contrib-uglify');
		grunt.registerTask('default', ['sass:dist', 'watch']);


};
