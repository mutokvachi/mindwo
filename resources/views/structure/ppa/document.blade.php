<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,700italic,400italic&subset=latin,latin-ext,cyrillic,cyrillic-ext' rel='stylesheet' type='text/css'>
        <link href="{{Request::root()}}/metronic/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <style>
            body {
                counter-reset: sec1;
            }

            h1 {
                counter-reset: sec2;
            }

            h1:before {
                counter-increment: sec1;
                content: counter(sec1) ". ";
            }

            h2:before {
                counter-increment: sec2;
                content: counter(sec1) "." counter(sec2) ". ";
            }

            h2 {
                counter-reset: sec3;
            }

            h3:before {
                counter-increment: sec3;
                content: counter(sec1) "." counter(sec2) "." counter(sec3) ". ";
            }

            h3 {
                counter-reset: sec4;
            }

            h4:before {
                counter-increment: sec4;
                content: counter(sec1) "." counter(sec2) "." counter(sec3) "." counter(sec4) ". ";
            }

            h4 {
                counter-reset: sec5;
            }

            h5:before {
                counter-increment: sec5;
                content: counter(sec1) "." counter(sec2) "." counter(sec3) "." counter(sec4) "." counter(sec5) ". ";
            }     
            
            thead, tfoot { 
                display: table-row-group; 
            }
           
        </style>
    </head>
    <body style="font-family: 'Open Sans', sans-serif;">        
        {!! $html !!}
    </body>
</html>