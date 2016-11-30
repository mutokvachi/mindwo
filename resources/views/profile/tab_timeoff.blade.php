<div id="dx-emp-notes-timeoff">   
    <div class="dx-emp-timeoff-tiles row">
        @foreach ($user->timeoff() as $timeoff)
        @include('profile.control_timeoff_tile', ['timeoff' => $timeoff])
        @endforeach
    </div>
    <div>
        <table class="table table-hover" style="width: 738px;"><thead><tr><th style="text-align: right; " data-field="id" tabindex="0"><div class="th-inner sortable both">Item ID</div><div class="fht-cell" style="width: 208px;"></div></th><th style="text-align: center; " data-field="name" tabindex="0"><div class="th-inner sortable both desc">Item Name</div><div class="fht-cell" style="width: 272px;"></div></th><th style="" data-field="price" tabindex="0"><div class="th-inner sortable both">Item Price</div><div class="fht-cell" style="width: 256px;"></div></th></tr></thead></table>
    </div>
</div>