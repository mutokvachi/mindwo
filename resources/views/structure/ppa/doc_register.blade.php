@if ($list->hint)
    <p>{{ $list->hint }}</p>
@endif

@if ($list->menu_path)
    <div class="alert alert-info">
        <p><b><i class="fa fa-sitemap"></i> Piekļuve no navigācijas:</b></p><br>
        <p>Reģistrs "{{ $list->list_title }}" ir izsaucams no SVS galvenās izvēlnes: <b>{{ $list->menu_path }}</b></p>
        <br>
        <a href='{{Request::root()}}/skats_{{ $list->row_default_view->id }}' class='btn btn-primary btn-sm' target='_blank'>Uz reģistru</a>
    </div>    
@endif

@if (count($list->rows_ref_sections) > 0)
    <div class="alert alert-info">
        <p><b><i class="fa fa-pencil-square-o"></i> Piekļuve no cita reģistra datu formas:</b></p><br>
        <p>Reģistrs "{{ $list->list_title }}" ir iekļauts kā saistītais reģistrs datu formā sekojošiem reģistriem:</p>
        <ul>
            @foreach($list->rows_ref_sections as $section)
            <li><b><a href="#list_{{ $section->list_id }}">{{ $section->list_title }}</a></b>;</li> 
            @endforeach
        </ul>
    </div>
@endif

@if ($list->row_form)

    @if ($list->row_form->form_type_id == 1)
        
        <p>Reģistra datu formai "{{ $list->row_form->title }}" ir definēti sekojoši lauki:</p>
        
        <div class='{{ ($is_html) ? "" : "table-responsive" }} dx-grid-outer-div'>
            <table class='table table-bordered table-striped cf {{ ($is_html) ? "" : "table-overflow" }} table-hover'>
                <thead class='cf'>
                    <tr>
                        <th>Nosaukums</th>
                        <th>Tips</th>
                        <th>Obligāts</th>
                        <th>Apraksts</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($list->rows_form_fields as $field)
                    <tr>
                        <td>{{ $field->title_form}}</td>
                        <td>{{ $field->type_title}}{{ ($field->type_code == 'varchar' || $field->type_code == 'text') ? ' (max ' . $field->max_lenght . ')' : ''}}</td>
                        <td>{{ ($field->is_required || $field->type_title == 'ID') ? 'Jā' : 'Nē' }}</td>
                        <td>
                            @if ($field->type_code == 'rel_id' || $field->type_code == 'autocompleate' )
                                Vērtība no saistītā reģistra <a href="#list_{{ $field->rel_list_id }}">{{ $field->rel_list_title }}</a>.&nbsp;
                            @endif
                            
                            @if ($field->type_code == 'autocompleate')
                                Var pievienot uzreiz jaunu vērtību, nospiežot podziņu blakus laukam.
                            @endif
                            
                            @if ($field->default_value)
                                Noklusētā vērtība {{ $field->default_value }}.&nbsp;
                            @endif
                            
                            @if ($field->is_image_file)
                                Laukā var pievienot tikai attēlu datnes.
                            @endif
                            
                            @if ($field->is_multiple_files)
                                Laukā var pievienot vienlaicīgi 20 datnes, izmantojot arī velc un nomet iespēju (drag & drop).
                            @endif
                            
                            @if ($field->is_readonly)
                                Lauks nav rediģējams.&nbsp;
                            @endif
                                                       
                                                        
                            {{ $field->hint }}
                        </td>
                    </tr>    
                    @endforeach
                </tbody>        
            </table>
        </div>
        
        @if (count($list->rows_sections) > 0)
            <br>
            <p>Reģistra datu formai ir pieejamas sekojošas datu sadaļas ar saistītajiem reģistriem:</p>
            <ul>
                @foreach($list->rows_sections as $section)
                <li>{{ $section->section_title }} - skatīt aprakstu reģistram <b><a href="#list_{{ $section->list_id }}">{{ $section->list_title }}</a></b>;</li> 
                @endforeach
            </ul>
        @endif
        
        @if (count($list->rows_scripts) > 0)
            <br>
            <p>Reģistra datu formai ir definēta sekojoša papildus funkcionalitāte:</p>
            <ul>
                @foreach($list->rows_scripts as $script)
                <li>{{ $script->title }};</li> 
                @endforeach
            </ul>
        @endif
    @endif

@endif
