<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Course;
use App\Language;
use App\CourseCategory;
use App\CourseCode;
use App\Certificate;
use App\mcq_questions;
use App\mcq_answer;

class LearningController extends Controller {

    public function ReferenceMaterial() {
        $courseList = Course::with('category')->with('language')->with('certificate')->where('org_id', session::get('org_id'))->get();
        $category = CourseCategory::where('org_id', session::get('org_id'))->get();
        $certificateList = Certificate::where('org_id', session::get('org_id'))->get();
        $language = Language::get();
        $returnData = array(
            'courseList' => $courseList,
            'allcategory' => $category,
            'languageList' => $language,
            'certificateList' => $certificateList,
        );
//        return response()->json($returnData);
        $data['content'] = 'instructor.learning-object.Reference_Material';
        return view('layouts.content', compact('data'))->with($returnData);
    }

//    public function Mcq() {
//        $data['content'] = 'instructor.learning-object.Manage_MCQ';
//        return view('layouts.content', compact('data'));
//    }


    /* --------Start Abhishek Anand code--------- */
    public function SetMcqQuestion() {
        $data['content'] = 'instructor.learning-object.Set_mcq_question';
        return view('layouts.content', compact('data'));
    }

    public function Mcq_old() {
        $data['content'] = 'instructor.learning-object.Manage_MCQ';
        return view('layouts.content', compact('data'));
    }

    public function Mcq() {
        $mcq = mcq_questions::where('org_id', session::get('org_id'))->where('status', 1)->get()->toArray();
        $course = Course::where('org_id', session::get('org_id'))->get()->toArray();
        $returnData = array(
            'mcq' => $mcq
        );
        $data['content'] = 'instructor.learning-object.Manage_MCQ';
        return view('layouts.content', compact('data'))->with("returnData ", $returnData)->with("mcq", $mcq)->with("course", $course);
    }

    public function Add_mcq(Request $request) {
        // return $request;
        // return $request->correct_answer;

        if ($request->mcq_id != "")
        {
            // echo "edit";
                // $mcq= new mcq_questions();
                $mcq = mcq_questions::find($request->mcq_id);
                $mcq->org_id = session::get('org_id');
                $mcq->course_id = $request->course_id;
                $mcq->category = $request->question_category;
                $mcq->score = $request->score;
                // $mcq->type = $request->question_type;
                $mcq->question = $request->question;
                $mcq->created_by = session::get('id');
                $mcq->save();

                
                if ($request->record_value_id != "" ) {
                    foreach ($request->record_value as $key => $value) {
                        
                            // $answer = new mcq_answer();
                            $answer = mcq_answer::find($request->record_value_id[$key]);
                            // return $answer;
                            $answer->org_id =  $mcq->org_id;
                            $answer->question_id = $mcq->id;
                            $answer->created_by = session::get('id');
                            $answer->answer = $request->record_value[$key];
                           
                            if($request->correct_answer) {
                                if (in_array('1' .$request->record_value[$key], $request->correct_answer))
                                {
                                    $answer->correct_answer = 1;
                                }
                                else {
                                    $answer->correct_answer = 0;
                                }
                            }
                            $answer->save();
                    
                    }
                }
        }
        else 
        {
            // echo "add";
                $mcq= new mcq_questions();
                $mcq->org_id = session::get('org_id');
                $mcq->course_id = $request->course_id;
                $mcq->category = $request->question_category;
                $mcq->score = $request->score;
                $mcq->type = $request->question_type;
                $mcq->question = $request->question;
                $mcq->created_by = session::get('id');
                $mcq->save();


                if ($request->record_value != "" ) {
                    foreach ($request->record_value as $key => $value) {
                        
                            $answer = new mcq_answer();
                            $answer->org_id =  $mcq->org_id;
                            $answer->question_id = $mcq->id;
                            $answer->created_by = session::get('id');
                            $answer->answer = $request->record_value[$key];
                            // if('1'.$request->record_value[$key] == $request->correct_answer)
                            if($request->correct_answer) {
                                if (in_array('1' .$request->record_value[$key], $request->correct_answer))
                                {
                                    $answer->correct_answer = 1;
                                }
                                else {
                                    $answer->correct_answer = 0;
                                }
                            }
                            $answer->save();
                    
                    }
                }
        }
        // exit;
                // $mcq= new mcq_questions();
                // $mcq->org_id = session::get('org_id');
                // $mcq->course_id = $request->course_id;
                // $mcq->category = $request->question_category;
                // $mcq->score = $request->score;
                // $mcq->type = $request->question_type;
                // $mcq->question = $request->question;
                // $mcq->created_by = session::get('id');
                // $mcq->save();


                // if ($request->record_value != "" ) {
                //     foreach ($request->record_value as $key => $value) {
                        
                //             $answer = new mcq_answer();
                //             $answer->org_id =  $mcq->org_id;
                //             $answer->question_id = $mcq->id;
                //             $answer->created_by = session::get('id');
                //             $answer->answer = $request->record_value[$key];
                //             if('1'.$request->record_value[$key] == $request->correct_answer)
                //             {
                //                 $answer->correct_answer = 1;
                //             }
                //             else {
                //                 $answer->correct_answer = 0;
                //             }
                //             $answer->save();
                    
                //     }
                // }



        // session()->flash('success', 'Create Successful...!');
        return redirect()->back();
    }

    public function delete_mcq(Request $Request) {
        $remove_question = mcq_questions::where('id', $Request->id)->update(array(
            'status' => 0
        ));
        $remove_answer = mcq_answer::where('question_id', $Request->id)->update(array(
            'status' => 0
        ));
        Session::flash('success', 'Remove Successfully');
        return redirect()->back();
    }

    
    public function edit_mcq(Request $Request)
    {
        // return $Request->mcq_id;
        $requrired_id = $Request->mcq_id;
        // $data['mcq_questions']  = mcq_questions::find($requrired_id);
        $data['mcq_questions']  = mcq_questions::where('mcq_questions.id', $requrired_id)
        ->leftjoin('courses', 'courses.id', '=', 'mcq_questions.course_id')
        ->select(
            'mcq_questions.id as id',
            'mcq_questions.org_id as org_id',
            'mcq_questions.course_id as course_id',
            'mcq_questions.category as category',
            'mcq_questions.type as type',
            'mcq_questions.score as score',
            'mcq_questions.question as question',
            'mcq_questions.status as status',
            'mcq_questions.live_status as live_status',
            'courses.course_name as course_name'
        )
        ->first();
        $data['mcq_answer'] = mcq_answer::where('question_id',$requrired_id)->get()->toArray();
        
        // $data['course_details'] = Course::where('id',$requrired_id)->first();
        // echo "<pre>";
        // print_r($data);
        // exit;

        return $data;
    }

    public function active_deactive_mcq(Request $Request) {
        $remove_question = mcq_questions::where('id', $Request->id)->update(array(
            'status' => 0
        ));
        $remove_answer = mcq_answer::where('question_id', $Request->id)->update(array(
            'status' => 0
        ));
        return redirect()->back();
    }

    public function changeStatus(Request $request) {
        $data = mcq_questions::find($request->id);
        $data->live_status = $request->live_status;
        $data->save();
        return $data;
    }
    
    public function fetch_mcq_question(Request $Request)
	{
			$data['mcq_questions']=mcq_questions::where('course_id',$Request->id)->get()->toArray();

		// echo ("<pre>");
		// print_r($data);
		// exit;
		
		return $data;
    }
    
    // public function BulkUploadQuestion(Request $request)
    // {
    //     echo "hello";
    // }

    /* --------End Abhishek Anand code--------- */

    public function BulkUploadQuestion(Request $request)
    {
        if ($request->isMethod('post')) 
        {
            
            if($_FILES["uploadfile"]["size"] > 0 && $request->submit=='upload_csv')
            {

                try
                {
                $filename=$_FILES["uploadfile"]["tmp_name"]; 
                $file = fopen($filename, "r");
                
                // return fgetcsv($file, 100000, ",");
                $i=0;
                while (($csv_data = fgetcsv($file, 100000, ",")) !== FALSE)
                {
                if($i>0)
                    {       
                        
                        $csvData['org_id'] = Session::get('org_id');
                        $csvData['course_id'] = $request->course_id;
                        $csvData['type'] = $request->type_mcq;
                        $csvData['category'] =  preg_replace('/[^\w]/', '', $csv_data[0]);
                        $csvData['question'] = preg_replace('/[^\w]/', '', $csv_data[1]);
                        $csvData['score'] = preg_replace('/[^\w]/', '', $csv_data[2]);
                    
                        mcq_questions::insert($csvData);



                        if (@$csv_data[3] != ""  &&  @$csv_data[4] != "" ) 
                        {
                            $answer = new mcq_answer();
                            $answer->org_id =  Session::get('org_id');
                            $answer->question_id =mcq_questions::orderBy('id','Desc')->value('id');
                            $answer->answer = preg_replace('/[^\w]/', '', $csv_data[3]);                    
                            $answer->correct_answer = preg_replace('/[^\w]/', '', $csv_data[4]);   
                            $answer->save();
                        }

                        $all[] = $i++;
                    } 
                    $i++;
               
                }
                
            }catch (Exception $e) {
                report($e);
                return false;
            }
            Session::flash('message',count($all).' Data Uploaded Successfully!!');
            return back();
            exit();
            }
        }

    
    return redirect()->back();
    }


    

}
