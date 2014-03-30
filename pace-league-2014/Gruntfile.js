'use strict';
module.exports = function (grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        // package options
        less: {
            mini: {
                options: {
                    cleancss: false, // minify
                    report: 'min' // minification results
                },
                expand: true, // set to true to enable options following options:
                cwd: "assets/less/", // all sources relative to this path
                src: "*.less", // source folder patterns to match, relative to cwd
                dest: "public/build/css/", // destination folder path prefix
                ext: ".css", // replace any existing extension with this value in dest folder
                flatten: true  // flatten folder structure to single level
            }
        },



        express: {
            server: {
                options: {
                    port: 3000,
                    hostname: 'localhost',
                    bases: 'public'
                }
            }
        },
        jshint: {
            options: {
                jshintrc: '.jshintrc'
            },
            all: [
                'Gruntfile.js',
                'assets/js/*.js'
            ]
        },
        concat: {
            basic: {
                src: [
                    'bower_components/jquery/dist/jquery.js',
                    'bower_components/bootstrap/dist/js/bootstrap.js',
                    'assets/js/app.js'
                ],
                dest: 'tmp/app.js'
            },
            extras: {
                src: [
                    'bower_components/modernizr/modernizr.js'
                ],
                dest: 'tmp/modernizr.js'
            }
        },
        compass: {
            dist: {
                options: {
                    config: 'config.rb'
                }
            }
        },
        imagemin: {
            dynamic: {
                files: [{
                    expand: true,
                    cwd: 'assets/img/',
                    src: ['**/*.{png,jpg,gif}'],
                    dest: 'public/build/img/'
                }]
            }
        },
        uglify: {
            build: {
                files: {
                    'public/build/js/modernizr.min.js' : 'tmp/modernizr.js',
                    'public/build/js/app.min.js' : 'tmp/app.js'
                }
            }
        },
        clean: {
            dist: [
                'tmp/**',
                'public/build/img/**'
            ]
        },
        watch: {
            compass: {
                files: ['assets/sass/**/*.{scss,sass}'],
                tasks: ['compass']
            },
            less: {
                files: ['assets/less/*.less'],  //watched files
                tasks: ['less'],                          //tasks to run
                options: {
                    livereload: true                        //reloads the browser
                }
            },
            css: {
                files: ['public/build/css/*'],
                options: {
                    livereload: true
                }
            },
            js: {
                files: [
                    'assets/js/*.js'
                ],
                tasks: ['concat', 'uglify'],
                options: {
                    livereload: true,
                    atBegin: true
                }
            },
            imagemin: {
                files: [
                    'assets/img/**'
                ],
                tasks: ['imagemin'],
                options: {
                    livereload: true,
                    atBegin: true
                }
            }
        }
    });

    // Load tasks
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-notify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-imagemin');
    grunt.loadNpmTasks('grunt-contrib-compass');
    grunt.loadNpmTasks('grunt-express');

    // Register default tasks
    grunt.registerTask('default', [
        'express:server',
        'watch'
    ]);

};