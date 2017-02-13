var
    pkg     = require('./package.json'),
    date    = require('date-utils'),
    del     = require('del'),
    gulp    = require('gulp'),
    less    = require('gulp-less'),
    minify  = require('gulp-minify-css'),
    replace = require('gulp-replace'),
    rename  = require('gulp-rename'),
    util    = require('gulp-util'),
    uglify  = require('gulp-uglify'),
    zip     = require('gulp-zip'),
    header  = require('gulp-header'),
    clean   = require('gulp-clean'),
    merge   = require('merge-stream'),
    runSeq  = require('run-sequence'),
    sym     = require('gulp-sym'),
    fs      = require('fs'),

    banner = ['/**',
        ' * @package     '+pkg.name.split("-").join(" "),
        ' * @version     '+pkg.version,
        ' * @author      '+pkg.authors[0].name+' - '+pkg.authors[0].homepage,
        ' * @license     '+pkg.license,
        ' */',
        ''].join('\n').replace(/\n$/g, ''),

    output = util.env.output || util.env.o || 'dist';


/** TASKS **/

gulp.task('default', ['build']);

gulp.task('build', function(cb) {
    runSeq(
        'build-clean',
        'build-copy',
        'build-compress',
        'build-prepare',
        'build-zip',
        cb
    );
});

gulp.task('build-clean', function(cb) {
    del(['dist'], cb);
});

gulp.task('build-prepare', function() {
    return merge(

        // add headers
        gulp.src([
            'dist/tmp/**/*.php'
        ]).pipe(replace(/^<\?php/g, '<?php\n'+banner))
            .pipe(gulp.dest('dist/tmp/')),

        gulp.src([
            'dist/tmp/**/*.js',
            'dist/tmp/**/*.css',
            '!dist/tmp/**/*.min.js',
            '!dist/tmp/**/*.min.css'
        ]).pipe(header(banner+'\n\n'))
            .pipe(gulp.dest('dist/tmp/'))
    );
});

gulp.task('build-compress', function() {
    return merge(
        gulp.src(['dist/tmp/**/*.css', '!dist/tmp/**/*.min.css'])
            .pipe(minify())
            .pipe(gulp.dest('dist/tmp/')),

        gulp.src(['dist/tmp/**/*.js', '!dist/tmp/**/*.min.js'])
            .pipe(uglify())
            .pipe(gulp.dest('dist/tmp/'))
    );
});


gulp.task('build-copy', function() {
    return merge(

        // package
        gulp.src(['zoolanders/pkg_zoolanders.xml'])
            .pipe(gulp.dest('dist/tmp')),

        // library
        gulp.src(['zoolanders/Framework/**'])
            .pipe(gulp.dest('dist/tmp/packages/library/zoolanders/Framework')),
        // library
        gulp.src(['zoolanders/installation/**'])
            .pipe(gulp.dest('dist/tmp/packages/library/zoolanders/installation')),


        gulp.src([
            'zoolanders/vendor/**/*',
            '!zoolanders/vendor/**/*.md',
            '!zoolanders/vendor/**/*.txt',
            '!zoolanders/vendor/**/*.pdf',
            '!zoolanders/vendor/**/LICENSE',
            '!zoolanders/vendor/**/CHANGES',
            '!zoolanders/vendor/**/README',
            '!zoolanders/vendor/**/VERSION',
            '!zoolanders/vendor/**/composer.json',
            '!zoolanders/vendor/**/.gitignore',
            '!zoolanders/vendor/**/docs',
            '!zoolanders/vendor/**/docs/**',
            '!zoolanders/vendor/**/tests',
            '!zoolanders/vendor/**/tests/**',
            '!zoolanders/vendor/**/unitTests',
            '!zoolanders/vendor/**/unitTests/**',
            '!zoolanders/vendor/**/.git',
            '!zoolanders/vendor/**/.git/**',
            '!zoolanders/vendor/**/examples',
            '!zoolanders/vendor/**/examples/**',
            '!zoolanders/vendor/**/build.xml',
            '!zoolanders/vendor/**/phpunit.xml',
            '!zoolanders/vendor/**/phpunit.xml.dist',
            '!zoolanders/vendor/**/bin',
            '!zoolanders/vendor/**/bin/**'
            // if you thing there is more let me know.
        ])
            .pipe(gulp.dest('dist/tmp/packages/library/zoolanders/vendor')),

        gulp.src(['zoolanders/*.php'])
            .pipe(gulp.dest('dist/tmp/packages/library/zoolanders')),

        gulp.src(['zoolanders/lib_zoolanders.xml'])
            .pipe(gulp.dest('dist/tmp/packages/library')),
        gulp.src(['zoolanders/install.script.php'])
            .pipe(gulp.dest('dist/tmp/packages/library')),

        // main plugin
        gulp.src(['plugin/**'])
            .pipe(gulp.dest('dist/tmp/packages/plg_zlframework'))
    );
});

gulp.task('build-zip', ['build-zip-packages'], function() {
    return gulp.src([
        'dist/tmp/**/*.zip',
        'dist/tmp/pkg_zoolanders.xml',
    ]).pipe(zip('pkg_zoolanders.zip'))
        .pipe(gulp.dest(output));
});

gulp.task('build-zip-packages', function() {
    return merge(
        gulp.src('dist/tmp/packages/library/**/*')
            .pipe(zip('lib_zoolanders.zip'))
            .pipe(gulp.dest('dist/tmp/packages/')),

        gulp.src('dist/tmp/packages/plg_zlframework/**/*')
            .pipe(zip('plg_zlframework.zip'))
            .pipe(gulp.dest('dist/tmp/packages/'))
    );
});

gulp.task('bump', function() {
    var semver = require('semver');
    var release = util.env.release || util.env.r || 'patch';

    // bump version
    pkg.version = semver.inc(pkg.version, release);

    return merge(
        gulp.src('./package.json')
            .pipe(replace(/"version":."(.+)"/, '"version": "' + pkg.version + '"'))
            .pipe(gulp.dest('./')),

        gulp.src('./CHANGELOG.md')
            .pipe(replace(/###.WIP/, '### ' + pkg.version))
            .pipe(gulp.dest('./')),

        tagHelper(gulp.src(['_*/**/*.xml']))
            .pipe(gulp.dest('./'))

    );

});


/* helper */

function tagHelper(stream) {
    return stream
        .pipe(replace(
            /<version>(.*)<\/version>/,
            '<version>' + pkg.version + '</version>'
        ))
        .pipe(replace(
            /<creationDate>(.*)<\/creationDate>/,
            '<creationDate>' + Date.today().toFormat('MMMM YYYY') + '</creationDate>'
        ));
};
