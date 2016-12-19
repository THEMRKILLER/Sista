var elixir = require ('laravel-elixir');

require('browserify');
require('laravel-elixir-vueify');


elixir(function(mix){
	mix.browserify('app.js');
});