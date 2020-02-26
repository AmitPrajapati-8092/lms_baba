<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Course;
use Illuminate\Support\Facades\Session;
use App\Quizz;

class QuizzController extends Controller {

    public function Quizz() {
        $courseList = Course::where('org_id', session::get('org_id'))->where('course_status', 'Active')->get();
        $quizzList = Quizz::with('course')->where('org_id', session::get('org_id'))->get();
        $returnData = array(
            'courseList' => $courseList,
            'quizzList' => $quizzList,
        );
        $data['content'] = 'Quizz.Quizz';
        return view('layouts.content', compact('data'))->with($returnData);
    }

    public function add(Request $req) {
        $req->validate([
            'title' => 'required',
            'course_id' => 'required',
            'time_limit' => 'required',
            'max_tries' => 'required',
            'no_of_question' => 'required',
            'instruction' => 'required',
            'description' => 'required',
            'status' => 'required',
        ]);
        if ($req->id > 0) {
            $messege = 'Quizz Update Successfull...!';
            $quizz = Quizz::find($req->id);
            $quizz->updated_by = session::get('id');
        } else {
            $messege = 'Quizz Create Successful...!';
            $quizz = new Quizz();
            $quizz->created_by = session::get('id');
        }
        $quizz->org_id = session::get('org_id');
        $quizz->title = $req->title;
        $quizz->course_id = $req->course_id;
        $quizz->time_limit = $req->time_limit;
        $quizz->max_tries = $req->max_tries;
        $quizz->no_of_question = $req->no_of_question;
        $quizz->instruction = $req->instruction;
        $quizz->description = $req->description;
        $quizz->status = $req->status;
        $quizz->ip_address = $req->ip();
        if ($quizz->save()) {
            session()->flash('success', $messege);
        } else {
            session()->flash('error', 'Something Wrong...!');
        }
        return redirect()->back();
    }

    public function deleteQuizz(Request $req) {
        if ($req->id) {
            $quizz = Quizz::find($req->id);
            $quizz->ip_address = $req->ip();
            $quizz->deleted_by = session::get('id');
            $quizz->deleted_at = date('Y-m-d H:i:s');
            $quizz->is_deleted = 1;
            $quizz->save();
            if ($quizz->delete()) {
                session()->flash('success', 'Quizz delete Successfull...!');
            } else {
                session()->flash('error', 'Something Wrong...!');
            }
        } else {
            session()->flash('error', 'Quizz Id is required...!');
        }
        return redirect()->back();
    }

    public function LearnerQuizz() {
        $quizzList = Quizz::with('course')->where('org_id', session::get('org_id'))->where('status','Active')->get();
        $returnData = array(
            'quizzList' => $quizzList,
        );
        $data['content'] = 'Quizz.student_quizz';
        return view('layouts.content', compact('data'))->with($returnData);
    }
    public function start() {
        return view('Quizz.quizz_demo');
    }

}
