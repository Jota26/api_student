<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\courses;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use DB;
use App\Http\Traits\Course;
class coursesController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use Course;
    public function index()
    {
        $list_courses=courses::orderBy('id','DESC')->get();
        if (count($list_courses)!=0) {
            return response()->json([
                'data'=>$list_courses,
                'message'=>'Student created successful',
                'result' => true
            ]);
        } else {
            return response()->json([
                'result' => false
            ]);
        }
        
        
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
                'name'=>'required | unique:courses',
                'hourtime'=>'required',
                'date_start_course'=>'required | date | after_or_equal:today',
                'date_end_course'=>'required | date | after_or_equal:date_start_course'
            ],
            $messages = [
                'required' => 'The :attribute is required.',
                'date' => 'The :attribute not is a date',
                'unique'=>'The :attribute already exists'
            ]
        );

        if(!$validator->fails())
        {
            courses::insert([
                'name'=>$request->name,
                'hourtime'=>$request->hourtime,
                'date_start_course'=> Carbon::parse($request->date_start_course)->toDateString(),
                'date_end_course'=> Carbon::parse($request->date_end_course)->toDateString()
            
            ]);
            $data=[
                'data'=>[
                    'name'=>$request->name,
                    'hourtime'=>$request->hourtime,
                    'date_start_course'=>$request->date_start_course,
                    'date_end_course'=>$request->date_end_course
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
        $student=courses::where('id',$id)->first();
        return response()->json($student);
    }


    /**
     * This function is for getting the three courses with more students in lasted 6 months
     *
     * @return \Illuminate\Http\Response
     */
    public function getTopCourses(){

        $date_current = Carbon::now()->addDay(1)->toDateString();
        $date_old = Carbon::now()->subMonth(6)->toDateString();

        $top_courses=DB::select("SELECT 
        Count(id_courses) AS number_students,
        courses.name,
        id_courses,
        courses.hourtime
        from students_courses,courses
        WHERE
        students_courses.created_at BETWEEN '".$date_old."' AND '".$date_current."' and courses.id=students_courses.id_courses
        GROUP BY
        students_courses.id_courses, courses.name, courses.hourtime
        ORDER BY
        number_students DESC
        LIMIT 0, 3");
        return response()->json([
            'data'=>$top_courses,
            'date_old'=>$date_old,
            'date_current'=>$date_current,
            'result'=>true
        ]);
    }


     /**
     * This function is for get the courses assigned to student
     *
     * @return \Illuminate\Http\Response
     */
    public function getCoursesStudent($id){

        $courses_students=courses::join('students_courses','students_courses.id_courses','=','courses.id')
        ->join('students','students.id','=','students_courses.id_student')
        ->where('students.id',$id)
        ->select('courses.id as id_course','courses.name as name_course','students.id as id_student','students.name as name_student')
        ->get();

        if(count($courses_students)!=0){
            return response()->json([
                'data'=>$courses_students,
                'message'=>"Student Courses",
                'result'=>true
            ]);
        }else{
            return response()->json([
                'data'=>'Error in the request, the student does not have courses',
                'message'=>'Error',
                'result'=>false
            ]);
        }

    }

     /**
     * This function is to assign a student to a course in specific
     *
     * @return \Illuminate\Http\Response
     */
    public function assign_student_to_course(Request $request){
        return $this->assign_student($request);
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
                'name'=>'required | unique:courses,name,'.$id,
                'hourtime'=>'required',
                'date_start_course'=>'required | date |after_or_equal:today',
                'date_end_course'=>'required | date | after_or_equal:date_start_course'
            ],
            $messages = [
                'required' => 'The :attribute is required.',
                'date' => 'The :attribute not is a date',
                'unique'=>'The :attribute already exists'
            ]
        );

        if(!$validator->fails())
        {
            courses::where('id',$id)->update($request->all());
            $list_courses=courses::orderBy('id','DESC')->get();
            $data=[
                'data'=>$list_courses,
                'message'=>'Course updated successful',
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
        $course=courses::where('id',$id)->get();
        if(count($course)!=0){
            courses::where('id',$id)->delete();
            DB::delete('DELETE FROM students_courses WHERE students_courses.id_courses = ?', [$id]);
            $data=[
                'data'=>[
                    "name"=> $course[0]->name,
                    "hourtime"=> $course[0]->hourtime,
                    "date_start_course"=> $course[0]->date_start_course,
                    "date_end_course"=> $course[0]->date_end_course,
                ],
                'message'=>'Courses deleted',
                'result'=>true
            ];
            return response()->json($data);
        }else{
            $data=[
                'data'=>'Error in the request,the course no exist',
                'message'=>'Error',
                'result'=>false
            ];
            return response()->json($data);
        }
        
    }
}
