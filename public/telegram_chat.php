<?php  

if (! isset($_GET["group_id"])) {
	
}

?><!DOCTYPE html>
<html>
<head>
	<title></title>
	<style type="text/css">
		.mcage {
			border: 1px solid #000;
			width: 500px;
			min-height: 70px;
			margin-bottom: 2px;
		}
		.name {
			margin-top: 10px;
		}
		.text {
			margin-top: 10px;
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
			return '<img src="https://webhook-a2.teainside.tech/storage/files/'+mg['sha1_checksum_file']+'_'+mg['md5_checksum_file']+'.jpg"/>';
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
									cn = r[i]['text'];
								}
								q.innerHTML += "<div id=\"chat_bind\">\
									<div class=\"mcage\">\
										<div class=\"name\">"+r[i]['first_name']+"</div>\
										<div class=\"text\">"+cn+"</div>\
									</div> \
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