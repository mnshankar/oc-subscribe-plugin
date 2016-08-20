<?php namespace Serenitynow\Subscribe\Components;

use Cms\Classes\ComponentBase;
use October\Rain\Exception\ValidationException;
use SerenityNow\Subscribe\Models\Subscriber;

class Signup extends ComponentBase
{

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
        $data = post();
        $rules = [
            'email' => 'required|email'
        ];
        $validation = \Validator::make($data, $rules);
        if ($validation->fails()) {
            throw new ValidationException($validation);
        }
        Subscriber::create([
            'email' => $data['email'],
        ]);
        return true;
    }

    //ajax method called on unsubscribe
    public function onCancel()
    {
        $data = post();
        $rules = [
            'email' => 'required|email'
        ];
        $validation = \Validator::make($data, $rules);
        if ($validation->fails()) {
            throw new ValidationException($validation);
        }
        if (!Subscriber::where('email', $data['email'])->first()) {
            throw new \Exception('Sorry. Email not found.');
        }
        Subscriber::where([
            'email' => $data['email'],
        ])->delete();

        return true;
    }
}
