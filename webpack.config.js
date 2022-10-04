const { VueLoaderPlugin } = require("vue-loader");
const webpack = require("webpack");
const path = require("path");

module.exports = {
	entry:{
		"map": "./js/map.js",
		"main": "./js/main.js"
	},
	output:{
		path: __dirname + "/dist",
		filename:"[name].js"
	},
	resolve:{
		extensions: ["", ".js"]
	},
	devtool: "inline-source-map",
	plugins:[
		new VueLoaderPlugin()
	],
	module:{
		rules:[
			{
				test: /\.js$/,
				loader: "babel-loader"
			},
			{
				test: /\.vue$/,
				loader: "vue-loader"
			},
			{
				test: /\.css$/,
				use:[
					"vue-style-loader",
					"css-loader"
				]
			}
		]
	}
}
