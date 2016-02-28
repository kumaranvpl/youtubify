<?php namespace App\Http\Controllers;

use Auth;
use Mail;
use Input;

class SendLinksController extends Controller {

    public function send() {
        $emails  = Input::get('emails', []);
        $name    = Input::get('name');
        $user    = Auth::user();
        $message = Input::get('message');

        foreach($emails as $email) {
            Mail::send('emails.link', ['link' => Input::get('link'), 'user' => $user, 'emailMessage' => $message], function ($m) use($email, $user, $name) {
                $m->to($email)
                  ->subject(trans('app.linkShareSubject', ['file' => $name, 'email' => $user->getNameOrEmail()]))
                  ->from($user->email);
            });
        }

        return trans('app.sentEmailsSuccess');
    }
}
