<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserOffers
 *
 * Model for pivotal table, that implements many-to-many relation for users' subscriptions for offers.
 *
 * @package App\Models
 */
class UserOffers extends Model
{
	/**
	 * Changes default column name for column updated_at
	 */
	const UPDATED_AT = 'modified_time';
	/**
	 * Changes default column name for column created_at
	 */
	const CREATED_AT = 'created_time';
	protected $table = 'dx_users_offers';
}
