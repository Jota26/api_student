<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\studentController;
use App\Http\Controllers\coursesController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



//Routes for crud Students
Route::prefix('students')->group(function () {
    Route::get('/list_student',[studentController::class,'index'])->name('list.student');
    Route::get('/show_student/{id}',[studentController::class,'show'])->name('show.student');
    Route::post('/store_student',[studentController::class,'store'])->name('store.student');
    Route::put('/update_student/{id}',[studentController::class,'update'])->name('update.student');
    Route::delete('/delete_student/{id}',[studentController::class,'destroy'])->name('delete.student');
});


//Routes for crud Courses
Route::prefix('courses')->group(function () {
    Route::get('/list_course',[coursesController::class,'index'])->name('list.course');
    Route::get('/show_course/{id}',[coursesController::class,'show'])->name('show.course');
    Route::post('/store_course',[coursesController::class,'store'])->name('store.course');
    Route::put('/update_course/{id}',[coursesController::class,'update'])->name('update.course');
    Route::delete('/delete_course/{id}',[coursesController::class,'destroy'])->name('delete.course');
});

//Routes for interaction between courses and students
Route::prefix('students_courses')->group(function () {
    Route::get('/top_courses',[coursesController::class,'getTopCourses'])->name('top.courses');
    Route::get('/get_courses_student/{id}',[coursesController::class,'getCoursesStudent'])->name('get_courses_student');
    Route::post('/assign_student',[coursesController::class,'assign_student_to_course'])->name('assign.student.course');
});
