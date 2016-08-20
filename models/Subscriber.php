<?php namespace SerenityNow\Subscribe\Models;

use Model;

/**
 * Model
 */
class Subscriber extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /*
     * Validation
     */
    public $rules = [
    ];

    protected $fillable = ['email'];
    /**
     * @var string The database table used by the model.
     */
    public $table = 'serenitynow_subscribe_blog_subscriptions';
}
