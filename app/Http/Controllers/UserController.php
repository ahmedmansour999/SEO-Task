<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    // Get All User
    public function AllUser(){
        $user = User::all() ;
        return $user ;
    }

    // Get One User
    public function OneUser($id){

        $user = User::where('id' , $id)->first() ;
        return new UserResource($user) ;
    }

    // Update User Data
    public function update(Request $request){


        $validate = Validator::make(
            $request->all(),
            [
                'name' => 'required|string',
                'phone' => 'required|min:11|unique:users,phone',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6' ,
                'is_admin' => 'required|boolean'
            ],
            [
                'name.required' => 'Name is required',
                'phone.required' => 'Phone is required',
                'email.required' => 'Email is required',
                'email.email' => 'Email is invalid',
                'email.unique' => 'Email is already taken',
                'phone.unique' => 'Phone Number is already taken',
                'password.required' => 'Password is required',
                'password.min' => 'Password must be at least 6 characters',
                'is_admin.required' => 'Is Admin is required',

            ]
        );
        if ($validate->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validate->errors()
            ]);
        }
        $user = User::where('id' , $request->id)->first() ;

        $user->name = $request->name ;
        $user->email = $request->email ;
        $user->phone = $request->phone ;
        $user->is_admin = $request->is_admin ;
        $user->password = Hash::make($request->password) ;
        $user->save() ;
        return response()->json([
            'status' => 200,
            'message' => "Updated Successfully" ,
            'date' => [new UserResource($user)]
        ]) ;
    }


    public function destroy($id){

        $user = User::where("id" , $id) ;
        $user->delete() ;
        return response()->json([
            "status" => 200 ,
            "message" => "deleted Successfully"
        ]) ;
    }

}
