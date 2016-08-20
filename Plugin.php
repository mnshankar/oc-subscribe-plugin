<?php namespace SerenityNow\Subscribe;

use System\Classes\PluginBase;
use SerenityNow\Subscribe\Models\Subscriber;

class Plugin extends PluginBase
{
    public function boot()
    {
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
        $subscribedEmails = Subscriber::lists('email');
        $params = [
            'blog_title'   => $post->title,
            'blog_content' => $post->content_html,
        ];
        foreach ($subscribedEmails as $email) {
            \Mail::queue('serenitynow.subscribe::mail.email', $params, function ($message) use ($email) {
                $message->subject('New Blog Published!');
                $message->to($email);
            });
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
    }
}
