<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    /**
     * Shows the settings page.
     */
    public function index()
    {
        return view('settings');
    }

    /**
     * Method to change the email.
     *
     * @param  Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeEmail(Request $request)
    {
        $this->validate($request, [
            'current-password-email-change' => 'required|string|max:255',
            'email' => 'required|email|string|unique:users',
        ]);

        if (!Hash::check($request->get('current-password-email-change'), Auth::user()->password)) {
            $request->session()->flash('error', 'The current password is not correct.');
            return redirect()->route('settings');
        }

        Auth::user()->email = $request->get('email');
        Auth::user()->save();

        $request->session()->flash('success', 'Changes saved successfully.');
        return redirect()->route('settings');
    }

    /**
     * Method to change the password.
     *
     * @param  Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(Request $request)
    {
        $this->validate($request, [
            'current-password-password-change' => 'required|string',
            'new-password' => 'required|confirmed|string',
            'new-password_confirmation' => 'required|string',
        ]);

        if (!Hash::check($request->get('current-password-password-change'), Auth::user()->password)) {
            $request->session()->flash('error', 'The current password is not correct.');
            return redirect()->route('settings');
        }

        Auth::user()->password = Hash::make($request->get('new-password'));
        Auth::user()->save();

        $request->session()->flash('success', 'Changes saved successfully.');
        return redirect()->route('settings');
    }
}
