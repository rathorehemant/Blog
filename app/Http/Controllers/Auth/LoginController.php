<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Validator;

class LoginController extends Controller
{
    public function login(Request $request){
        $validate = Validator::make($request->all(),[
            'email' => 'required|string|email',
            'password' => 'required'
        ]);

        if($validate->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Validation error. Please check your input.',
                'error' => $validate->errors(),
               
            ], 422);  
        }

        // check email address is exist in database or not 

        $user = User::where('email',$request->email)->first();

        if(!$user){
            return response()->json([
                'status' => false,
                'message' => 'Invalid email.Try again',
            ], 401); 
        }else{
            // check password are match or not
            if(!Hash::check($request->password, $user->password)){
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Password.Try again',
                ], 401); 
            }else{
                
                $data['token'] = $user->createToken($request->email)->plainTextToken;
                $data['user'] = $user;
                
                $response = [
                    'status' => 'success',
                    'message' => 'User is logged in successfully.',
                    'data' => $data,
                ];
        
                return response()->json($response, 200);
            }
        }
    }
}
