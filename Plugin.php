<?php namespace SerenityNow\Subscribe;

use Mailchimp;
use SerenityNow\Subscribe\Models\Settings as MailChimpSettings;
use System\Models\MailSetting;
use System\Models\MailTemplate;
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
        $contentAndSubject = $this->getContentAndSubject('serenitynow.subscribe::mail.email', ['post' => $post]);
        $options = [
            'list_id'    => MailChimpSettings::get('list_id'),
            'subject'    => $contentAndSubject['subject'],
            'from_name'  => MailSetting::get('sender_name'),
            'from_email' => MailSetting::get('sender_email'),
        ];
        /*
         * Mailchimp API V2.0
         */
        try {
            $mailchimp = new Mailchimp(MailChimpSettings::get('api_key'));
            $campaign = $mailchimp->campaigns->create('regular', $options, array('html' => $contentAndSubject['html']));
            $mailchimp->campaigns->send($campaign['id']);
        } catch (\MailChimp_Error $e) {
            throw new \Exception('MailChimp returned the following error: ' . $e->getMessage());
        }
    }
    
    /**
     * @param $code
     * @param $data
     *
     * @return array('html'=>,'subject'=>)
     * @throws \Exception
     * Helper function to extract content and subject from the backend mail template
     */
    private function getContentAndSubject($code, $data)
    {
        try {
            $template = MailTemplate::whereCode($code)->firstOrFail();

            $html = \Twig::parse($template->content_html, $data);
            if ($template->layout) {
                $html = \Twig::parse($template->layout->content_html, [
                        'content' => $html,
                        'css'     => $template->layout->content_css
                    ] + (array)$data);
            }
            return ['html' => $html, 'subject' => $template->subject];
        } catch (\Exception $ex) {
            throw new \Exception("Error accessing Email template: $code. Please refresh plugin and retry");
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
