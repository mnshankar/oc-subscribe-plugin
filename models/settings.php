<?php namespace SerenityNow\Subscribe\Models;

use October\Rain\Database\Model;

/**
 * Modifiy mailchip settings model to add a list id field
 *
 * @package system
 * @author Alexey Bobkov, Samuel Georges
 *
 */
class Settings extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'serenitynow_mailchimp_api_listid_settings';

    public $settingsFields = 'fields.yaml';

    /**
     * Validation rules
     */
    public $rules = [
        'api_key' => 'required',
        'list_id' => 'required',
    ];
}
