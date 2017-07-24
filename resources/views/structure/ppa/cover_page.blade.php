<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,700italic,400italic&subset=latin,latin-ext,cyrillic,cyrillic-ext' rel='stylesheet' type='text/css'>
        <style>
            .parent {
                position: relative;
                height: 100%;
            }              
            .child {
                width: 600px;
                height: 500px;
                padding: 20px;

                position: absolute;
                top: 50%;
                left: 50%;

                margin: -270px 0 0 -320px;
            }
            
            .child p {
                text-align: center;
                padding-top: 15px;
            }
            
            p.title {
                font-size: 40px;
            }
            
            p.title2 {
                font-size: 30px;
            }
        </style>
    </head>
    <body style="font-family: 'Open Sans', sans-serif;">
        <div class='parent'>
            <div class='child'>
                <p class='title'>{{ $title }}</p>
                <p class='title2'>{{ trans('ppa.sub_title') }}</p>
                <p>{{ trans('ppa.lbl_version') }}: {{ date('d.m.Y') }}</p>
                <p>{{ trans('ppa.lbl_author') }}: {{ $author }}</p>
                <p><small>{{ trans('ppa.lbl_generated') }}</small></p>
            </div>
        </div>
    </body>
</html>