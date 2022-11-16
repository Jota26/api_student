<?php
  
  namespace App\Http\Traits;
  use DB;
  use Illuminate\Support\Facades\Validator;
  use App\Models\courses;
  trait Course{
    public function assign_student($request){

        $validator=Validator::make($request->all(), 
            [
                'id_student'=>'required',
                'course_assign'=>'required',
            ],
            $messages = [
                'required' => 'The :attribute is required.'
            ]
        );

        if(!$validator->fails())
        {
            DB::insert('insert into students_courses 
            (id, id_student, id_courses, created_at, updated_at)
            values 
            (?, ?, ?, ?, ?)', [
                NULL,
                $request->id_student,
                $request->course_assign,
                NOW(),
                NOW()
            ]);
            $course=courses::where('id',$request->course_assign)->get();
            $data=[
                'data'=>[
                    'course'=>$course[0]->name
                ],
                'message'=>'Course assigned',
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
  }