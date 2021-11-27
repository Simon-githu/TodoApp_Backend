<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Exception;
use Facade\FlareClient\Http\Exceptions\NotFound;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Getting all todos of the authenticated user
        $todos = Auth::user()->todos;
        return response()->json(["status" => "success", "error" => false, "count" => count($todos), "todos" => $todos],200);
    
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validating todo title 
        $validator = Validator::make($request->all(), [
            "title" => "required|min:3|unique:todos,title",
           
        ]);
// if the todo is not entered or has 2 letters only an error will be thrown
        if($validator->fails()) {
            return $this->validationErrors($validator->errors());
        }

        try {
            // creating the todo
            //insert todo with logged user id
            $todo = Todo::create([
                "title" => $request->title,
                "date" => $request->date,
                "user_id" => Auth::user()->id
            ]);
            $todos = Auth::user()->todos;
            return response()->json(["status" => "success", "error" => false, "message" => "Success! todo created.","todos"=>$todos], 201);
        }
        catch(Exception $exception) {
            return response()->json(["status" => "failed", "error" => $exception->getMessage()], 404);
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
        //the logged user can see only their own todo with the todo id. It will return only one todo on the basis of todo id.
        //This returns the todos of the logged user
        $todo = Auth::user()->todos->find($id);

        if($todo) {
            return response()->json(["status" => "success", "error" => false, "data" => $todo], 200);
        }
        return response()->json(["status" => "failed", "error" => true, "message" => "Failed! no todo found."], 404);
    
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
        //Will update a particular todo
        $todo = Auth::user()->todos->find($id);

        
        if($todo) {
            //validate input
            $validator = Validator::make($request->all(), [
                'title' => 'required',
            ]);
// return error if input is incorrect(null)
            if($validator->fails()) {
                return $this->validationErrors($validator->errors());
            }

            
            $todo['title'] = $request->title;
 
            // if has active
            if($request->active) {
                $todo['active'] = $request->active;
            }

            // if has completed
            if($request->completed) {
                $todo['completed'] = $request->completed;
            }
// saving the todo
            $todo->save();
            $todos = Auth::user()->todos;
            return response()->json(["status" => "success", "error" => false, "message" => "Success! todo updated.","todos"=>$todos], 201);
        }
        return response()->json(["status" => "failed", "error" => true, "message" => "Failed no todo found."], 404);
    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Deleting a specific todo
        $todo = Auth::user()->todos->find($id);
        //if it gets the todo it then deletes it
        if($todo) {
            $todo->delete();
            return response()->json(["status" => "success", "error" => false, "message" => "Success! todo deleted."], 200);
        }
        return response()->json(["status" => "failed", "error" => true, "message" => "Failed no todo found."], 404);
    
    }
}
