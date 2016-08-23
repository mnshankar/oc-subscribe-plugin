<?php namespace Serenitynow\Subscribe\Components;

use Cms\Classes\ComponentBase;
use October\Rain\Exception\ValidationException;
use October\Rain\Exception\ApplicationException;
use Mailchimp;
use Mailchimp_Lists;
use SerenityNow\Subscribe\Models\Settings as MailChimpSettings;

class Signup extends ComponentBase
{
    public function init()
    {
         //include Mailchimp V2 loaded via composer
        set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/vendor/mailchimp/mailchimp/src');
    }

    public function componentDetails()
    {
        return [
            'name'        => 'Signup Component',
            'description' => 'Signup to get notified on new blog posts'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    //ajax method called on subscribe
    public function onSignup()
    {
        $settings = MailChimpSettings::instance();
        if (!$settings->api_key) {
            throw new ApplicationException('MailChimp API key is not configured.');
        }
        /*
         * Validate input
         */
        $data = post();
        $rules = [
            'email' => 'required|email|min:2|max:64',
        ];
        $validation = \Validator::make($data, $rules);
        if ($validation->fails()) {
            throw new ValidationException($validation);
        }
        /*
         * Sign up to a list using Mailchimp API
         */
        $api = new Mailchimp($settings->api_key);
        $lists = new Mailchimp_Lists($api);
        $this->page['error'] = null;
        $mergeVars = '';
        if (isset($data['merge']) && is_array($data['merge']) && count($data['merge'])) {
            $mergeVars = $data['merge'];
        }
        try {
            $lists->subscribe($settings->list_id,
                array('email'=>post('email')),
                $mergeVars,
                'html',
                true,  //double_optin.. avoids spam emails from being subscribed
                false,  //update_existing
                false); //replace_interests
        } catch (\Mailchimp_Error $e) {
            $this->page['error'] = $e->getMessage();
        }
    }
}
