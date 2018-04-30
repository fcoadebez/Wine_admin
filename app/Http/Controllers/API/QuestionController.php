<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Model\Question;
use App\Model\QuestionResponse;
use App\Model\ClientResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use App\Http\APIResponse;
use \Validator;
use \DB;

class QuestionController extends Controller
{
    public function getAll(){
        $data = [];

        $questions = Question::get()->all();

        for($i=0; $i<count($questions); $i++) {
            $data[$i] = [
                "question" => $questions[$i],
                "responses" => $questions[$i]->rQuestionResponse()->get()
            ];
        }

		return json_encode($data);
	}
    public function storeResponses(Request $request){
        $data = [];

        DB::beginTransaction();
        $error = 0;

        foreach($request->input('responses') as $response) {
            $clientResponse = new ClientResponse();
            $clientResponse->user_id = $request->input("userId");
            $clientResponse->question_id = $response["question_id"];
            $clientResponse->response_id = $response["response_id"];

            if(!$clientResponse->save())
            $error++;

            if($error>=1){
                DB::rollback();
                $data["alert"] = [
                    "type" => "fail",
                    "message" => "Une erreur est survenue durant l'ajout des questions."
                ];
                return $data;
            }
        }

        

        DB::commit();
        $data["alert"] = [
            "type" => "success",
        ];

        return $data;
    }
}
