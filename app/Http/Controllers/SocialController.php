<?php

namespace App\Http\Controllers;

use App\Models\Social;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str; 

class SocialController extends Controller
{
    public function redirect($provider) {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider) {
        // return Socialite::driver('google')->redirect();
        $socialUser = Socialite::driver($provider) ->user();

        $user = User::where('email',  $socialUser->getEmail())->first();
        $name = $socialUser->getNickname() ?? $socialUser->getName();

        if (!$user) {
            // create user
            $user = User::create([
                'name' => $name,
                'email' => $socialUser->getEmail(),
                'password' => Hash::make(Str::random(7)),
                // 'password' => Hash::make('password'),
            ]);
                //create socials for user
            $user->socials()->create([
                'provider-id' => $socialUser -> getId(),
                'provider' => $provider,
                'provider_token' =>  $socialUser -> token,
                'provider-refresh_token' =>$socialUser -> refreshToken
            ]);

             $user->assignRole('user');
        // dd($provider, $user);
    }

    //if user does exist

    $socials = Social::where('provider', $provider)
                    ->where('user_id', $user->id)->first();

        //check if user doesn't have socials

        if(!$socials){

            $user ->socials()->create([
                'provider-id' => $socialUser -> getId(),
                'provider' => $provider,
                'provider_token' =>  $socialUser -> token,
                'provider-refresh_token' =>$socialUser -> refreshToken
            ]);
        }
        //login user

        Auth::login($user);
        
        return redirect()->intended('/dashboard');
}
}


//    Route::get('/auth/redirect', function () {
//     return Socialite::driver('github')->redirect();
// });
 
// Route::get('/auth/callback', function () {
//     $user = Socialite::driver('github')->user();
 
//     // $user->token
// });
