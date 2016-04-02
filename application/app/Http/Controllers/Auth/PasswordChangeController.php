<?php namespace App\Http\Controllers\Auth;

use Input, Auth, Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PasswordChangeController extends Controller {

    /**
     * Send a reset link to the given user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function change(Request $request)
    {
        $user = Auth::user();

        if (IS_DEMO && $user && $user->email === 'admin@admin.com') {
            return "you can't change admin password on demo site.";
        }

        $messages = ['newPassword' => 'required|max:255|min:5|confirmed'];

        //if user already has a password set, validate old password
        if ($user->password) {
            $messages['oldPassword'] = 'required|max:255';
        }

        $validator = $this->getValidationFactory()->make($request->all(), $messages);

        //if user already has a password set, check if it matches the one in input
        if ($user->password) {
            $validator->after(function($validator) {
                if ( ! Auth::validate(['password' => Input::get('oldPassword')])) {
                    $validator->errors()->add('oldPassword', trans('app.wrongPassword'));
                }
            });
        }

        if ($validator->fails())
        {
            $this->throwValidationException($request, $validator);
        }

        if ($user->fill(['password' => Hash::make(Input::get('newPassword'))])->save()) {
            return response(trans('app.passwordChangeSuccess'));
        }
    }
}
