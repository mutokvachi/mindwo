@if (count($docs_items) > 0)
<table border="0" width="100%" class="table table-striped">
    <thead
        <tr>
            <th>Datne</th>
            <th>Numurs</th>
            <th>Datums</th>
            <th>Veids</th>
            <th>Nosaukums</th>
        </tr>
    <thead>
    <tbody>
        @foreach($docs_items as $item)
            <tr>
                <td title="{{ $item->file_name }}"><a href="{{Request::root()}}/img/{{ $item->file_guid }}"><i class="fa fa-file"></i></a></td>
                <td>{{ $item->doc_nr }}</td>
                <td>
                   {{!! format_event_time($item->doc_date) !!}
                </td>
                <td>
                  {{ $item->doc_kind }}
                </td>
                <td>{{ $item->doc_title }}</td>
            </tr>                                                
        @endforeach
    </tbody>
</table>
@else
    <div class="alert alert-danger" role="alert">Nav atrasts neviens atbilstošs ieraksts pēc meklēšanas kritērija <b>{{ $criteria }}</b>.</div>
@endif