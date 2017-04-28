<div class="row">
    <div class="col-md-3">
        <b>Sapulce:</b>
    </div>
    <div class="col-md-8">
        {{ long_date($agenda->meeting_time) }}
        @include('meetings.meeting_status',['meeting_row' => $agenda])
    </div>
</div>
<div class="row" style='margin-top: 10px;'>
    <div class="col-md-3">
        <b>Jautājums:</b>
    </div>
    <div class="col-md-8">
        {{ $agenda->title }}
    </div>
</div>
<hr>                        
<div class="row">
    <div class="col-md-6">
        <div class="alert alert-info" role="alert">
            <b>Lēmumi:</b>
            <br>
            <br>
            <a href='javascript:;' title='Lejupielādēt lēmuma datni'><i class='fa fa-file-word-o'></i></a>&nbsp;&nbsp;<a href='javascript:;' title='Atvērt lēmuma kartiņu' class='dx-open-decidion-link'>Lēmums par kaut ko svarīgu</a>
            <br>
            <a href='javascript:;' title='Lejupielādēt lēmuma datni'><i class='fa fa-file-word-o'></i></a>&nbsp;&nbsp;<a href='javascript:;' title='Atvērt lēmuma kartiņu' class='dx-open-decidion-link'>Lēmums otrs par kaut ko</a>
        </div>
        @if ($is_decider && $agenda->status_code == $this::AGENDA_IN_PROCESS)
            <button type='button' class='btn btn-white'><i class='fa fa-thumbs-o-down'></i>&nbsp;Balsot PRET</button>&nbsp;&nbsp;
            <button type='button' class='btn btn-primary'><i class='fa fa-thumbs-o-up'></i>&nbsp;Balsot PAR</button>        
        @endif
        
        @if ($agenda->status_code == $this::AGENDA_PROCESSED)
            <b>Balsošanas rezultāts:</b>&nbsp;&nbsp;<span class='badge badge-success'>Akceptēts</span>
            <br>
            <div><i class="fa fa-check" style="color: green;"></i>&nbsp;Jānis Bērziņš</div>
            <div><i class="fa fa-check" style="color: green;"></i>&nbsp;Oskars Kalnozols</div>
            <div><i class="fa fa-times" style="color: red;"></i>&nbsp;Janīna Segalova</div>
        @endif
    </div>
    <div class="col-md-6">

        <div class="row">
            <div class="col-md-3">
                <b>N.p.k.:</b>
            </div>
            <div class="col-md-8">
                {{ $agenda->order_index }}
            </div>
        </div>

        <div class="row" style='margin-top: 5px;'>
            <div class="col-md-3">
                <b>Reģ. nr.:</b>
            </div>
            <div class="col-md-8">
                AG-{{ $agenda->order_index }}
            </div>
        </div>

        <div class="row" style='margin-top: 5px;'>
            <div class="col-md-3">
                <b>Ziņotāji:</b>
            </div>
            <div class="col-md-8">
                Jānis Bērziņš
            </div>
        </div>

        <div class="row" style='margin-top: 5px;'>
            <div class="col-md-3">
                <b>Sagatavotāji:</b>
            </div>
            <div class="col-md-8">
                Oskars Burtnieks
            </div>
        </div>
        
        <div class="row" style='margin-top: 0px;'>
            <div class='col-md-12'>
                <hr>
                <b>Pievienotie dokumenti:</b>
                <br>
                <br>
                <a href='javascript:;' title='Lejupielādēt datni'><i class='fa fa-file-word-o'></i></a>&nbsp;&nbsp;<a href='javascript:;' title='Atvērt lēmuma kartiņu'>Informācija par jautājumu</a>
                <br>
                <a href='javascript:;' title='Lejupielādēt datni'><i class='fa fa-file-word-o'></i></a>&nbsp;&nbsp;<a href='javascript:;' title='Atvērt lēmuma kartiņu'>Plāni par jautājuma būtību</a>
            </div>
        </div>

    </div>
</div>
