<div class="splash" style="position: fixed; z-index: 15000012; background: white; color: gray; top: 0; bottom: 0; left: 0; right: 0;">
  <div class="color-line"></div>
  <div class="splash-title" style="text-align: center; max-width: 500px; margin: 15% auto; padding: 20px;">
    <h1 style="font-size: 26px;">{{ $portal_name }}</h1>
    <p>{{ trans("frame.data_loading") }}</p>
    <img src="{{Request::root()}}/assets/global/progress/loading-bars.svg" width="64" height="64"/>
  </div>
</div>
