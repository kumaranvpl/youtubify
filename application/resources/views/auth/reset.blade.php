<!DOCTYPE html>
<html>

    <head>
        <title>{{ trans('app.resetPasswordTitle')  }}</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}">
        <link href='http://fonts.googleapis.com/css?family=RobotoDraft:300,400,500,700,900' rel='stylesheet' type='text/css'>
    </head>

    <body id="reset-password-page">
        <div class="reset-container">
            <div class="panel panel-default">
                <div class="panel-heading">{{ trans('app.resetPassword')  }}</div>
                <div class="panel-body">
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <div><strong>Whoops!</strong> <span>{{ trans('app.resetErrors')  }}</span></div>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/reset') }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="input-container">
                            <label>{{ trans('app.emailAddress')  }}</label>
                            <input type="email" name="email" value="{{ old('email') }}">
                        </div>

                        <div class="input-container">
                            <label>{{ trans('app.newPassword')  }}</label>
                            <input type="password" name="password">
                        </div>

                        <div class="input-container">
                            <label>{{ trans('app.confirmPassword')  }}</label>
                            <input type="password" name="password_confirmation">
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="primary">
                                    {{ trans('app.resetPassword')  }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>