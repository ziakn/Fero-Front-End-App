<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use Auth;
use Mail;
use Session;
use Redirect;
use DB;
use Image;
use Illuminate\Support\Str;

class UserController extends Controller
{
    

    public function register (Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        $request['password']=Hash::make($request['password']);
        $request['remember_token'] = Str::random(10);
        $user = User::create($request->toArray());
        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        $response = ['token' => $token];
        // return response($response, 200);
        if()
        {
            return response()->json(
                [
                    'status'=> $this->response_code,
                    'message'=> 'Successfuly Changed',
                    'data' => $response
                ], 200);
        }
    
        
    }

    // public function login(Request $request)
    // {
    //     $loginData = $request->validate([
    //         'email' => 'email|required',
    //         'password' => 'required'
    //     ]);

    //     if (!auth()->attempt($loginData)) {
    //         return response(['message' => 'Invalid Credentials']);
    //     }

    //     $accessToken = auth()->user()->createToken('authToken')->accessToken;

    //     return response(['user' => auth()->user(), 'access_token' => $accessToken]);

    // }
   public function login(Request $request)
   {
        $http = new \GuzzleHttp\Client;
        try {
            $response = $http->post('http://192.168.43.254/Fero/public/oauth/token', [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => 2,
                    'client_secret' => 'D7ZaLKTW8E64AgHrq1X8RRDQpbSyrikg6LQfe0Hz',
                    'username' => $request->email,
                    'password' => $request->password,
                ]
            ]);
            return $response->getBody();
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            if ($e->getCode() === 400) {
                return response()->json([
                    'status' => '400',
                    'message' => 'Invalid Request. Please enter a username or a password.',
                    'access_token' => false
                ], 200);
            } else if ($e->getCode() === 401) {
                return response()->json([
                    'status' => '401',
                    'message' => 'Your credentials are incorrect. Please try again',
                    'access_token' => false
                ],200);
            }
            return response()->json([
                'status' => '401',
                'message' => 'Something went wrong on the server.',
                'access_token' => false
            ], 200);
        }
    }

    public function logout()
    {  
        Auth::logout();
        Session::flush();
        // return Redirect::to('/login');
    }
    public function profile()
    {
        $data=Auth::user();
        return $data;
    }

    public function updateUser(Request $request)
    {
        $update=User::where('id',$request->id)->update(
            [
                'name' => $request->name
            ]);
        return $update;
    }

    public function updatepassword(Request $request)
   {
        $update=User::where('id',$request->id)->update(
           [
               'password'=>bcrypt($request->password),
           ]
        );
        $data['password'] =$request->password;
        $data['email'] =$request->email;
        Mail::send('mailview', $data, function($message) use ($request) {
            $message->to( $request->email , $request->name )
            ->subject('Password for SimplistiQ Solution Login');
        });
       return $update;
   }

   public function avatar(Request $request)
   {
       $user_id = Auth::id();
       //return $request->all();
       $request->file('myFile')->store('public/uploads/avatar');
       $pic= '/storage/uploads/avatar/'.$request->myFile->hashName();   
       Image::make('storage/uploads/avatar/'.$request->myFile->hashName())->fit(600, 400, function($constraint) {
        $constraint->aspectRatio();})->save('storage/uploads/avatar/'.$request->myFile->hashName());              
       $update=User::where('id', $user_id)->update([
           'image' => $pic
       ]);
       if($update)
       {
        return response()->json([
            'data' => $pic,
            'status' => true
        ],200);
        }
        return response()->json([
            'data' => 'Failed',
            'status' => false
        ],200);
   }
   public function changePass(Request $request)
   {
       

       $request->validate([
           'newPassword' => ['required'],
           'confirmPassword' => ['same:newPassword'],
       ]);
       if(!Hash::check($request->oldPassword,Auth::user()->password))
       {
           return response()->json(
               [
                   'status'=> false,
                   'message'=> 'Current Password dose not matched'
               ], 200);
       }
       else
       {                     
           $update=User::find(auth()->user()->id)->update(['password'=> Hash::make($request->newPassword)]);  
           if($update)   
           {
               return response()->json(
                   [
                       'status'=> true,
                       'message'=> 'Successfuly Changed'
                   ], 200);
           } 
           else
           {
               return response()->json(
                   [
                       'status'=> false,
                       'message'=> 'Failed, Try again'
                   ], 200);
           }

       }
   }
    

}
