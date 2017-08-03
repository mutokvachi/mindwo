<!--
    1) Joma (tagu klasifikators)
2) Datums (vai intervāls)
3) Programma
4) Modulis
5) Pasniedzējs ?
6) Teksta (meklēšanas frāze)
7) Maksas/Bezmaksas
8) Pulkstenis (nu mani interesētu kursi kas notiek pa vakariem piemēram..)
9) Vēl varētu filtrēt lai nerāda pilnās grupas..
 -->
 <div class='row'>
    <div class="form-group col-lg-4">
        <div style="font-size:11px;">Nosaukums</div>
        <input class="form-control input-sm" type="text" maxlength="500" value=""/>  
    </div>
</div>
<div class='row'>
    <div class="form-group col-lg-3">
        <div style="font-size:11px;">Joma</div>
        <select class='form-control input-sm'>  
            <option value="" selected>Visas</option>         
            <option value="1">Valoda</option>
            <option value="2">Nodokļi</option>
        </select>  
    </div>
    <div class="form-group col-lg-3">
        <div style="font-size:11px;">Programma</div>
        <select class='form-control input-sm'> 
            <option value="" selected>Visas</option>          
            <option value="1">Programma</option>
        </select>  
    </div>
    <div class="form-group col-lg-3">
        <div style="font-size:11px;">Modulis</div>
        <select class='form-control input-sm'>           
            <option value="" selected>Visi</option>
            <option value="1">Modulis</option>
        </select> 
    </div>
    <div class="form-group col-lg-3">
        <div style="font-size:11px;">Pasniedzējs</div>
        <select class='form-control input-sm'>           
            <option value="" selected>Visi</option>
            <option value="1">Valērija Egle</option>
            <option value="2">Zandis Ezers</option>
        </select>  
    </div>
</div>
<div class='row'>
    <div class="form-group col-lg-3">
        <div style="font-size:11px;">Datums</div>
        <div class='input-group dx-datetime'>
        <span class='input-group-btn'>
            <button type='button' class='btn btn-white btn-sm dx-datetime-cal-btn' style="border: 1px solid #c2cad8!important; margin-right: -2px!important;"><i class='fa fa-calendar'></i></button>
        </span>
        <input class='form-control dx-datetime-field input-sm' type="text"/>
    </div>
    <span class="glyphicon form-control-feedback" aria-hidden="true"></span> 
    </div>
    <div class="form-group col-lg-3">
        <div style="font-size:11px;">Laiks no</div>
        <input class="form-control input-sm" type="text" maxlength="500" value=""/>  
    </div>
    <div class="form-group col-lg-3">
        <div style="font-size:11px;">Laiks līdz</div>
        <input class="form-control input-sm" type="text" maxlength="500" value=""/>  
    </div>   
    <div class="form-group col-lg-1">
        <div style="font-size:11px;">Rādīt tikai bezmaksas</div>
        <div>
            <input
                type="checkbox" 
                class="dx-bool" 
                data-size="small"
                data-off-text="Nē" 
                data-on-text="Jā" />
        </div>
    </div>
    <div class="form-group col-lg-1">
        <div style="font-size:11px;">Rādīt pilnās grupas</div>
        <div>
            <input
                type="checkbox" 
                class="dx-bool" 
                checked
                data-size="small"
                data-off-text="Nē" 
                data-on-text="Jā" />
        </div> 
    </div>
 </div>
 <div style='border-bottom:1px solid gray; margin-top:10px; margin-bottom:10px;'>
 </div>