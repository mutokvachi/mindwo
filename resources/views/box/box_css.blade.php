<style>
    .slide-page-holder {
        position: absolute;
        margin-right: 20px;
    }

    #slides-container .row {
        display: none;
        position: absolute;
        left: 15px;
        right: 15px;   
    }

    #slides-container .row .folder {
        margin-bottom: 15px;
    }

    #slides-container .row .folder .folder-content {
        background: silver;
        border: 4px solid transparent;
        overflow: hidden;
        position: relative;
        height: 100px;
        cursor: pointer;
    }

    #slides-container .row .folder .folder-content:hover {
        border-color: #aaa;
    }

    #slides-container .row .folder .folder-content .folder-icon {
        position: absolute;
        left: 8px
    }

    #slides-container .row .folder .folder-content .folder-icon > i {
        margin-top: 16px;
        font-size: 56px;
        line-height: 56px;
        text-align: center;
        color: #e4e4e4;
    }

    #slides-container .row .folder .folder-content .folder-object {
        position: absolute;
        left: 76px;
        top: 24px;
        color: #4c87b9;
    }

    .page-bar .page-breadcrumb > li > a,
    .page-bar .page-breadcrumb > li > span {
        color: #888;
        font-size: 14px;
        text-shadow: none;
    }

    .page-bar {
        border-bottom: 1px solid #e7ecf1;
        background-color: #fff;
        position: relative;
        padding: 0 20px;
        padding-right: 0px;
        margin: -25px -20px 15px;
    }

    .page-bar .page-breadcrumb {
        padding: 8px 6px 6px 6px;
    }

    .page-bar .page-breadcrumb > li > i.fa-circle {
        font-size: 5px;
        margin: 0 5px;
        position: relative;
        top: -3px;
        opacity: .4;
        filter: alpha(opacity=40);
    }

    .page-bar .page-toolbar .btn-fit-height {
        border-radius: 0px;
        box-shadow: none;
    }

    @media (max-width: 991px)
    {
        .slide-page-holder {
            margin-left:6px;
            margin-right:6px;
        }

        .page-bar {
            margin:0px;
            margin-bottom:20px;
            padding-left:5px;
        }

        #slides-container .col-sm-4 {
            padding-left:20px;
            padding-right:20px;
        }
    }

    @media (max-width: 600px)
    {
        #slides-container .col-sm-4 {
            padding-left:14px;
            padding-right:14px;
        }
        
        .page-bar .tooltips {
            padding-left: 6px;
            padding-right: 2px;
        }
        .page-bar .tooltips span {
            display: none;
        }
        
        #slides-container .row .folder .folder-content .folder-icon > i {
            font-size:36px;
            line-height:36px;
            margin-top:25px;
        }

        #slides-container .row .folder .folder-content .folder-object { left:55px; }
        .folder-object h4 {
            font-size:16px;
        }
    }
    
    @media (max-width: 480px)
    {
        .page-bar .page-breadcrumb > li > a, .page-bar .page-breadcrumb > li > span { font-size: 11px}
        
        #slides-container .row .folder .folder-content { height:60px; }
        #slides-container .row .folder .folder-content .folder-icon { left:4px; }
        #slides-container .row .folder .folder-content .folder-icon > i {
            font-size:20px;
            line-height:20px;
            margin-top:15px;
        }

        #slides-container .row .folder .folder-content .folder-object { 
            left:30px;
            top: 10px;
        }
        .folder-object h4 {
            font-size:12px;
        }
        
        #slides-container .col-sm-4 {
            padding-left:12px;
            padding-right:12px;
        }
    }
</style>