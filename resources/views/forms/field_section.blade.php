<table class="table-form">                
    <tbody>
    @foreach($flds as $fld)

                <tr>
                    <td>
                            {{ $fld['fld']->title_form }}
                    </td>
                    <td>
                            {{ $fld['val'] }}
                    </td>
                </tr>

    @endforeach
    </tbody>
</table>