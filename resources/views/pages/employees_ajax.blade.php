@if (count($employees))         

    <div class="employee-list"
        data-profile-url = "{{ $profile_url }}"
        data-employees-list-id = "{{ Config::get('dx.employee_list_id') }}"     
    >           
        @foreach($employees as $item)
            @include('elements.employee', ['item' => $item, 'show_date' => 0])
        @endforeach
    </div>
    <div style='height: 20px;'>
    </div>
@else
    Nav atrasts neviens atbilstošs ieraksts
@endif
