<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
class UserController extends Controller
{
    
    /**
     * Register User
     *
     * @param Request $request
     * @return void
     */
    public function register(Request $request) {
// validating user data entered from the frontend 
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "email" => "required|email|unique:users,email",
            "password" => "required|min:6",
        ]);
// if data data is not correct throws error
        if($validator->fails()) {
            return $this->validationErrors($validator->errors());
        }

        // creating a user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            // this makes password to be hidden
            'password' => Hash::make($request->password)
        ]);
      
            $token = $user->createToken('token')->accessToken;
       
        
        return response()->json(["status" => "success", "error" => false, "message" => "Success! User registered.","token"=>$token,"data"=>$user], 201);

       
    }
// ===============================================================================================================================
/**
     * User Login
     *
     * @param Request $request
     * @return void
     */
    public function login(Request $request) {

        // validating user data
        $validator = Validator::make($request->all(), [
            "email" => "required|email",
            "password" => "required|min:3"
        ]);
// if data is not correct throws error
        if($validator->fails()) {
            return $this->validationErrors($validator->errors());
        }

        try {
            //Checking if inputs are correct
            //if inputs are correct it will set the logged user data in the Auth() 
            //and it will create a token for the current logged user
            if(Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();
                $token = $user->createToken('token')->accessToken;
                return response()->json(
                    [
                        "status" => "success",
                        "error" => false,
                        "message" => "Success! you are logged in.",
                        "token" => $token,
                        "data"=>$user
                    ]
                );
            }
            return response()->json(["status" => "failed", "message" => "Failed! invalid credentials."], 200);
        }
        catch(Exception $e) {
            return response()->json(["status" => "failed", "message" => $e->getMessage()], 404);
        }
    }
    //=================================================================================================================
      /**
     * Logged User Data Using Auth Token
     *
     * @return void
     */
    //This function will return the logged user details through the auth token that will be passed through the header4
    public function user() {
        try {
            $user = Auth::user()->name;
            return response()->json(["status" => "success", "error" => false, "data" => $user], 200);
        }
        catch(NotFoundHttpException $exception) {
            return response()->json(["status" => "failed", "error" => $exception], 401);
        }
    }
    //=========================================================================================================================
    
    // ========= 
    /**
     * Update 
     *
      * @param Request $request
  * @return void
     */
    public function update(Request $request) 
    {
              // validating user data
              $validator = Validator::make($request->all(), [
                "name" => "required|min:3",
              
            ]);
    // if data is not correct throws error
            if($validator->fails()) {
                return $this->validationErrors($validator->errors());
            }
    
        $user=User::find(Auth::user()->id);
        if($user){
        //    Assigning user input entered name to user name
            $user->name = $request['name'];
            // Saving updated user
            $user->save();
            return response()->json(["status" => "success", "error" => false, "message" => "Success! Username updated.","data"=>$user], 201);
        }
        return response()->json(["status" => "failed", "error" => true, "message" => "Failed no todo found."], 404);
      
            }
      
  //=========================================================================================================================
    
    // ========= 
    /**
     * Update 
     *
      * @param Request $request
  * @return void
     */
    public function updateImage(Request $request) 
    {
              // validating user data
              $validator = Validator::make($request->all(), [
                "image" => "required|min:3",
              
            ]);
    // if data is not correct throws error
            if($validator->fails()) {
                return $this->validationErrors($validator->errors());
            }
    
        $user=User::find(Auth::user()->id);
        if($user){
        //    Assigning user input entered image to user image
            $user->image = $request['image'];
            // Saving updated user
            $user->save();
            return response()->json(["status" => "success", "error" => false, "message" => "Success! User image updated.","data"=>$user], 201);
        }
        return response()->json(["status" => "failed", "error" => true, "message" => "Failed no todo found."], 404);
      
            }
      

// ===============================================================================================================
     /**
    * Logout Auth User
    *
    * @param Request $request
    * @return void
    */
    // Will revoke the auth token that is generated after the successful login
    public function logout() {

        if(Auth::check()) {
            Auth::user()->token()->revoke();
            return response()->json(["status" => "success", "error" => false, "message" => "Success! You are logged out."], 200);
        }
        return response()->json(["status" => "failed", "error" => true, "message" => "Failed! You are already logged out."], 403);
    }
   
}
