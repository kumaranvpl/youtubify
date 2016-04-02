<?php include 'install_files/php/boot.php'; ?>
<html ng-app="installer">
<head>
	<title>Youtubify - Installation</title>

	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

	<link rel="stylesheet" href="install_files/css/styles.css">
	<link rel="stylesheet" href="install_files/css/fontello-embedded.css">
	<link rel="stylesheet" href="install_files/css/animate.css">
	<link href='http://fonts.googleapis.com/css?family=RobotoDraft:300,400,500,700,900' rel='stylesheet' type='text/css'>

	<style>#splash,[ng-cloak]{display:none}#splash,.inner{position:absolute}[ng-cloak]#splash{display:flex!important}#splash{top:0;left:0;width:100%;height:100%;z-index:9999;justify-content:center;align-items:center}#splash-spinner{display:block!important;width:120px;height:120px;border-radius:50%;perspective:800px}.inner{box-sizing:border-box;width:100%;height:100%;border-radius:50%}.inner.one{left:0;top:0;animation:rotate-one 1s linear infinite;border-bottom:3px solid #84BD00}.inner.two{right:0;top:0;animation:rotate-two 1s linear infinite;border-right:3px solid #84BD00}.inner.three{right:0;bottom:0;animation:rotate-three 1s linear infinite;border-top:3px solid #84BD00}@keyframes rotate-one{0%{transform:rotateX(35deg) rotateY(-45deg) rotateZ(0)}100%{transform:rotateX(35deg) rotateY(-45deg) rotateZ(360deg)}}@keyframes rotate-two{0%{transform:rotateX(50deg) rotateY(10deg) rotateZ(0)}100%{transform:rotateX(50deg) rotateY(10deg) rotateZ(360deg)}}@keyframes rotate-three{0%{transform:rotateX(35deg) rotateY(55deg) rotateZ(0)}100%{transform:rotateX(35deg) rotateY(55deg) rotateZ(360deg)}}</style>
</head>

<body id="install" ng-controller="InstallController">

	<div id="splash" ng-cloak>
		<div id="splash-spinner">
			<div class="inner one"></div>
			<div class="inner two"></div>
			<div class="inner three"></div>
		</div>
	</div>

	<div class="header">
		<div class="container">
			<div class="left">
				<div class="logo"><img class="img-responsive" src="assets/images/logo_light.png" alt="logo"></div>
				<h2 class="current-step">{{ currentStep }}</h2>
			</div>

			<ul class="wizard right">
				<li ng-class="{ active: currentStep === 'compatability'}" class="compat">1</li>
				<li ng-class="{ active: currentStep === 'database'}" class="db">2</li>
				<li ng-class="{ active: currentStep === 'admin'}" class="config">3</li>
				<li ng-class="{ active: currentStep === 'finalize'}" class="config">4</li>
			</ul>
		</div>
	</div>

	<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="100" viewBox="0 0 100 100" preserveAspectRatio="none">
		<path d="M-5 100 Q 0 20 5 100 Z
			M0 100 Q 5 0 10 100
			M5 100 Q 10 30 15 100
			M10 100 Q 15 10 20 100
			M15 100 Q 20 30 25 100
			M20 100 Q 25 -10 30 100
			M25 100 Q 30 10 35 100
			M30 100 Q 35 30 40 100
			M35 100 Q 40 10 45 100
			M40 100 Q 45 50 50 100
			M45 100 Q 50 20 55 100
			M50 100 Q 55 40 60 100
			M55 100 Q 60 60 65 100
			M60 100 Q 65 50 70 100
			M65 100 Q 70 20 75 100
			M70 100 Q 75 45 80 100
			M75 100 Q 80 30 85 100
			M80 100 Q 85 20 90 100
			M85 100 Q 90 50 95 100
			M90 100 Q 95 25 100 100
			M95 100 Q 100 15 105 100 Z">
		</path>
	</svg>

	<div class="container cont-pad-bottom" id="content">
		<section ui-view></section>
	</div>

	<div id="loader"><md-progress-circular md-mode="indeterminate"></md-progress-circular></div>

	<script>
		var checks = <?php echo $checks; ?>
	</script>

	<script src="install_files/js/angular.js"></script>
	<script src="install_files/js/angular-ui-router.js"></script>
	<script src="install_files/js/installer.js"></script>
</body>
</html>