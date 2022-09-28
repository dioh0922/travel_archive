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
	module:{
		rules:[
			{
				test:/\.js$/,
				exclude: /node_modules/,
				loader: "babel-loader"
			}
		]
	}
}
