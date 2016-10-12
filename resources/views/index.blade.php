<!DOCTYPE html>
<html>

    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ trans('index.site_title') }}</title>

        <link rel="shortcut icon" href="{{Request::root()}}/favicon.ico">

        <link href="{{Request::root()}}/css/bootstrap.min.css" rel="stylesheet">

    </head>

    <body>
        @if ($error)
        <div class="container" style='margin-top: 40px;'>        
            <div class="alert alert-danger">
                {{ $error }}
            </div>
        </div>
        @endif

        @if (count($errors) > 0)
        <div class="container" style='margin-top: 40px;'>
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <div class="text-center" style="max-width: 300px; width: 300px; margin: 0 auto; margin-top: 100px;">       
            <div>
                <img src="{{ trans('index.logo') }}" alt="LOGO" />
            </div>
            <h3>{{ trans("index.hello_title") }}</h3>
            <p>{{ trans("index.about_title") }}</p>
            
            <form name="frmLogin" id="frmLogin" method="post" action="{{Request::root()}}/login">
                <div class="form-group has-feedback">
                    <input type="text" class="form-control" placeholder="{{ trans("index.placeholder_user_name") }}" name="user_name" id ="user_name" required maxlength='100' data-minlength="3" data-error="{{ trans("index.user_name_data_error") }}">
                    <div class="help-block with-errors"></div>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" class="form-control" placeholder="{{ trans("index.placeholder_password") }}" name="password" id="password" required maxlength='100' data-minlength="8" data-error="{{ trans("index.password_data_error") }}">
                    <div class="help-block with-errors"></div>
                </div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
            </form>

            <button type="button" class="btn btn-primary block full-width m-b" style="margin-bottom: 10px; background-color: #243c68;" id="btnLogin">{{ trans("index.login") }}</button>           
            
            <p class="m-t"> <small>{{ trans("index.version") }}</small> </p>
            
        </div>

        @include('elements.progress')

        <!-- Mainly scripts -->
        <script src="{{Request::root()}}/js/jquery-2.1.1.js"></script>
        <script src="{{Request::root()}}/js/bootstrap.min.js"></script>

        <!-- Validator -->
        <script src="{{Request::root()}}/plugins/validator/validator.js"></script>

        <script>
        $(document).ready(function() {
            $("#user_name").focus();

            $('#frmLogin').validator({
                feedback: {
                    success: 'glyphicon-ok',
                    error: 'glyphicon-alert'
                }
            });

            $('#frmLogin').validator().on('submit', function(e) {
                if (e.isDefaultPrevented())
                {
                    $("#password").focus();
                    return false;
                }
                else
                {
                    // autorizācijas dati ir korekti ievadīti
                    show_progres_result("{{ trans("index.authorization_in_system") }}", "<img src='{{Request::root()}}/assets/global/progress/loading.gif' alt='{{ trans("index.please_wait") }}' title='{{ trans("index.wait_please") }}' /> {{ trans("index.authorizing") }}", "{{ trans("index.cancel") }}");
                    $('#progres_window').modal('show');

                    return true;
                }
            })

            $("#password").keypress(function(e) {
                if (e.which == 13) {
                    $("#frmLogin").submit();
                }
            });

            $("#btnLogin").click(function(event) {
                event.stopPropagation();
                $("#frmLogin").submit();
            });
        });
        </script>
    </body>

</html>
