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
    <div class="form-group col-lg-4 col-md-5 col-sm-6">
        <div style="font-size:11px;">Meklēšanas frāze</div>
        <input class="form-control input-sm" type="text" maxlength="500" value=""/>  
    </div>
    <div class="form-group col-lg-4 col-md-5 col-sm-6">
        <div style="font-size:11px;">&nbsp;</div>
        <button class="btn btn-sm btn-primary">Meklēt</button>
        <button class="btn btn-sm dx-edu-catalog-btn-filter-detailed" data-toggle="collapse" data-target="#dx-edu-catalog-filter-detailed">
            Paplašināta meklēšana <i class="fa fa-caret-down"> </i>
        </button>
    </div>
</div>
<div id="dx-edu-catalog-filter-detailed" class="row collapse">
    <div class="form-group col-lg-3 col-md-4 col-sm-6 dx-edu-multiselect-container">
        <div style="font-size:11px;">Joma</div>
        <select class='form-control input-sm mt-multiselect' multiple="multiple" data-label="left">         
            <option value="1">Valoda</option>
            <option value="2">Nodokļi</option>
        </select>  
    </div>
    <div class="form-group col-lg-3 col-md-4 col-sm-6 dx-edu-multiselect-container">
        <div style="font-size:11px;">Programma</div>
        <select class='form-control input-sm mt-multiselect' multiple="multiple" data-label="left">          
            <option value="1">Programma</option>
        </select>  
    </div>
    <div class="form-group col-lg-3 col-md-4 col-sm-6 dx-edu-multiselect-container">
        <div style="font-size:11px;">Modulis</div>
        <select class='form-control input-sm mt-multiselect' multiple="multiple" data-label="left">     
            <option value="1">Modulis</option>
        </select> 
    </div>
    <div class="form-group col-lg-3 col-md-4 col-sm-6 dx-edu-multiselect-container">
        <div style="font-size:11px;">Pasniedzējs</div>
        <select class='form-control input-sm mt-multiselect' multiple="multiple" data-label="left">
            <option value="1">Valērija Egle</option>
            <option value="2">Zandis Ezers</option>
        </select>  
    </div>
    <div class="form-group col-lg-3 col-md-4 col-sm-6">
        <div style="font-size:11px;">Datums</div>
        <div class='input-group dx-datetime'>
            <input class='form-control dx-edu-datetime-field input-sm' type="text"/>
            <span class='input-group-btn'>
                <button type='button' class='btn btn-white btn-sm' style="border: 1px solid #c2cad8!important; margin-right: -2px!important;"
                    onclick="javascript:$('.dx-edu-datetime-field').click();">
                    <i class='fa fa-calendar'></i>
                    </button>
            </span>
        </div>
        <span class="glyphicon form-control-feedback" aria-hidden="true"></span> 
    </div>
    <div class="clearfix visible-md"></div>
    <div class="form-group col-lg-2 col-md-3 col-sm-3">
        <div style="font-size:11px;">Laiks no</div>
        <div class="input-group">
            <input class="form-control input-sm dx-edu-time-field" type="text"/>
            <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
        </div>
    </div>
    <div class="form-group col-lg-2 col-md-3 col-sm-3">
        <div style="font-size:11px;">Laiks līdz</div>
        <div class="input-group">
            <input class="form-control input-sm dx-edu-time-field" type="text"/>
            <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
        </div>
    </div>   
    <div class="form-group col-lg-1 col-md-2 col-sm-3">
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
    <div class="form-group col-lg-1 col-md-2 col-sm-3">
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
    <div class="form-group col-lg-1 col-md-2 col-sm-3">
        <div style="font-size:11px;">Rādīt tikai aktīvos</div>
        <div>
            <input
                type="checkbox" 
                class="dx-bool"                 
                data-size="small"
                data-off-text="Nē" 
                data-on-text="Jā" />
        </div> 
    </div>
    <div class="form-group col-xs-12 visible-xs">
        <button class="btn btn-sm btn-primary">Meklēt</button>
        <button class="btn btn-sm dx-edu-catalog-btn-filter-detailed" data-toggle="collapse" data-target="#dx-edu-catalog-filter-detailed">
            Paplašināta meklēšana <i class="fa fa-caret-down"> </i>
        </button>
    </div>
 </div>
 <div style='border-bottom:1px solid gray; margin-top:10px; margin-bottom:10px;'>
 </div>