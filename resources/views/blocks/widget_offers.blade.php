@if  (count($offers) > 0)
<div class="portlet widget-offers" dx_block_id="offers">
  <div class="portlet-title">
    <div class="caption font-grey-cascade uppercase">{{ trans('widgets.offers.title') }}
      <span class="badge badge-success">{{ count($offers) }}</span></div>
    <div class="tools">
      <a class="collapse" href="javascript:;"> </a>
    </div>
  </div>
  <div class="portlet-body">
    <div class="mt-actions">
      @foreach($offers as $offer)
        <div class="mt-action {{ $self->isSubscribed($offer) ? 'subscribed' : '' }}">
          <div class="mt-action-body">
            <div class="mt-action-row">
              <div class="mt-action-info">
                <div class="mt-action-details">
                  <span class="mt-action-author">{{ $offer->title }}</span>
                  <div class="mt-action-desc">
                    {{ $offer->description }}
                  </div>
                </div>
              </div>
              
              <div class="mt-action-buttons">
                  @if($offer->quantitative == 1)
                  {{-- If offer is quantitative, display an input field --}}
                  <input id="offer_quantity_{{ $offer->id }}" type="text" class="offer-quantity form-control"
                    value="{{ $self->isSubscribed($offer) ? $self->getQuantity($offer) : '1' }}"
                    {{ $self->isSubscribed($offer) ? 'disabled' : '' }} style="width: 40px; margin: 0 auto;"><br>
                @endif
                @if($self->isSubscribed($offer))
                  {{-- Unsubscribe button --}}
                  <a class="btn btn-primary green btn-xs dx-offers-unsubscribe" data-id="{{ $offer->id }}" href="javascript:;">
                    {{ trans('widgets.offers.unsubscribe') }}
                  </a>
                @else
                  {{-- Subscribe button --}}
                  <a class="btn btn-primary green btn-xs dx-offers-subscribe" data-id="{{ $offer->id }}" href="javascript:;">
                    {{ trans('widgets.offers.subscribe') }}
                  </a>
                @endif
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>
@endif