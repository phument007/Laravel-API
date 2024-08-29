<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     *Create User
     * @param Request $request;
     * @return User
     */
    public function createUser(Request $request){

        $validator = Validator::make($request->all(),[
            'name' =>'required|max:255',
            'email' =>'required|email|max:255|unique:users',
            'password' => 'required',
        ]);

        if($validator->failed()){
            return response([
                'status' => 500,
                'errors' => $validator->errors()
            ]);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return response([
            'status' => 200,
            'message' => 'User Create Successfully.',
            'token' => $user->createToken("API TOKEN")->plainTextToken,
        ]);
    }

    /**
     *Login
     * @param Request $request;
     * @return User
     */
    public function authentication(Request $request){
        $validator = Validator::make($request->all(),[
            'email' =>'required|email|max:255',
            'password' => 'required',
        ]);

        if($validator->failed()){
            return response([
               'status' => 500,
                'errors' => $validator->errors()
            ]);
        }

        $user = User::where('email',$request->email)->first();

        if(!Auth::attempt($request->only(['email','password']))){
            return response([
               'status' => 401,
               'message' => 'Email or Password is incorrect.'
            ]);
        }

        return response([
            'status' => 200,
            'message' => 'Login Success.',
            'token' => $user->createToken("API TOKEN")->plainTextToken,
        ]);
    }

    public function index(){
        $users = User::all();
        return response([
           'status' => 200,
           'message' => 'User List.',
           'data' => $users
        ]);
    }
}
