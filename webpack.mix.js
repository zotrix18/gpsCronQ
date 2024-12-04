const mix = require("laravel-mix");

mix
  .js("resources/js/app.js", "public/js")
  .sass("resources/sass/app.scss", "public/css")
  .browserSync({
    proxy: "localhost:8000", // Cambia esto si usas un puerto diferente
    files: [
      "app/**/*.php",
      "resources/views/**/*.php",
      "public/js/**/*.js",
      "public/css/**/*.css",
    ],
    notify: false, // Opcional: elimina las notificaciones de Browsersync
  });
