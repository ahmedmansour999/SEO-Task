<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TwilioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Client;
use Tymon\JWTAuth\Facades\JWTAuth;

use function Laravel\Prompts\error;

class AuthController extends Controller
{

    protected $twilioService;




    public function register(Request $request)
    {

        $validate = Validator::make(
            $request->all(),
            [
                'name' => 'required|string',
                'phone' => 'required|min:11|unique:users,phone',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6'
            ],
            [
                'name.required' => 'Name is required',
                'phone.required' => 'Phone is required',
                'email.required' => 'Email is required',
                'email.email' => 'Email is invalid',
                'email.unique' => 'Email is already taken',
                'phone.unique' => 'Phone Number is already taken',
                'password.required' => 'Password is required',
                'password.min' => 'Password must be at least 6 characters'
            ]
        );
        if ($validate->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validate->errors()
            ]);
        }




        $verificationCode = mt_rand(100000, 999999);


        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'is_admin' => $request->is_admin,
            'verification_code' => $verificationCode,
            'password' => Hash::make($request->password)
        ]);

        // Send SMS with Twilio
        try {
            $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
            $to = '+2' . $user->phone;
            $message = "Your verification code is: $verificationCode";

            $twilio->messages->create($to, [
                'from' => env('TWILIO_PHONE_NUMBER'),
                'body' => $message
            ]);

            Log::info("SMS sent successfully to: $to");
            return response()->json(['message' => 'SMS sent successfully'], 200);
        } catch (\Exception $e) {
            Log::error("Error sending SMS: " . $e->getMessage());
            return response()->json(['error' => 'Failed to send SMS', 'twilio_error' => $e->getMessage()], 500);
        }
    }

    public function verify(Request $request, $id)
    {

        $user = User::findOrFail($id);

        if ($request->code  != $user->verification_code) {
            return response()->json(['message' => 'Verification code is not correct'], 400);
        }

        $user->is_verified = 1;
        return response([
            "status" => 200,
            'message' => "Your Number ($user->phone) is verified",
        ], 200);
    }



    public function login(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'identifier' => 'required',
            'password' => 'required'

        ], [
            'identifier.required' => 'Identifier is required',
            'password.required' => 'Password is required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validate->errors()
            ]);
        }

        $user = User::where('email', $request->identifier)->orWhere('phone', $request->identifier)->first();

        // Check the Email or Phone
        if (!$user) {
            return response()->json([
                'status' => 400,
                'message' => '[ Email/Phone ] Not Correct'
            ], 400);
        }

        // Check The Password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 400,
                'message' => 'Password is incorrect'
            ]);
        }


        $token = JWTAuth::fromUser($user);

        return response()->json([
            'status' => 200,
            'user' => $user,
            'access_token' => $token,
            'message' => 'User logged in successfully'
        ]);
    }



    public function logout()
    {

        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json([
            'status' => 200,
            'message' => 'User logged out successfully'
        ]);
    }
}
