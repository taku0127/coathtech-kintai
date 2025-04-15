const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */
 const fs = require('fs');
 const glob = require('glob');

 mix.before(() => {
    const scssDirs = glob.sync('resources/scss/*', {
      ignore: ['resources/scss/_*'],
      nodir: false
    }).filter(dir => fs.lstatSync(dir).isDirectory());

    scssDirs.forEach(dir => {
      const scssFiles = glob.sync(`${dir}/_*.scss`, { ignore: [`${dir}/_forward.scss`] });

      const forwardContent = scssFiles.map(file => {
        return `@forward '${file.replace(`${dir}/_`, '').replace('.scss', '')}';`;
      }).join('\n');

      fs.writeFileSync(`${dir}/_forward.scss`, forwardContent);

      console.log(`✅ ${dir}/_forward.scss を更新しました！`);
    });
  });
 glob.sync('resources/scss/*.scss').map(function (file) {
   mix.sass(file, 'public/css').options({
     processCssUrls: false,
   });
 });

 glob.sync('resources/js/*.js').map(function (file) {
   mix.js(file, 'public/js');
 });
