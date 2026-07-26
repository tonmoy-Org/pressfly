const mix = require('laravel-mix');

if (process.env.env_type === 'frontend') {
    mix.js('resources/frontend/js/app.js', 'js')
        .sass('resources/frontend/sass/app.scss', 'css')
        .options({
                postCss: [
                    require('postcss-discard-comments')({
                        removeAll: true,
                    }),
                ],
            },
        )
        .setPublicPath('../assets/frontend')
        .setResourceRoot('../../') // https://github.com/JeffreyWay/laravel-mix/issues/1026
        //.sourceMaps()
        .version();
}

if (process.env.env_type === 'backend') {
    mix.js('resources/backend/js/app.js', 'js')
        .sass('resources/backend/sass/app.scss', 'css')
        .options({
                postCss: [
                    require('postcss-discard-comments')({
                        removeAll: true,
                    }),
                ],
            },
        )
        .setPublicPath('../assets/backend')
        .setResourceRoot('../../') // https://github.com/JeffreyWay/laravel-mix/issues/1026
        //.sourceMaps()
        .version();
}

if (!mix.inProduction()) {
    mix.sourceMaps().webpackConfig({
        devtool: 'source-map',
    });
}

// mix.webpackConfig({
//     stats: {
//         children: true,
//     },
// });

// https://www.npmjs.com/package/cross-env
// cross-env my_var=my_value npm run development
// console.log(process.env.my_var);
