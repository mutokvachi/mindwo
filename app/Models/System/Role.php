<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for available system roles
 */
class Role extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'dx_roles';

    /**
     * Changes default column name for column updated_at 
     */
    const UPDATED_AT = 'modified_time';

    /**
     * Changes default column name for column created_at 
     */
    const CREATED_AT = 'created_time';

    /**
     * Relation to user model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'id', 'user_id');
    }
}
