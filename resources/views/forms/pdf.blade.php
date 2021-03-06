<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,700italic,400italic&subset=latin,latin-ext,cyrillic,cyrillic-ext' rel='stylesheet' type='text/css'>
        <link href="{{Request::root()}}/metronic/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <style>
        .table-form {
		border: 1px solid #ddd;
		text-align: left;
		border-collapse: collapse;
		width: 100%;
	}
	
	.table-form td {
		padding: 15px;
		border: 1px solid #ddd;
		text-align: left;
	}
        
        .table-form th {
            background-color: #dddddd;
            padding: 15px;
            border: 1px solid #c1c1c1;
            text-align: left;
        }
        
        thead, tfoot { 
                display: table-row-group; 
        }
    </style>
</head>
<body style="font-family: 'Open Sans', sans-serif;">
    <table border="0" width="100%">
        <tbody>
            <tr>
                <td valign="top">
                    <img src="{{ url(Config::get('dx.logo_print')) }}" alt="Logo" /><br>
                </td>
                <td valign="top" align="right">
                    <b>{{ $self->params->form_title }}</b><br>
                    {{ trans('form.lbl_id') }}: {{ $self->item_id }}<br>
                    <small>{{ trans('form.lbl_print_date') }}: {{ long_date(date('Y-n-d H:i:s')) }}</small>
                </td>
            </tr>
        </tbody>        
    </table>    
    <hr>
    @if (count($self->arr_data_tabs[0]) > 0)
        <h2>{{ trans('form.lbl_data') }}</h2>
        @include('forms.field_section', ['flds' => $self->arr_data_tabs[0]])
    @endif
    @foreach($self->tab_rows as $tab)        
        @if ($tab->is_custom_data)
            <h2>{{ $tab->title }}</h2>
            @include('forms.field_section', ['flds' => $self->arr_data_tabs[$tab->id]])
        @else
            <?php $htm = $self->getGridHtm($tab->grid_list_id, $tab->grid_list_field_id); ?>
            
        @if (strlen($htm) > 0)
            <h2>{{ $tab->title }}</h2>
            <table class="table-form">
                {!! $htm !!}
            </table>
        @endif
        @endif
    @endforeach
</body>
</html>