<div class="portlet light">
    <div class="portlet-body">
        <div class="row">
            <div class="col-md-12">
                <div style="margin-bottom: 10px;">
                    <span class="caption-subject font-dark bold uppercase">
                        Tuvākās nodarbības
                    </span>
                    <a class="pull-right" href="javascript:;">
                        Skatīt visu
                    </a>
                </div>


                <table class="fpGraf">
                    <tbody>
                    @foreach($schedule as $s)
                        <tr>
                            <td class="fgDate">
                                <b>{{ $s['day'] }}</b><br>
                                {{ $s['month'] }}
                            </td>
                            <td>
                                <table class="fpKurs">
                                    <tbody>
                                    @foreach($s['groups'] as $g)
                                        <tr>
                                            <td class="fpKname">
                                                <a href="#">
                                                    {{ $g['title'] }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fpKdat">
                                                {{ $g['time_from'] . ' - ' . $g['time_to'] }}
                                            </td>

                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>

                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
