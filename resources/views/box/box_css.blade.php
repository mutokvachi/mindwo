<style>
    #slide-page-holder {
        position: absolute;
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
</style>