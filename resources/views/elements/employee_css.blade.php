<link href="{{Request::root()}}/css/pages/search_tools.css" rel="stylesheet" type="text/css" />
<link href="{{Request::root()}}/metronic/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
<link href="{{Request::root()}}/plugins/tree/themes/default/style.min.css" rel="stylesheet" type="text/css" />
<style>
    .employee-list {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .employee-panel img {
        width: 120px;
        border-radius: 2px;
        box-shadow: 0 1px 1px rgba(0,0,0,.15)!important;
    }

    .employee-panel .employee-details-1 .well {
        padding: 15px;
        margin: 0;
        height: 160px;
    }

    .employee-panel .employee-details-1 .well h4{
        font-weight: 400;
        margin-top: 0;
        color: #333;
    }

    .employee-panel .employee-details-2 {
        margin-left: 15px;
    }

    .employee-pic-box .label {
        border-radius: 4px!important;
        -webkit-box-shadow: 0 1px 1px rgba(0,0,0,.15)!important;
        box-shadow: 0 1px 1px rgba(0,0,0,.15)!important;
        text-transform: uppercase;
        font-size: 10px;
    }

    @media (max-width: 990px)
    {
        .employee-panel .employee-pic-box
        {
            margin-bottom: 20px;
        } 

        .employee-panel .employee-details-1 {
            margin-bottom: 10px;
        }

    }

    @if (!$is_advanced_filter)

    .dx_position_link, .dx_department_link, .dx_cabinet_link, .dx_manager_link, .dx_substit_link {
        cursor: default!important;
        text-decoration: none!important;
        color: #333!important;
    }

    @endif

</style>