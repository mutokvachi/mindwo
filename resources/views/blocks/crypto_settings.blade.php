<div class="dx-crypto-user_panel-page">  
    <h3 class="page-title"></h3>
    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-lock"></i>
                <span class="caption-subject bold uppercase">{{ trans('crypto.user_profile_title') }}</span>
            </div>
        </div>
        <div class="portlet-body">
            <div class="alert alert-success dx-crypto-generate-new-cert-info" style="{{ $has_cert ? '' : 'display: none;'  }}">
                <strong>{{ trans('crypto.help_success') }}</strong> {{ trans('crypto.help_success_text') }}
            </div>
            <div class="alert alert-warning dx-crypto-generate-cert-info" style="{{ $has_cert ? 'display: none;' : ''  }}">
                <strong>{{ trans('crypto.help_warning') }}</strong> {{ trans('crypto.help_warning_text') }}
            </div>
            <button class="btn green-jungle dx-crypto-generate-cert-btn" style="{{ $has_cert ? 'display: none;' : ''  }}">
                {{ trans('crypto.btn_generate_cert') }} <i class="fa fa-shield"></i>
            </button>    
            <button class="btn dx-crypto-generate-new-cert-btn" style="{{ $has_cert ? '' : 'display: none;'  }}">
                {{ trans('crypto.btn_generate_new_cert') }} <i class="fa fa-warning"></i>
            </button>
        </div>
    </div>
    @include('pages.crypto.modal_generate_cert')
</div>