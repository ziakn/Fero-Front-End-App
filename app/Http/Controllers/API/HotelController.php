<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Auth;
use Mail;
use Session;
use Redirect;
use Illuminate\Support\Facades\Hash;
use App\User;
use DB;
use Image;
use Illuminate\Support\Str;

class HotelController extends Controller
{
    
    public function index()
    {
        //
    }

   
    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {
        //
    }

    
    public function show($id)
    {
        //
    }

    
    public function edit($id)
    {
        //
    }

   
    public function update(Request $request, $id)
    {
        
    }

   
    public function destroy($id)
    {
        //
    }

    public function register (Request $request) {
        
        $response=array();
        // dd( $request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'tax_id' => 'required|string|max:255',
            'restaurent_address' => 'required|string|max:255',
            'iban' => 'required|string|max:255',
        ]);
        if ($validator->fails())
        {
            return response([
                'status' => 400 ,
                'message'=>$validator->errors()->all(),
                'data' => false,

            ], 422);
        }

       DB::beginTransaction();
        try {

            $create= User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'user_type' => 1,
                    'password' => Hash::make($request->password),
                    'restaurent_name' => $request->restaurent_name,
                    'tax_id' => $request->tax_id,
                    'iban' => $request->iban,
                    'address' => $request->address,
                    'restaurent_address' => $request->restaurent_address,
                    'contact' => $request->contact,
                    'lat' => $request->lat,
                    'lon' => $request->lon,
                ]);

        DB::commit();
        $response['status']= 200;
        $response['message']= 'Successfuly Done';
        $response['data'] = $create;
                    
    } catch (\Exception $e) {
        $response['status']=$e->getStatusCode();
        $response['message'] = 'Not Successfull';
        $response['data'] = false;
        DB::rollback();
    } 
    return response()->json($response);
        
    
        
    }

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


    public function hotelupdate(Request $request)
    {

        $auth_id = Auth::id();
        // dd($request->all());
        $response=array();
        $src="";
        DB::beginTransaction();
        try {
        if($request->hasFile('image'))
        {    
            $request->image->store('public/uploads/image');
            $src= '/storage/uploads/image/'.$request->image->hashName();
            
        }
        
        $update=User::where('id',$auth_id)->update(
            [
                'restaurent_name' => $request->restaurent_name,
                'tax_id' => $request->tax_id,
                'iban' => $request->iban,
                'address' => $request->address,
                'restaurent_address' => $request->restaurent_address,
                'contact' => $request->contact,
                'lat' => $request->lat,
                'lon' => $request->lon,
                'image' => $src,
            ]);
     
            DB::commit();
            $response['status']= 200;
            $response['message']= 'Successfuly Done';
            $response['data'] = $update;
                        
        } catch (\Exception $e) {
            $response['status']=$e->getStatusCode();
            $response['message'] = 'Not Successfull';
            $response['data'] = false;
            DB::rollback();
        } 
        return response()->json($response);    

    }

    public function profile(Request $request)
    {
        $response=array();
        $auth_id = Auth::id();
        DB::beginTransaction();
        try {

        $update=User::where('id',$auth_id)->update(
            [
                'name' => $request->name
            ]);


            DB::commit();
            $response['status']= 200;
            $response['message']= 'Successfuly Done';
            $response['data'] = $update;
                        
        } catch (\Exception $e) {
            $response['status']=$e->getStatusCode();
            $response['message'] = 'Not Successfull';
            $response['data'] = false;
            DB::rollback();
        } 
        return response()->json($response); 
     }

     public function changePass(Request $request)
     {
        $response=array();
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'newPassword' => ['required'],
             'confirmPassword' => ['same:newPassword'],
        ]);
        if ($validator->fails())
        {
            return response([
                'status' => 400 ,
                'message'=>$validator->errors()->all(),
                'data' => false,

            ], 422);
        }
        DB::beginTransaction();
        try {
         if(!Hash::check($request->oldPassword,Auth::user()->password))
         {
             return response()->json(
                 [
                    'status' => 400 ,
                     'message'=> 'Current Password dose not matched',
                     'data' => false,
                 ], 200);
         }
         else
         {                     
             $update=User::find(auth()->user()->id)->update(['password'=> Hash::make($request->newPassword)]);  
         }
         DB::commit();
            $response['status']= 200;
            $response['message']= 'Successfuly Done';
            $response['data'] = $update;
                        
        } catch (\Exception $e) {
            $response['status']=$e->getStatusCode();
            $response['message'] = 'Not Successfull';
            $response['data'] = false;
            DB::rollback();
        } 
        return response()->json($response); 
     }
    
}
