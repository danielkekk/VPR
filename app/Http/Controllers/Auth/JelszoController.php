<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class JelszoController extends Controller
{
    public function index()
    {
        return view('auth.passwords.jelszomodosit');
    }

    public function changePassword(Request $request)
    {
        //echo $request->userid . ' , ' . $request->oldpassword; exit;

        //meg kel nézni a régi egyezik-e a megadottal


        $messages = [
            'required' => 'A :attribute mező kitöltése kötelező.',
            'min' => 'A jelszó legalább 8 karakter hosszúságú kell legyen.',
            'max' => 'A jelszó maximum 30 karakter hosszúságú kell legyen.',
            'confirmed' => 'A két jelszó nem egyezik meg',
        ];

        $validator = Validator::make($request->all(), [
            'userid' => 'required|integer',
            'oldpassword' => 'required|string|max:30',
            'password' => 'required|string|min:8|confirmed',
        ], $messages);

        if ($validator->fails()) {
            return redirect('newpassword')
                ->withErrors($validator)
                ->withInput();
        }

        //ha minde ok csere
        $kepviselo = User::where('id',trim($request->userid))->first();
        if (!Hash::check(trim($request->oldpassword), trim($kepviselo->password))) {
            $messageBag = new MessageBag();
            $messageBag->add('passworderr', 'A jelszavak nem egyeznek.');

            return redirect('newpassword')
                ->withErrors($messageBag)
                ->withInput();
        }

        $kepviselo->password = Hash::make($request->password);
        $kepviselo->save();

        return redirect('home');
    }
}

