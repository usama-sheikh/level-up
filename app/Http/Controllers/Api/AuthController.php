<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Api\BaseController as BaseController;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Validator;

class AuthController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request): \Illuminate\Http\JsonResponse
    {
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $user['token'] = $user->createToken('MyApp')->accessToken;

        return $this->sendResponse($user, 'User register successfully.');
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $user['token'] = $user->createToken('MyApp')->accessToken;

            return $this->sendResponse($user, 'User login successfully.');
        } else {
            return $this->sendDefaultError('credentials', 'Unauthorised.');
        }
    }

    /**
     * Send OTP api
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendOTP(Request $request): \Illuminate\Http\JsonResponse
    {
        $email = $request->email;
        $get_user_email = User::where('email', $email)->first();
        $regards = config('app.app_name') ?? '';
        $otp = rand(1000, 9999);

        if ($get_user_email != null) {
            try {
                DB::beginTransaction();
                $get_user_email->otp = $otp;
                $subject = "Level Up Registration Otp";
                $get_user_email->save();
                try {
                    Mail::send('emailTemplate.send_otp_email_api', compact('otp', 'regards'), function ($message) use ($subject, $email) {
                        $message->from(config('app.email_from') ?? 'noreply@levelup.com');
                        $message->to($email);
                        $message->subject($subject);
                    });
                } catch (\Exception $e) {
                    $emailError = $e->getMessage();
                    return $this->sendError('Email not sent.', $emailError);
                }
                DB::commit();
                $response['otp'] = $otp;
                return $this->sendResponse($response, 'Email sent Successfully.');
            } catch (\Exception $e) {
                $dbError = $e->getMessage();
                DB::rollBack();
                return $this->sendError('Something went wrong', $dbError);
            }
        } else {
            return $this->sendError('Email not found.');
        }

    }

    /**
     * Send OTP api
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkOTP(Request $request): \Illuminate\Http\JsonResponse
    {
        $email = $request->email;
        $otp = $request->otp;
        $get_otp = User::where('email', $email)->where('otp', $otp)->first();
        if (($get_otp != null) && ($get_otp->email_verified_at != null) ) {
            try {
                DB::beginTransaction();
                $get_otp->email_verified_at = Carbon::today();
                $get_otp->otp = "";
                $get_otp->save();
                DB::commit();
                $response['email'] = $email;
                return $this->sendResponse($response, 'Email verified.');
            } catch (\Exception $e) {
                $dbError = $e->getMessage();
                return $this->sendError('Unable to update user as verified.', $dbError);
            }
        } else {

            if ($get_otp->email_verified_at == null)
                return $this->sendError('Email already verified.');
            /*else if ($get_otp->email_verified_at == null)
                return $this->sendError('Email not verified.');*/
            else
                return $this->sendError('Email not found.');
        }
    }
}
