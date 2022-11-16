<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\students;

use Illuminate\Support\Facades\Validator;
use App\Http\Traits\Course;
use DB;
class studentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use Course;
    public function index()
    {

        $list_students=students::orderBy('id','DESC')->get();
        return response()->json(
            [
                'data'=>$list_students,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator=Validator::make($request->all(), 
            [
                'name'=>'required',
                'last_name'=>'required',
                'age'=>'required',
                'email_address'=>'email|required|unique:students',
            ],
            $messages = [
                'required' => 'The :attribute is required.',
                'unique' => 'The :attribute already exists.',
            ]
        );

        if(!$validator->fails())
        {
            students::create([
                'name'=>$request->name,
                'last_name'=>$request->last_name,
                'age'=>$request->age,
                'email_address'=>$request->email_address
            ]);
            $data=[
                'data'=>[
                    'name'=>$request->name,
                    'last_name'=>$request->last_name,
                    'age'=>$request->age,
                    'email_address'=>$request->email_address
                ],
                'message'=>'Student created successful',
                'result'=>true
            ];
            return response()->json($data);
        }else{
            return response()->json([
                'data'=>$validator->errors(),
                'message'=>'Errors',
                'result'=>false
            ]);
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
        $student=students::where('id',$id)->first();
        return response()->json($student);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
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
        $validator=Validator::make($request->all(), 
            [
                'name'=>'required',
                'last_name'=>'required',
                'age'=>'required',
                'email_address' => 'required | email |unique:students,email_address,'.$id
            ],
            $messages = [
                'required' => 'The :attribute is required.',
                'unique' => 'The :attribute already exists.',
            ]
        );

        if(!$validator->fails())
        {
            students::where('id',$id)->update($request->all());
            $data=[
                'data'=>$request->all(),
                'message'=>'Student updated successful',
                'result'=>true
            ];
            return response()->json($data);
        }else{
            return response()->json([
                'data'=>$validator->errors(),
                'message'=>'Errors',
                'result'=>false
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $student=students::where('id',$id)->get();
        if(count($student)!=0){
            students::where('id',$id)->delete();
            DB::delete('DELETE FROM students_courses WHERE students_courses.id_student = ?', [$id]);
            $data=[
                'data'=>[
                    "name"=>$student[0]->name,
                    "last_name"=>$student[0]->last_name,
                    "email_address"=>$student[0]->email_address
                ],
                'message'=>'Student deleted',
                'result'=>true
            ];
            return response()->json($data);
        }else{
            $data=[
                'data'=>['Error in the request, the student no exist'],
                'message'=>'Errors',
                'result'=>false
            ];
            return response()->json($data);
        }
        
    }
}
