<html ng-app="installer">
	<head>
		<title>Youtubify - Installation</title>

		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

		<link rel="stylesheet" href="{{ asset('assets/css/styles.min.css?v1') }}">
        <link href='http://fonts.googleapis.com/css?family=RobotoDraft:300,400,500,700,900' rel='stylesheet' type='text/css'>
	</head>

	<body id="install">

		<div class="container cont-pad-bottom" id="content">
			
			<div class="row logo"><img class="img-responsive" src="{{ asset('assets/images/logo_light.png')  }}" alt="logo"></div>

            <form id="compat-check" class="step-panel" action="{{ url('run-update') }}" method="post">
                <p>This might take several minutes, please don't close this browser tab while update is in progress.</p>

                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="center-buttons">
                    <button class="primary" type="submit">Update Now</button>
                </div>
            </form>

        </div>
	</body>
</html>
	

