<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\APIResponse;
use App\Model\Devis;
use App\Model\Question;
use DB;

class ProjectController extends Controller
{
   // créer le projet, son token et récupère la première question à poser
	public function project_init(Request $request){
		$response = new APIResponse($request);
		DB::beginTransaction();

		$devis = new Devis();
		$devis->token = uniqid().uniqid();
		$devis->price = 0;
		$devis->discount = 0;
		$devis->in = 1;
		$devis->date_creation = date("Y-m-d H:i:s");

		if(!$devis->save()){
			DB::rollback();
			$response->setStatus(400);
			$response->setBody("Cannot create project configuration.");
			return $response->json();
		}

		$question = Question::orderBy("order")->first(["id", "name"]);
		$responses = $question->rQuestionReponses()->get(["id", "name", "description", "image", "price"]);

		DB::commit();
		$response->setStatus(200);
		$response->setBody(["token" => $devis->token, "question" => ["subject" => $question, "reponses" => $responses]]);

		return $response->json();
	}
	
	// finalise le projet au renvoie du formulaire de précisions des informations de contact
	public function project_end(Request $request, $token){
		$response = new APIResponse($request);
		$devis = Devis::where("token", "=", $token)->first();
		if($devis == null){
			$response->setStatus(400);
			$response->setBody("Unfind project.");
			return $response->json();
		}

		DB::beginTransaction();
		$devis->in = 0;
		$devis->state_question = 'end';
		$devis->date_archived = date("Y-m-d H:i:s");

		// calc all responses prices
		// make quote
		// send by mail

		if(!$devis->save()){
			DB::rollback();
			$response->setStatus(400);
			$response->setBody("Cannot close project.");
			return $response->json();
		}

		DB::commit();
		$response->setStatus(200);
		$response->setBody(["token" => $devis->token]);

		return $response->json();
	}
	


	// envoie l'état de l'avancement du projet.
	public function project_send(Request $request, $token){
		// Verify if project is set
		$valid = Validator($request->all(), [
			'responses' => 'json'
		]);
		if($valid->fails()){
			$response->setStatus(400);
			$response->setBody("Bad request");
			return $response->json();
		}

		// Get project

		$response = new APIResponse($request);
		DB::beginTransaction();

		$devis->token = uniqid().uniqid();
		$devis->price = 0;
		$devis->discount = 0;
		$devis->in = 1;
		$devis->date_creation = date("Y-m-d H:i:s");

		if(!$devis->save()){
			DB::rollback();
			$response->setStatus(400);
			$response->setBody("Cannot save project state configuration.");
			return $response->json();
		}

		$question = Question::orderBy("order")->first(["id", "name"]);
		$responses = $question->rQuestionReponses()->get(["id", "name", "description", "image", "price"]);

		DB::commit();
		$response->setStatus(200);
		$response->setBody(["token" => $devis->token, "question" => ["subject" => $question, "reponses" => $responses]]);

		return $response->json();
	}
	
	// finalise le projet.
	public function project_finalise(Request $request, $token){

	}
	
	// précise qu'il y a eu un fail.
	public function project_fail(Request $request, $token){

	}
	
	// fait payer le projet.
	public function project_pay(Request $request, $token){

	}
}
