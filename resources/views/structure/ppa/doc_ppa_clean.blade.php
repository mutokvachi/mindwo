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
    
    <div class='portlet'>
        <div class='portlet-title'>
            <div class="caption font-grey-cascade uppercase">Programmatūras Projektējuma Apraksts</div>                
        </div>
        <div class='portlet-body'>
            
            @foreach($begin_pages as $page)
                {!! $page->formated_description !!}
            @endforeach
            
            <h1>Datu atkarības</h1>
            @foreach($components_rows as $component)                
                @if ($component->is_generate)
                    <p>{{ $component->title }}:</p>
                    <ul>
                        @foreach($component->rows_modules as $module)
                            <li>{{ $module->title }}:
                                <ul>
                                @foreach($module->rows_groups as $group)
                                    <li>{{ $group->title }}:
                                        <ul>
                                        @foreach($group->rows_lists as $key => $list)
                                            @if (count($list->rows_uses_lists) || count($list->rows_used_by_lists))
                                                <li><a href='#list_{{ $list->id }}'>{{ $list->list_title }}:</a>
                                                    <ul>
                                                    @if (count($list->rows_uses_lists))

                                                        <li>Izmanto saistītos datus no reģistriem:
                                                            <ul>
                                                                @foreach($list->rows_uses_lists as $use)
                                                                    <li><a href='#list_{{ $use->list_id }}'>{{ $use->list_title }}</a>;</li>
                                                                @endforeach
                                                            </ul>
                                                        </li>
                                                    @endif

                                                    @if (count($list->rows_used_by_lists))
                                                        <li>Šī reģistra datus izmanto:
                                                            <ul>
                                                                @foreach($list->rows_used_by_lists as $use)
                                                                    <li><a href='#list_{{ $use->list_id }}'>{{ $use->list_title }}</a>;</li>
                                                                @endforeach
                                                            </ul>
                                                        </li>
                                                    @endif
                                                    </ul>
                                                </li>
                                            @endif
                                        @endforeach
                                        </ul>
                                    </li>
                                @endforeach
                                </ul>
                            </li>
                        @endforeach
                    </ul>
                @endif            
            @endforeach
            
            <h1>Saskarnes apraksts</h1>
            @foreach($components_rows as $component)
                <h2>{{ $component->title }}</h2>
                {!! $component->html_interface !!}
            @endforeach
            
            <h1>Komponenšu detalizēts projektējums</h1>
            
            @foreach($components_rows as $component)
                <h2>{{ $component->title }}</h2>
                @if ($component->is_generate)
                    @foreach($component->rows_modules as $module)
                        <h3>{{ $module->title }}</h3>
                        @foreach($module->rows_groups as $group)
                            <h4>{{ $group->title }}</h4>
                            @foreach($group->rows_lists as $key => $list)
                                <h5 id="list_{{ $list->id }}">{{ $list->list_title }}</h5>
                                @include('structure.ppa.doc_register', ['list' => $list, 'is_html' => 1])
                            @endforeach
                        @endforeach
                    @endforeach
                @else
                    {!! $component->html_description !!}
                @endif            
            @endforeach
            
            <h1>Piekļuves tiesības</h1>
            
            @foreach($list_roles_rows as $list_role)
                @if (count($list_role->rows_lists) || count($list_role->rows_pages))
                    <h2>{{ $list_role->title }}</h2>
                    <p>{{ $list_role->description }}</p>

                    @if (count($list_role->rows_lists))
                        <h3>Tiesības uz reģistriem</h3>
                        <p>Tabulā norādīti reģistri, uz kuriem lietotājiem no attiecīgās lomas ir piekļuves tiesības:</p>
                        <div class='dx-grid-outer-div'>
                            <table class='table table-bordered table-striped cf table-hover'>
                                <thead class='cf'>
                                    <tr>
                                        <th>Reģistrs</th>
                                        <th>Skatīt</th>
                                        <th>Jauns ieraksts</th>
                                        <th>Labošana</th>
                                        <th>Dzēšana</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($list_role->rows_lists as $key => $list)
                                    <tr>
                                        <td><a href="#list_{{ $list->list_id }}">{{ $list->list_title}}</a></td>
                                        <td>Jā</td>
                                        <td>{{ ($list->is_new_rights) ? "Jā" : "Nē" }}</td>
                                        <td>{{ ($list->is_edit_rights) ? "Jā" : "Nē" }}</td>
                                        <td>{{ ($list->is_delete_rights) ? "Jā" : "Nē" }}</td>
                                    </tr>    
                                    @endforeach
                                </tbody>        
                            </table>
                        </div>
                    @endif
                    
                    @if (count($list_role->rows_pages))
                        <h3>Tiesības uz lapām</h3>
                        <p>Lietotājiem no attiecīgās lomas ir piekļuves tiesības uz sekojošām lapām:</p>
                        <ul>
                            @foreach($list_role->rows_pages as $key => $page)
                            <li>{{ $page->title}};</li>    
                            @endforeach
                        </ul>
                    @endif
                    
                    @if (count($list_role->rows_specs))
                        <h3>Tiesības uz speciālām funkcijām</h3>
                        <p>Lietotājiem no attiecīgās lomas ir piekļuves tiesības uz sekojošu funkcionalitāti:</p>
                        <ul>
                            @foreach($list_role->rows_specs as $key => $spec)
                            <li>{{ $spec->title}} - {{ $spec->description }};</li>    
                            @endforeach
                        </ul>
                    @endif
                
                @endif
            @endforeach            
            
            @foreach($end_pages as $page)
                {!! $page->formated_description !!}
            @endforeach
            
        </div>
    </div>

</body>
</html>