<div class='modal fade dx-popup-modal dx-publish-popup' aria-hidden='true' role='dialog' aria-hidden='true' data-backdrop='static' style="z-index: 999999;">
    <div class='modal-dialog modal-lg'>
        <div class='modal-content'>
            @include('elements.form_header',['form_title' => trans('calendar.scheduler.publish_popup_title'), 'badge' => '', 'form_icon' => '<i class="fa fa-globe"></i>'])

            <div class='modal-body' style="overflow-y: auto; max-height: 500px;">
                Publicējot grupas, tiks nosūtītas e-pastu notifikācijas grupu pasniedzējiem, dalībniekiem un atbalsta personālam un informācija būs pieejama visiem iesaistītajiem lietotājiem MPS portālā.
                <br><br>
                Pirms grupu publicēšanas, sistēma pārbaudīs publicējamo datu korektību, t.i., lai izpildās sekojoši nosacījumi:
                <ul>
                    <li>Visām nodarbībām ir norādīts vismaz viens pasniedzējs;</li>
                    <li>Visām grupām ir norādīta vismaz viena nodarbība;</li>
                    <li>Ja kādai nodarbībai vairāki pasniedzēji, tad nepārklājās pasniedzēju laiki;</li>
                    <li>Grupai norādītais mācību pasākums, modulis un programma ir publicēti;</li>
                    <li>Grupas vietu limits nepārsniedz vietu limitu telpās, kurās notiek nodarbības;</li>
                    <li>Nepārklājās dažādu grupu nodarbību laiki kādā no telpām;</li>
                    <li>Nepārklājās grupas nodarbības laiks telpā, kura tiek izmantota tajā pašā dienā kafijas pauzēm;</li>
                    <li>Visām kafijas pauzēm ir norādīti pakalpojumu sniedzēji;</li>
                    <li>Grupās, kurās dalībnieki paši nevar pieteikties (tikai ar uzaicinājumu), dalībnieku kopējais skaits pa uzaicināmajām iestādēm ir vienāds ar grupu vietu limitu;</li>
                    <li>Grupās, kurās dalībnieki paši nevar pieteikties (tikai ar uzaicinājumu), ir aizpildītas vismaz 50% vietas;</li>
                    <li>Grupās, kurās dalībnieki paši nevar pieteikties (tikai ar uzaicinājumu), dalībnieku skaits nepārsniedz grupas vietu limitu;</li>
                </ul>
                <br>
                <br>
                <b>Publicējamo grupu skaits: </b><span class="dx-total-groups">10</span>
            </div>
            <div class='modal-footer'>                
                <button type='button' class='btn btn-primary dx-check-publish-btn'>{{ trans('calendar.scheduler.btn_check_publish') }}</button>                
                <button type='button' class='btn btn-white' data-dismiss='modal'>{{ trans('form.btn_cancel') }}</button>                            
            </div>
        </div>
    </div>
</div>