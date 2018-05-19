<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Model\Question;
use App\Model\Wine;
use App\Model\QuestionResponse;
use App\Model\ClientResponse;
use App\Model\ClientProfil;
use App\Model\Profil;
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
        $profils = [];
        $wines = [];
        $winesAll = Wine::get()->all();

        DB::beginTransaction();
        $error = 0;

        if($request->input("reset") == true) {
            $profil_is_set = ClientProfil::where('client_id', '=', $request->input("userId"))->first()->delete();
            $pastResponses = ClientResponse::where('user_id', '=', $request->input("userId"))->delete();
        }

        // $profil_is_set = ClientProfil::where('client_id', '=', $request->input("userId"))->first();
        // $pastResponses = ClientResponse::where('user_id', '=', $request->input("userId"))->delete();

        // if($pastResponses) {
        //     foreach($pastResponses as $pastResponse) {
        //         $pastResponse->delete();
        //     }
        //     // die();
        //     if ($profil_is_set !== null)
        //         $profil_is_set->delete();
        // }

        foreach($request->input('responses') as $response) {

            $profil = QuestionResponse::where("id", "=", $response["response_id"])->first()->rProfil()->first();

            array_push($profils, $profil->profil);

            $clientResponse = new ClientResponse();
            $clientResponse->user_id = $request->input("userId");
            $clientResponse->question_id = $response["question_id"];
            $clientResponse->response_id = $response["response_id"];

            if(!$clientResponse->save()) {
                $error++;
            }

            if($error >= 1){
                DB::rollback();
                $data["alert"] = [
                    "type" => "fail",
                    "message" => "Une erreur est survenue durant l'ajout des questions."
                ];
                return $data;
            }
        }

        $profilCount = array_count_values($profils);
        $profil = array_search(max($profilCount), $profilCount);

        $profil_client = Profil::where("profil", "=", $profil)->first()->id;

        $clientProfil = new ClientProfil();
        $clientProfil->client_id = $request->input("userId");
        $clientProfil->profil_id = $profil_client;

        if(!$clientProfil->save())
        $error++;

        if($error >= 1){
            DB::rollback();
            $data["alert"] = [
                "type" => "fail",
                "message" => "Une erreur est survenue durant l'ajout du profil."
            ];
            return $data;
        }

        foreach($winesAll as $wine) {
            if($wine->rProfil()->first()->profil_id == $profil_client) {
                array_push($wines, $wine);
            }
        }

        DB::commit();
        $data["alert"] = [
            "type" => "success",
            "wines" => $wines,
            "winesAll" => $winesAll
        ];

        return $data;
    }
}