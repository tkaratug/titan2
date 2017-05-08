<!DOCTYPE html>
<html>
<head>
	<title>404 Error</title>
	<style type="text/css">
		@import url('https://fonts.googleapis.com/css?family=Nunito:300,400,600');
		body {
			font-family: 'Nunito', sans-serif;
			font-size: 12px;
			background-color: #fdfdfd;
		}

		a {
			text-decoration: none;
			color: #bc5858;
		}

		#logo {
			position: relative;
			top: 125px;
			width: 28%;
			margin: 0 auto;
			text-align: center;
		}

		#container {
			position: relative;
			top: 150px;
			width: 28%;
			margin: 0 auto;
		}

		#container h3 {
			text-align: center;
			margin: 0;
			padding: 10px;
			font-size: 38px;
			border-bottom: 1px solid #ccc;
			color: #777;
			font-weight: 300;
		}

		#container > h3 > span {
			font-size: 48px;
			color: #bc5858;
		}

		#container > h4 {
			text-align: center;
			margin: 0;
			padding: 10px;
			font-size: 32px;
			color: #777;
			font-weight: 300;
		}
	</style>
	<link rel="shortcut icon" sizes="16x16" href="Resources/img/favicon.png" />
</head>
<body>

	<div id="logo">
		<img src="{!! RESOURCES_DIR !!}img/titan.png" width="150" />
	</div>
	<div id="container">
		<h3>404!</h3>
		<h4>Aradığınız sayfa bulunamadı!</h4>
	</div>

</body>
</html>