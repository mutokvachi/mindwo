    <meta charset="utf-8">
    <title>{{ $portal_name }} :: {{ isset($page_title) ? $page_title : 'Intranet' }}</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <link rel="shortcut icon" href="{{ url('favicon.ico') }}">
