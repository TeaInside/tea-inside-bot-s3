"use strict";

const fs        = require("fs");
const login     = require("facebook-chat-api");
const config    = require(__dirname+"/../../../config/facebook/config.js");

// Simple echo bot. It will repeat everything that you say.
// Will stop when you say '/stop'
login({appState: JSON.parse(fs.readFileSync(config["app_state_file"], 'utf8'))}, (err, api) => {
    if(err) return console.error(err);

    api.setOptions({listenEvents: true});

    // api.sendMessage("test solid report", config["solid_report"]);

    // api.listen((err, event) => {
    //     if(err) return console.error(err);

    //     api.markAsRead(event.threadID, (err) => {
    //         if(err) console.error(err);
    //     });

    //     switch(event.type) {
    //         case "message":
    //             if(event.body === '/stop') {
    //                 api.sendMessage("Goodbye…", event.threadID);
    //                 return stopListening();
    //             }
    //             api.sendMessage("TEST BOT: " + event.body, event.threadID);
    //             break;
    //         case "event":
    //             console.log(event);
    //             break;
    //     }
    // });
});
