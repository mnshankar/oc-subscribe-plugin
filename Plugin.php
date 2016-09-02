<?php namespace SerenityNow\Subscribe;

use Mailchimp;
use SerenityNow\Subscribe\Models\Settings as MailChimpSettings;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public $require = ['RainLab.Blog'];

    public function boot()
    {
        //include Mailchimp V2 loaded via composer
        set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/vendor/mailchimp/mailchimp/src');

        \Event::listen(['eloquent.updating: RainLab\Blog\Models\Post',
            'eloquent.creating: RainLab\Blog\Models\Post'], function ($post) {
            //send email when the post status is changed from unpublished to published
            $original = $post->getOriginal();
            if (count($original)) {    //updating
                if (($original['published'] == 0) && ($post->published == 1)) {
                    $this->sendEmail($post);
                }
            } else {   //creating
                if ($post->published == 1) {
                    $this->sendEmail($post);
                }
            }
        });
    }

    private function sendEmail($post)
    {
        $params = [
            'blog_title'   => $post->title,
            'blog_content' => $post->content_html,
        ];
        $settings = MailChimpSettings::instance();
        $options = [
            'list_id'    => $settings->list_id,
            'subject'    => 'New Blog Published',
            'from_name'  => \System\Models\MailSettings::get('sender_name'),
            'from_email' => \System\Models\MailSettings::get('sender_email'),
        ];
        /*
         * Mailchimp API V2.0
         */
        try{ 
            $mailchimp = new Mailchimp($settings->api_key);
            $content = \View::make('serenitynow.subscribe::mail.email', $params)->render();
            $campaign = $mailchimp->campaigns->create('regular', $options, array('html' => $content));
            $mailchimp->campaigns->send($campaign['id']);
        }
        catch (\MailChimp_Error $e)
        {
            throw new \Exception('MailChimp returned the following error: '.$e->getMessage());
        }
    }

    public function registerMailTemplates()
    {
        return [
            'serenitynow.subscribe::mail.email' => 'E-mail Sent To Subscribers',
        ];
    }

    public function registerComponents()
    {
        return [
            'SerenityNow\Subscribe\Components\Signup' => 'Signup',
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'category'    => 'Blog',
                'label'       => 'MailChimp Subscription',
                'icon'        => 'icon-envelope',
                'description' => 'On Blog Publish, Send Email to All Subscribers',
                'class'       => 'SerenityNow\Subscribe\Models\Settings',
                'order'       => 500
            ]
        ];
    }
}
