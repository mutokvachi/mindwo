@if ($background_file)
    body {
        /* Body fix */
    }

    body
    { 
        background: url('{{Request::root()}}/img/{{ $background_file }}') no-repeat center center fixed; 
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
    }

    .page-container 
    {
        background-color: transparent!important;   
    }            
        
    .page-content, .dx-page-container
    {
        background: {{ $content_bg_color }};
    }        

    .page-sidebar-menu span.selected {
            border-right: 12px solid {{ $content_bg_color }}!important;
    }

    @media (max-width: 991px)
    {
        body {
            background: none;
        }
    }
@endif