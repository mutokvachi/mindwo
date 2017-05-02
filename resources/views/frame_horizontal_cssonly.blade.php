<!DOCTYPE html>
<html>
  <head>
    @include('main.head_meta')
    @include('main.head_styles')
  </head>
  <body
    class="dx-main-page dx-horizontal-menu-ui dx-cssonly {{ (isset($page_is_full_height) && $page_is_full_height) ? 'dx-page-full-height' : '' }}"
    @include('main.body_attributes')
  >
    <div id="dx-wrap">
      @include('main.splash')
      @include('main.cssonly.header')
      @include('main.cssonly.menu')
      @include('main.content')
      @include('main.scroll_top')
      @include('elements.popup_info')
    </div>
    @include('main.modal_dialog')
    @include('main.modal_dialog_crypto_psw')
    @include('main.body_scripts')
  </body>
</html>