<?php

namespace App\Http\Controllers\Admin;

use App\Model\Devis;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Validator;

class QuoteController extends Controller
{
	// list
	public function list ($data_state = null){
		$data = [];

		if($data_state != null && isset($data_state['alert'])){
			$data["alert"] = $data_state["alert"];
		}

		$data["quotes"] = Devis::where('date_archived', "=", NULL)
		->where('in', "=", "0")
		->orderBy("date_creation", "asc")
		->orderBy("date_update", "asc")
		->get();

		return view("admin.views.quote.list")->with($data);
	}

	// discount
	public function discount (Request $request, $id){
		$data = [];
		$data['quote'] = Devis::where("id", "=", $id)->firstOrFail();

		$response = function($data){
			return view("admin.views.quote.discount")->with($data);
		};

		if($request->isMethod('POST')){
			$valid = Validator($request->all(),
				[
					'discount' =>'required|integer|min:0|max:100'
				]);

			if($valid->fails()){

				$request->session()->flash('alert', ["warning", "attention", "Merci de saisir une remise en pourcentage."]);

				// $data["alert"] = [
				// 	"type" => "warning",
				// 	"icon" => "attention",
				// 	"message" => "Merci de saisir une remise en pourcentage."
				// ];
				return $response($data);
			}

			DB::beginTransaction();
			$error = 0;

			$data["quote"]->discount = $request->input("discount");

			if(!$data["quote"]->save()){
				$error++;
			}

			if($error >= 1){
				DB::rollback();

				$request->session()->flash('alert', ["danger", "attention", "Impossible d'appliquer le pourcentage de remise."]);

				// $data["alert"] = [
				// 	"type" => "danger",
				// 	"icon" => "attention",
				// 	"message" => "Impossible d'appliquer le pourcentage de remise."
				// ];
				return $response($data);
			}

			DB::commit();

			$request->session()->flash('alert', ["success", "check", "Pourcentage de remise appliquer."]);

			// $data["alert"] = [
			// 	"type" => "success",
			// 	"icon" => "check",
			// 	"message" => "Pourcentage de remise appliquer."
			// ];
		}

		return $response($data);
	}
	// archive
	public function archive (Request $request, $id){
		$data = [];

		$response = function(){
			return redirect("/admin/quote/list");
		};

		$data['quote'] = Devis::where("id", "=", $id)->firstOrFail();
		DB::beginTransaction();
		$error = 0;

		$data["quote"]->date_archived = date('Y-m-d H:i:s');

		if(!$data["quote"]->save()){
			DB::rollback();

			$request->session()->flash('alert', ["danger", "attention", "Impossible d'archiver la demande de devis."]);

			// $data["alert"] = [
			// 	"type" => "danger",
			// 	"icon" => "attention",
			// 	"message" => "Impossible d'archiver la demande de devis."
			// ];
			return $response();
		}

		DB::commit();

		$request->session()->flash('alert', ["success", "check", "Devis archivé."]);

		// $data["alert"] = [
		// 	"type" => "success",
		// 	"icon" => "check",
		// 	"message" => "Devis archivé."
		// ];
		return $response();
	}
	// Clean un end quote
	public function clean_unend_quote (Request $request, $id){
		// TODO
		/*$data = [];

		$response = function($data){
			return $this->list($data);
		};

		$data['quote'] = Devis::where("id", "=", $id)->firstOrFail();
		DB::beginTransaction();
		$error = 0;

		$data["quote"]->date_archived = date('Y-m-d H:i:s');

		if(!$data["quote"]->save()){
			DB::rollback();
			$data["alert"] = [
				"type" => "danger",
				"icon" => "attention",
				"message" => "Impossible d'archiver la demande de devis."
			];
			return $response($data);
		}

		DB::commit();
		$data["alert"] = [
			"type" => "success",
			"icon" => "check",
			"message" => "Devis archivé."
		];

		return $response($data);*/
	}
}
