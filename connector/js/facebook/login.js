"use strict";

const fs		= require("fs");
const login		= require("facebook-chat-api");
const config	= require(__dirname+"/../../../config/facebook/config.js");

login(config["credentials"], function (err, api) {
	if(err) {
		return console.error(err);
	}
	fs.writeFileSync(config["app_state_file"], JSON.stringify(api.getAppState()));
});
