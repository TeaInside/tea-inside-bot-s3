// "use strict";

const fs        = require("fs");
const login     = require("facebook-chat-api");
const config    = require(__dirname+"/../../../config/facebook/config.js");
// http://nodejs.org/api.html#_child_processes
var sys = require('sys')
var exec = require('child_process').exec;
var child;

const options = {
    selfListen: false,
    listenEvents: false,
    updatePresence: false,
    forceLogin: true,
    userAgent: "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/600.3.18 (KHTML, like Gecko) Version/8.0.3 Safari/600.3.18"
};

var i = 0;

login({appState: JSON.parse(fs.readFileSync(config["app_state_file"], 'utf8'))}, options, (err, api) => {
    if(err) return console.error(err);
    var r1 = function () {
        let w = new Promise((resolve0, reject) => {
            exec("ls \""+config["queues"]+"\" | grep .jqueue\$", function (error, stdout, stderr) {
                if (error !== null) {
                    resolve0();
                } else {
                    var x = [], k, q, s = 0;
                    stdout = stdout.split("\n");
                    for(k in stdout) {
                        stdout[k] = stdout[k].split(" ");
                        for(q in stdout[k]) {
                            stdout[k][q] = stdout[k][q].trim();
                            if (stdout[k][q] != "") {
                                x[s++] = stdout[k][q];
                            }
                        }
                    }
                    console.log(x);
                    var r = function (jj) {
                        let z = new Promise((resolve1, reject) => {
                            var s = JSON.parse(fs.readFileSync(config["queues"]+"/"+x[jj], 'utf8')),
                            rr = function (ii) {
                                let p = new Promise((resolve2, reject) => {
                                    if (s[ii]["message_type"] === "text") {
                                        console.log(s[ii]);
                                        api.sendMessage(s[ii]["text"], config["solid_report"], function (err, res) {
                                            if (err) {
                                                console.log("Error: "+err);
                                            } else {
                                                console.log(res);
                                            }
                                            resolve2();
                                        });
                                    }
                                });

                                p.then(() => {
                                    if (typeof s[ii+1] != "undefined") {
                                        rr(ii+1);
                                    } else {
                                        exec("rm -rfv \""+config["queues"]+"/"+x[jj]+"\"", function(error, stdout, stderr){
                                            console.log("stdout: "+stdout.trim());
                                            console.log("stderr: "+stderr.trim());
                                            if (error !== null) {
                                                console.log("exec error: "+error.trim());
                                            }
                                            console.log("\n\n");
                                            resolve1();
                                        });
                                    }
                                });
                            };
                            rr(0);
                        });

                        z.then(() => {
                            if (typeof x[jj+1] != "undefined") {
                                r(jj+1);
                            } else {
                                resolve0();
                            }
                        });
                    }
                    r(0);            
                }
            });
        });
        w.then(() => {
            r1();
        });
    };
    r1();
});
    