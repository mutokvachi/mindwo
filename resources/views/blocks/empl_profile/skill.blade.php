<div class="alert alert-info" role="alert" style="margin-top: 30px;">There are no skills added jet.
    @if ($empl_row->id == Auth::user()->id)
        <br><br>
        Add skills so your team get noticed how you can help to grow.<br><br>
        <a class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add skill </a><br>
    @endif
</div>