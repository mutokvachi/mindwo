<?php
/**
 * Author:  Eugene Ostapenko <evo@olympsoft.com>
 * License: MIT
 * Created: 20.12.16, 20:47
 */

namespace App\Libraries\Blocks;

use App\Models\Offer;
use App\Models\UserOffers;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Class Block_OFFERS
 *
 * Widget that allows employees to subscribe for various events provided by company.
 *
 * @package App\Libraries\Blocks
 */
class Block_OFFERS extends Block
{
	/**
	 * Current logged in user.
	 * @var User
	 */
	protected $user;
	/**
	 * Actual offers.
	 * @var Collection
	 */
	protected $offers;
	/**
	 * Offers to which the user is subscribed.
	 * @var array
	 */
	protected $subscriptions = [];
	
	/**
	 * Render widget.
	 * @return array|\Illuminate\Contracts\Routing\ResponseFactory|string|\Symfony\Component\HttpFoundation\Response
	 */
	function getHtml()
	{
		// AJAX - return dummy JSON response
		if(Request::ajax())
		{
			$result = [
				'success' => 1
			];
			
			return response($result);
		}
		
		$result = view('blocks.widget_offers', [
			'self' => $this,
			'offers' => $this->getOffers()
		])->render();
		
		return $result;
	}
	
	/**
	 * Get current logged in user.
	 * @return mixed
	 */
	public function getUser()
	{
		if($this->user)
		{
			return $this->user;
		}
		
		$this->user = User::find(Auth::user()->id);
		
		return $this->user;
	}
	
	/**
	 * Get all actual offers (with valid dates).
	 * @return mixed
	 */
	public function getOffers()
	{
		if($this->offers)
		{
			return $this->offers;
		}
		
		$this->offers = Offer::whereDate('valid_from', '<=', date('Y-m-d'))
			->whereDate('valid_to', '>=', date('Y-m-d'))
			->orderBy('valid_to', 'desc')
			->get();
		
		return $this->offers;
	}
	
	/**
	 * Get offers for which the user is subscribed.
	 * @return array
	 */
	public function getSubscriptions()
	{
		if(!empty($this->subscriptions))
		{
			return $this->subscriptions;
		}
		
		$subs = $this->getUser()->offers;
		
		foreach($subs as $sub)
		{
			$this->subscriptions[$sub->id] = $sub;
		}
		
		return $this->subscriptions;
	}
	
	/**
	 * Check if the user is subscribed for particular offer.
	 * @param $offer
	 * @return bool
	 */
	public function isSubscribed($offer)
	{
		return isset($this->getSubscriptions()[$offer->id]);
	}
	
	/**
	 * Get quantity of quantitative offer.
	 * @param $offer
	 * @return mixed
	 */
	public function getQuantity($offer)
	{
		return $this->getSubscriptions()[$offer->id]->pivot->quantity;
	}
	
	/**
	 * Widget's JavaScript.
	 * @return string
	 */
	function getJS()
	{
		$subscribeText = trans('widgets.offers.subscribe');
		$unsubscribeText = trans('widgets.offers.unsubscribe');
		
		return <<<END
			<script>
				$(document).ready(function(){
					// Init scrollbar
					$('.widget-offers .mt-actions').slimScroll({
						height: '300px'
					});
					
					var subscribeHandler, unsubscribeHandler;
					 
					subscribeHandler = function(){
						var a = $(this);
					
						var request = {
							param: 'OBJ=OFFERS',
							action: 'subscribe',
							offerId: $(this).data('id')
						};
						
						var quantityInput = $('#offer_quantity_' + $(this).data('id'));
						
						if(quantityInput.length)
							request.quantity = quantityInput.val();
					
						show_page_splash(1);
						
						$.ajax({
							type: 'POST',
							url: DX_CORE.site_url + 'block_ajax',
							dataType: 'json',
							data: request,
							success: function(data)
							{
								if(typeof data.success != "undefined" && data.success == 0)
								{
									notify_err(data.error);
									hide_page_splash(1);
									return;
								}
								
								a.removeClass('dx-offers-subscribe')
									.addClass('dx-offers-unsubscribe')
									.unbind('click', subscribeHandler)
									.bind('click', unsubscribeHandler)
									.text('$unsubscribeText');
									
								if(quantityInput.length)
									quantityInput.prop('disabled', true);
									
								a.parents('.mt-action').addClass('subscribed');
								
								hide_page_splash(1);
							},
							error: function(jqXHR, textStatus, errorThrown)
							{
								console.log(textStatus);
								console.log(jqXHR);
								hide_page_splash(1);
							}
						});
					};
					
					unsubscribeHandler = function(){
						var a = $(this);
						
						var request = {
							param: 'OBJ=OFFERS',
							action: 'unsubscribe',
							offerId: $(this).data('id')
						};
						
						var quantityInput = $('#offer_quantity_' + $(this).data('id'));
						
						show_page_splash(1);
						
						$.ajax({
							type: 'POST',
							url: DX_CORE.site_url + 'block_ajax',
							dataType: 'json',
							data: request,
							success: function(data)
							{
								if(typeof data.success != "undefined" && data.success == 0)
								{
									notify_err(data.error);
									hide_page_splash(1);
									return;
								}
								
								a.removeClass('dx-offers-unsubscribe')
									.addClass('dx-offers-subscribe')
									.unbind('click', unsubscribeHandler)
									.bind('click', subscribeHandler)
									.text('$subscribeText');
								
								if(quantityInput.length)
									quantityInput.prop('disabled', false);
									
								a.parents('.mt-action').removeClass('subscribed');
								
								hide_page_splash(1);
							},
							error: function(jqXHR, textStatus, errorThrown)
							{
								console.log(textStatus);
								console.log(jqXHR);
								hide_page_splash(1);
							}
						});
					};
					
					// bind click handlers to buttons
					$('.widget-offers .dx-offers-subscribe').click(subscribeHandler);
					$('.widget-offers .dx-offers-unsubscribe').click(unsubscribeHandler);
				});
			</script>
END;
	}
	
	/**
	 * Widget's CSS.
	 * @return string
	 */
	function getCSS()
	{
		return <<<END
			<style>
				.widget-offers .mt-action-datetime {
					width: 50px !important;
				}
				.widget-offers .subscribed {
					background-color: #f0f8ff;
				}
			</style>
END;
	}
	
	function getJSONData()
	{
		// TODO: Implement getJSONData() method.
	}
	
	/**
	 * Subscribe for an offer.
	 * @param $offerId
	 * @param int $quantity
	 */
	protected function subscribe($offerId, $quantity = 0)
	{
		if(isset($this->getSubscriptions()[$offerId]))
		{
			return;
		}
		
		$subscription = new UserOffers;
		$subscription->user_id = $this->getUser()->id;
		$subscription->offer_id = $offerId;
		$subscription->quantity = $quantity;
		$subscription->save();
	}
	
	/**
	 * Unsubscribe from an offer.
	 * @param $offerId
	 */
	protected function unsubscribe($offerId)
	{
		UserOffers::where([
			['offer_id', '=', $offerId],
			['user_id', '=', $this->getUser()->id]
		])->delete();
	}
	
	/**
	 * Handle AJAX requests.
	 */
	protected function parseParams()
	{
		if(Request::ajax())
		{
			$action = Request::input('action');
			$offerId = Request::input('offerId');
			if($action == 'subscribe')
			{
				$quantity = Request::input('quantity', 0);
				$this->subscribe($offerId, $quantity);
			}
			elseif($action == 'unsubscribe')
			{
				$this->unsubscribe($offerId);
			}
		}
	}
}