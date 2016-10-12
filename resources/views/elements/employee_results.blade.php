@if (count($employees_items) > 0)
<table border="0" width="100%" class="table table-striped">
    <thead
        <tr>
            <th>Nr</th>
            <th>Vārds, uzvārds</th>
            <th>Telefons</th>
            <th>Amats</th>
            <th>Struktūrvienība</th>
            <th>E-mail</th>
        </tr>
    <thead>
    <tbody>
        @foreach($employees_items as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td><a href="{{Request::root()}}/darbinieks_{{ $item->id }}">{{ $item->display_name }}</a></td>
                <td>{{ $item->phone }}</td>
                <td>{{ $item->position }}</td>
                <td>{{ $item->department }}</td>
                <td>{{ $item->email }}</td>
            </tr>                                                
        @endforeach
    </tbody>
</table>
<div style="margin-top:10px; margin-bottom: 10px;">Ierakstu skaits: <b>{{ count($employees_items) }}</b></div>
@else
     <div class="alert alert-danger" role="alert">Nav atrasts neviens atbilstošs ieraksts pēc meklēšanas kritērija <b>{{ $criteria }}</b>.</div>
@endif