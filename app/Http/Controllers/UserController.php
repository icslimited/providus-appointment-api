<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        try {
            return response()->json(['success' => true, 'users' => User::all()], 200);
        } catch (Exception $ex) {
            return response()->json(['success' => false, 'errors' => array('message' => $ex.getMessage())], 500);
        }
    }

    /**
     * Store/Register a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        try {
            $fields = $request->validate([
                'fullname' => 'required|string',
                'contact' => 'required|numeric|min:10',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|confirmed|min:6',
                'staffId' => 'required|string',
                'departmentId' => 'required|string|exists:departments,id'
            ]);
    
            $user = User::create([
                'fullname' => $fields['fullname'],
                'email' => $fields['email'],
                'password' => bcrypt($fields['password']),
                'userType' => 'front-desk',
                'contact' => $fields['contact'],
                'staffId' => $fields['staffId'],
                'department_id' => $fields['departmentId'],
                'verification_code' => Str::random(60)
            ]);

            $user->notify(new UserNotification($user));

            return response()->json(['success' => true, 'user' => $user], 201);

        } catch (Exception $ex) {
            return response()->json(['success' => false, 'error'], 500);
        }
    }

    //
    public function login(Request $request){
        try {
            $fields = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:6'
            ]);

            if(!Auth::attempt($fields)){
                $error['message'] = 'Invalid Credentials';
                return response()->json(['success' => false, 'error' => $error], 401);
            }

            $user = User::where('email', $fields['email'])->first();
            $token = auth()->user()->createToken('authToken')->plainTextToken;

            return response()->json(['success' => true, 'user' => $user, 'token' => $token], 200);

        } catch (Exception $ex) {
            return response()->json(['success' => false, 'errors' => array('message' => $ex.getMessage()) ], 500);
        }
    }

    //
    public function logout(){
        try {
            auth()->user()->tokens()->delete();

            return response()->json(['success' => true], 200);

        } catch (Exception $ex) {
            return response()->json(['success' => false, 'errors' => array('message' => $ex.getMessage()) ], 500);
        }
    }

    //
    public function verifyAccount(Request $request){
        try {
            $verification_code = $request->query('cl');
            $user = User::where('verification_code', $verification_code)->first();
            if($user == null)
                return response()->json(['success' => false, 'error' =>  array('message' => 'Code not Found') ]);

            $user->email_verified_at = date('Y-m-d H:i:s');
            $user->save();

            return response()->json(['success' => true, 'message' => 'Account Verified Successfully' ]);

        } catch (Exception $ex) {
            return response()->json(['success' => false, 'errors' => array('message' => $ex.getMessage()) ], 500);
        }
    }

    //
    public function forgotPassword(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email']);

            $user = User::where('email', $request['email'])->first();
            if($user == null)
                return response()->json(['success' => false, 'error' =>  array('message' => 'User not Found') ]);
 
            $status = Password::sendResetLink(
                $request->only('email')
            );

            // return $status === Password::RESET_LINK_SENT
            //             ? back()->with(['status' => __($status)])
            //             : back()->withErrors(['email' => __($status)]);

            return response()->json(['success' => true, 'message' => 'A Code has been sent to your email. Use the code to reset your Password' ], 200);

        } catch (Exception $ex) {
            return response()->json(['success' => false, 'errors' => array('message' => $ex.getMessage()) ], 500);
        }
    }

    public function resetPassword(Request $request){
        try {
            $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:6|confirmed',
            ]);
         
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => bcrypt($password)
                    ])->setRememberToken(Str::random(60));
         
                    $user->save();
         
                    event(new PasswordReset($user));
                }
            );
         
            // return $status === Password::PASSWORD_RESET
            //             ? redirect()->route('login')->with('status', __($status))
            //             : back()->withErrors(['email' => [__($status)]]);

            return response()->json(['success' => true, 'message' => 'Password Reset successful' ], 200);

        } catch (Exception $ex) {
            return response()->json(['success' => false, 'errors' => array('message' => $ex.getMessage()) ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Show user profile
        try {
            $user = User::findOrFail($id);

            return response()->json(['success' => true, 'user' => $user], 200);

        } catch (Exception $ex) {
            return response()->json(['success' => false, 'errors' => array('message' => $ex.getMessage()) ], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {   
        return $request;

        try {
            $user = User::find($id);
            if($user == null)
                return response()->json(['success' => false, 'error' => array('message' => 'User Not Found!')]);

            $fields = $request->validate([
                'fullname' => 'string',
                'contact' => 'numeric|min:10',
                'department_id' => 'numeric|exists:departments',
                'branch' => 'string',
                'ableToLogin' => 'boolean',
                'profilePicture' => 'max:10000|mimes:jpeg,jpg,png'
            ]);
 
            DB::beginTransaction();
 
            if($request->hasFile('profilePicture')){
                $fileName = time().'_'.$request->file('profilePicture')->getClientOriginalName();
                $filePath = $request->file('path')->storeAs('profile-pictures', $fileName, 'public');
                $fields['profilePicture'] = '/storage/' . $filePath;
            }

            $user->update($fields);
           
            DB::commit();
 
            return response()->json(['success' => true, 'user' => $user]);
            
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json(['success' => false, 'error' => array('message' => $ex.getMessage())]);
        }

        $user = User::create([
            'fullname' => $fields['fullname'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'contact' => $fields['contact'],
            'staffId' => $fields['staffID'],
            'department_id' => $fields['departmentId'],
            'verification_code' => Str::random(60)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            // if(Auth->user()->id == $user->id)
            //     $user->delete();

            return response()->json(['success' => $user->delete()], 200);

        } catch (Exception $ex) {
            return response()->json(['success' => false, 'errors' => array('message' => $ex.getMessage()) ], 500);
        }
    }

    //
    public function profilePicture(Request $request)
    {
        // Upload profile picture
    }

    //
    public function ableToLogin($id)
    {
        try {
            $status = User::where('id', $id)->pluck('ableToLogin')->first();
            User::where('id', $id)->update(['ableToLogin' => !$status]);

            return response()->json(['success' => true, 'message'=> 'Status changed successfully!'], 200);
        } catch (Exception $ex) {
            return response()->json(['success' => false, 'errors' => array('message' => $ex.getMessage()) ], 500);
        }
    }
}
