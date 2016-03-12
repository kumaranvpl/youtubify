<?php namespace App\Http\Controllers;

use App;
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
                  ->subject(trans('app.linkShareSubject', ['name' => $name, 'email' => $user ? $user->getNameOrEmail() : App::make('Settings')->get('siteName')]));
            });
        }

        return trans('app.sentEmailsSuccess');
    }
}
