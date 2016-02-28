<html ng-app="installer">
	<head>
		<title>Youtubify - Installation</title>

		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

		<link rel="stylesheet" href="{{ asset('assets/css/styles.min.css?v1') }}">
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

		<div class="container cont-pad-bottom" id="content">
			
			<div class="row logo"><img class="img-responsive" src="{{ asset('assets/images/logo_light.png')  }}" alt="logo"></div>

			<section class="row">
				<ul class="wizard">
					<li ng-class="{ active: currentStep === 'compatability'}" class="compat"><span class="step">1</span> <span class="title">Compatability</span></li>
					<li ng-class="{ active: currentStep === 'database'}" class="db"><span class="step">2</span> <span class="title">Database</span></li>
					<li ng-class="{ active: currentStep === 'admin'}" class="config"><span class="step">3</span> <span class="title">Admin Account</span></li>
				</ul>
			</section>

			<section ui-view></section>

		</div>

		<div id="loader"><md-progress-circular md-mode="indeterminate"></md-progress-circular></div>

        <script src="{{ asset('assets/js/install.min.js') }}"></script>
	</body>
</html>
	

