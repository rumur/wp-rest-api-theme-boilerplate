<?php

namespace App\Api\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * @package App\Api\Models
 * @author rumur
 */
class User extends Model {
    /**
     * @var string
     */
    protected $table = 'users';

    /**
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * @var array
     */
    protected $guarded = [
        'user_pass',
    ];

    /**
     * WordPress does not have "created_at" and "updated_at" columns.
     */
    public $timestamps = false;
}

