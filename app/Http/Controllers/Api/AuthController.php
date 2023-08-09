<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Mail\Passwordreset;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Mail\EmailVerification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // register controller
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'email' => 'required|email|unique:users,email|max:191',
            'password' => 'required|min:8',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'validation_error' => $validator->messages(),
            ]);
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            // Send the email verification notification
            $verificationUrl = URL::temporarySignedRoute(
                'verify-email',
                now()->addMinute(10),
                ['email' => $user->email],
                false // Set this to false to get the URL without the domain
            );
            // Prepend the FRONTEND_URL to the generated URL
            $verificationUrl = Config::get('app.frontend_url') . $verificationUrl;
            Mail::to($user)->send(new EmailVerification($verificationUrl));
            $token = $user->createToken($user->email . '_Token')->plainTextToken;
            return response()->json([
                'status' => Response::HTTP_OK, //200
                'username' => $user->name,
                'access_token' => $token,
                "message" => 'successfully registered user!',
                "email_verfication" => 'Please verify your email!',
            ], Response::HTTP_CREATED); //202
        }
    }

    // login controller

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|max:191',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'validation_error' => $validator->messages(),
            ]);
        } else {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => Response::HTTP_UNAUTHORIZED, //401
                    'message' => 'Invalid credentials',
                ]);
            } else {
                // Check if the user's email is verified
                if (!$user->email_verified_at) {
                    return response()->json([
                        'status' => Response::HTTP_UNAUTHORIZED, //401
                        'message' => 'Please verify your email before logging in.',
                    ]);
                }

                $token = $user->createToken($user->email . '_Token')->plainTextToken;

                return response()->json([
                    'status' => Response::HTTP_OK, //200
                    'username' => $user->name,
                    'access_token' => $token,
                    "message" => 'successfully logged user!',
                ], Response::HTTP_ACCEPTED); //202
            }
        }
    }


    // logout controller
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'status' => Response::HTTP_OK,
            "message" => 'successfully logout user!',
        ], Response::HTTP_ACCEPTED);
    }


  // sendResetLinkEmail controller
    public function sendResetLinkEmail(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|max:191',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'validation_error' => $validator->messages(),
            ]);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found!', 'status' => Response::HTTP_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }
        $token = Str::random(64);
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);
        $passwordUrl = URL::temporarySignedRoute('password-reset', now()->addMinute(10), ['email' => $user->email, 'token' => $token], false);
        $passwordUrl = Config::get('app.frontend_url') . $passwordUrl;
        Mail::to($user)->send(new Passwordreset($passwordUrl));

        return response()->json([
            'message' => 'Password reset link sent successfully to your email',
            'status' => Response::HTTP_OK,
        ]);
    }
}
