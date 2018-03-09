<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Model\Realisation;
use App\Http\Controllers\Controller;
use \Validator;
use \DB;

class PortfolioController extends Controller
{
   // list
	public function list($data_state = null)
	{
		$data = [];
		$data["realisations"] = Realisation::get();

		if($data_state == null){
			$data["alert"] = $data_state["alert"];
		}

		return view("admin.views.portfolio.list")->with($data);
	}

   // add
	public function add(Request $request)
	{
		$data = [];

		$response = function($data){
			return view("admin.views.portfolio.add")->with($data);
		};

		if($request->isMethod("POST")){
			$valid = Validator($request->all(),
				[
					'name' => 'required|max:200',
					'type' => 'required|max:200',
					'description' => '',
					'miniature' => '',
					'image' => '',
					'link' => ''
				]);

			if($valid->fails()){

				$request->session()->flash('alert', ["warning", "attention", "Merci de compléter tous les champs."]);

				// $data["alert"] = [
				// 	"type" => "warning",
				// 	"icon" => "attention",
				// 	"message" => "Merci de compléter tous les champs."
				// ];
				return $response($request);
			}
			$valid = Validator($request->all(),
				[
					'link' => 'url'
				]);

			if($request->input("link") != null && $valid->fails()){

				$request->session()->flash('alert', ["warning", "attention", "Merci de saisir une adresse url valide."]);

				// $data["alert"] = [
				// 	"type" => "warning",
				// 	"icon" => "attention",
				// 	"message" => "Merci de saisir une adresse url valide."
				// ];

				return $response($request);
			}

			$validator = Validator::make(
				$request->all(),
				[
					"image" => "file|max:3512|image|mimes:jpeg,jpg,bmp,png"
				]
			);
			if ($validator->fails()) {

				$request->session()->flash('alert', ["warning", "attention", "L'image est requise et doit être compris dans un format d'images (.jpg, .jpeg, .bmp ou .png) et ne pas dépassé 3 Mo."]);

				// $data["alert"] = [
				// 	"type" => "warning",
				// 	"icon" => "attention",
				// 	"message" => "L'image est requise et doit être compris dans un format d'images (.jpg, .jpeg, .bmp ou .png) et ne pas dépassé 3 Mo."
				// ];

				return $response($request);
			}

			$validator = Validator::make(
				$request->all(),
				[
					"miniature" => "file|max:3512|image|mimes:jpeg,jpg,bmp,png"
				]
			);
			if ($validator->fails()) {

				$request->session()->flash('alert', ["warning", "attention", "L'image est requise et doit être compris dans un format d'images (.jpg, .jpeg, .bmp ou .png) et ne pas dépassé 3 Mo."]);

				// $data["alert"] = [
				// 	"type" => "warning",
				// 	"icon" => "attention",
				// 	"message" => "La miniature est requise et doit être compris dans un format d'images (.jpg, .jpeg, .bmp ou .png) et ne pas dépassé 3 Mo."
				// ];

				return $response($request);
			}
			if ($request->hasFile("miniature") != null && !(function() use ($request) {
				$t = getimagesize($request->miniature->path());
				return ($t[0] == "800" && $t[1] == "800");
			})()) {

				$request->session()->flash('alert', ["warning", "attention", "L'image miniature doit avoir la taille 800px de large sur 800px de haut."]);

				// $data["alert"] = [
				// 	"type" => "warning",
				// 	"icon" => "attention",
				// 	"message" => "L'image miniature doit avoir la taille 800px de large sur 800px de haut."
				// ];

				return $response($request);
			}

			DB::beginTransaction();
			$error = 0;

			$t = new Realisation();
			$t->name = $request->input("name");
			$t->type = $request->input("type");
			$t->description = $request->input("description");
			$t->link = $request->input("link");

			if ($request->hasFile("miniature") && $request->file("miniature")->isValid()) {
				$t->miniature = 'data: '.mime_content_type($request->miniature->path()).';base64,'.base64_encode(file_get_contents($request->miniature->path()));
			}
			if ($request->hasFile("image") && $request->file("image")->isValid()) {
				$t->image = 'data: '.mime_content_type($request->image->path()).';base64,'.base64_encode(file_get_contents($request->image->path()));
			}
			$t->date_creation = date("Y-m-d H:i:s");
			$t->user_id = $request->session()->get('user')->id;

			if(!$t->save())
				$error++;

			if($error>=1){
				DB::rollback();

				$request->session()->flash('alert', ["danger", "attention", "Une erreur est survenue durant l'ajout de la réalisation. Merci de réessayer ultérieurement."]);

				// $data["alert"] = [
				// 	"type" => "danger",
				// 	"icon" => "attention",
				// 	"message" => "Une erreur est survenue durant l'ajout de la réalisation. Merci de réessayer ultérieurement."
				// ];

				return $response($data);
			}

			DB::commit();

			$request->session()->flash('alert', ["success", "check", "Réalisation ajouté."]);

			// $data["alert"] = [
			// 	"type" => "success",
			// 	"icon" => "check",
			// 	"message" => "Réalisation ajouté."
			// ];

			return $response($data);
		}

		return $response($data);
	}

   // edit
	public function edit(Request $request, $id)
	{
		
		$data = [];
		$data["realisation"] = Realisation::where("id", "=", $id)->first();
		if($data["realisation"] == null)		{
			return redirect("/admin/portfolio/list");
		}

		$response = function($data){
			return view("admin.views.portfolio.edit")->with($data);
		};

		if($request->isMethod("POST")){
			$valid = Validator($request->all(),
				[
					'name' => 'required|max:200',
					'type' => 'required|max:200',
					'description' => '',
					'miniature' => '',
					'image' => '',
					'link' => ''
				]);

			if($valid->fails()){

				$request->session()->flash('alert', ["warning", "attention", "Merci de compléter tous les champs."]);

				// $data["alert"] = [
				// 	"type" => "warning",
				// 	"icon" => "attention",
				// 	"message" => "Merci de compléter tous les champs."
				// ];

				return $response($request);
			}
			$valid = Validator($request->all(),
				[
					'link' => 'url'
				]);

			if($request->input("link") != null && $valid->fails()){

				$request->session()->flash('alert', ["warning", "attention", "Merci de saisir une adresse url valide."]);

				// $data["alert"] = [
				// 	"type" => "warning",
				// 	"icon" => "attention",
				// 	"message" => "Merci de saisir une adresse url valide."
				// ];
				return $response($request);
			}

			$validator = Validator::make(
				$request->all(),
				[
					"image" => "file|max:3512|image|mimes:jpeg,jpg,bmp,png"
				]
			);
			if ($request->hasFile("image") != null && $validator->fails()) {

				$request->session()->flash('alert', ["warning", "attention", "L'image est requise et doit être compris dans un format d'images (.jpg, .jpeg, .bmp ou .png) et ne pas dépassé 3 Mo."]);

				// $data["alert"] = [
				// 	"type" => "warning",
				// 	"icon" => "attention",
				// 	"message" => "L'image est requise et doit être compris dans un format d'images (.jpg, .jpeg, .bmp ou .png) et ne pas dépassé 3 Mo."
				// ];

				return $response($request);
			}

			$validator = Validator::make(
				$request->all(),
				[
					"miniature" => "file|max:3512|image|mimes:jpeg,jpg,bmp,png"
				]
			);
			if ($request->hasFile("miniature") != null && $validator->fails()) {

				$request->session()->flash('alert', ["warning", "attention", "L'image est requise et doit être compris dans un format d'images (.jpg, .jpeg, .bmp ou .png) et ne pas dépassé 3 Mo."]);

				// $data["alert"] = [
				// 	"type" => "warning",
				// 	"icon" => "attention",
				// 	"message" => "La miniature est requise et doit être compris dans un format d'images (.jpg, .jpeg, .bmp ou .png) et ne pas dépassé 3 Mo."
				// ];

				return $response($request);
			}
			if ($request->hasFile("miniature") != null && !(function() use ($request) {
				$t = getimagesize($request->miniature->path());
				return ($t[0] == "800" && $t[1] == "800");
			})()) {

				$request->session()->flash('alert', ["warning", "attention", "L'image miniature doit avoir la taille 800px de large sur 800px de haut."]);

				// $data["alert"] = [
				// 	"type" => "warning",
				// 	"icon" => "attention",
				// 	"message" => "L'image miniature doit avoir la taille 800px de large sur 800px de haut."
				// ];
				return $response($data);
			}

			DB::beginTransaction();
			$error = 0;

			$data["realisation"]->name = $request->input("name");
			$data["realisation"]->type = $request->input("type");
			$data["realisation"]->description = $request->input("description");
			$data["realisation"]->link = $request->input("link");

			if ($request->hasFile("miniature") && $request->file("miniature")->isValid()) {
				$data["realisation"]->miniature = 'data: '.mime_content_type($request->miniature->path()).';base64,'.base64_encode(file_get_contents($request->miniature->path()));
			}
			if ($request->hasFile("image") && $request->file("image")->isValid()) {
				$data["realisation"]->image = 'data: '.mime_content_type($request->image->path()).';base64,'.base64_encode(file_get_contents($request->image->path()));
			}
			$data["realisation"]->date_creation = date("Y-m-d H:i:s");
			$data["realisation"]->user_id = $request->session()->get('user')->id;

			if(!$data["realisation"]->save())
				$error++;

			if($error>=1){
				DB::rollback();

				$request->session()->flash('alert', ["danger", "attention", "Une erreur est survenue durant l'edition de la réalisation. Merci de réessayer ultérieurement."]);

				// $data["alert"] = [
				// 	"type" => "danger",
				// 	"icon" => "attention",
				// 	"message" => "Une erreur est survenue durant l'edition de la réalisation. Merci de réessayer ultérieurement."
				// ];

				return $response($request);
			}

			DB::commit();

			$request->session()->flash('alert', ["success", "check", "Réalisation modifié avec succès."]);

			// $data["alert"] = [
			// 	"type" => "success",
			// 	"icon" => "check",
			// 	"message" => "Réalisation modifié avec succèss."
			// ];

			return $response($data);
		}

		return $response($data);
	}

   // remove
	public function remove(Request $request, $id)
	{
		$data = [];
		$data["realisation"] = Realisation::where("id", "=", $id)->first();

		$response = function(){
            return redirect("/admin/portfolio/list");
        };
		if($data["realisation"] == null){
			return $response();
		}

		if(!$data["realisation"]->delete()){

			$request->session()->flash('alert', ["danger", "attention", "Une erreur est survenue durant la suppression de la réalisation. Merci de réessayer ultérieurement."]);

			// $data["alert"] = [
			// 	"type" => "danger",
			// 	"icon" => "attention",
			// 	"message" => "Une erreur est survenue durant la suppression de la réalisation. Merci de réessayer ultérieurement."
			// ];
            return $response();
        }

		$request->session()->flash('alert', ["success", "check", "Réalisation supprimé."]);

		// $data["alert"] = [
		// 	"type" => "success",
		// 	"icon" => "check",
		// 	"message" => "Réalisation supprimé."
		// ];

        return $response();
    }
}
