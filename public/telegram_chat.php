<?php  

if (! isset($_GET["group_id"])) {
	
}

?><!DOCTYPE html>
<html>
<head>
	<title></title>
	<style type="text/css">
		* {
			font-family: Arial;
		}

		.text {
			text-align: left;
			width: 740px;
		}
		.mcage {
			width: 750px;
			min-height: 100px;
			padding-bottom: 10px;
			border: 1px solid #000;
		}
		#chat_bind {
			margin-top: 50px;
			border: 1px solid #000;
			height: 530px;
			width: 800px;
			overflow-y: scroll;
		}
		.img-chat {
			max-width: 400px;
			max-height: 400px;
		}
	</style>
</head>
<body>
	<center>
		<div id="chat_bind"></div>
	</center>
	<script type="text/javascript">

		function buildImage(mg) {
			console.log(mg);
			return '<img class="img-chat" src="https://webhook-a2.teainside.tech/storage/files/'+mg['sha1_checksum_file']+'_'+mg['md5_checksum_file']+'.jpg"/>';
		}

		function getChat() {
			var ch = new XMLHttpRequest();
				ch.onreadystatechange = function () {
					if (this.readyState === 4) {
						try {
							var r = JSON.parse(this.responseText), q = document.getElementById("chat_bind"), cn;
							r = r["message"];
							q.innerHTML = "";
							for (var i = r.length - 1; i >= 0; i--) {
								if (r[i]['msg_type'] == 'photo') {
									cn = buildImage(r[i]);
									console.log(cn);
								} else {
									cn = "<div class=\"text\">"+r[i]['text'].replace("\n", "<br/>")+"</div>";
								}
								q.innerHTML += "<div class=\"mcage\">\
										<div class=\"name\"><strong>["+r[i]['id']+"]<br/>"+r[i]['first_name']+(r[i]['last_name']?" "+r[i]['last_name']:"")+"<br/>"+(r[i]['username'] ? "(@"+r[i]['username']+")" : "(No Username)")+"</strong><br/><br/></div>\
										"+cn+"\
									</div>";
							}
						} catch (e) {
							console.log("Error, responseText = " +this.responseText);
						}
					}
				};

				ch.open("GET", "https://webhook-a2.teainside.tech/api.php?method=getchat&group_id=<?php print urlencode($_GET["group_id"]); ?>&limit=7");
				ch.send();
		}

		getChat();

		setInterval(function () {
			getChat();
		}, 3000);
	</script>
</body>
</html>