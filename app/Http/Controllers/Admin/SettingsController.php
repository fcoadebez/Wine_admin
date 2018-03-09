<?php

namespace App\Http\Controllers\Admin;

use App\Model\Config;
use Illuminate\Http\Request;
use App\Model\User;
use App\Http\Controllers\Controller;
use \DB;

class SettingsController extends Controller
{

	//index
	public function index(){
		return view("admin.views.settings.index");
	}

	//user_list
	public function user_list($data_state = null){
		$data = [];
		$data["users"] = User::get();

		if(!is_null($data_state))
			$data["alert"] = $data_state["alert"];

		return view("admin.views.settings.users.list")->with($data);
	}

	//user_add
	public function user_add(Request $request){
		$data = [];

		$response = function($data){
			return view("admin.views.settings.users.add")->with($data);
		};

		if($request->isMethod("POST")){
			$valid = Validator($request->all(),
				[
					'name' => 'required|max:50',
					'firstname' => 'required|max:50',
					'email' => 'required|email|max:75',
					'password' =>'required|max:100'
				]);

			if($valid->fails()){
				$data["alert"] = [
					"type" => "warning",
					"icon" => "attention",
					"message" => "Merci de compléter tous les champs."
				];
				return $response($data);
			}

			DB::beginTransaction();
			$error = 0;

			$t = new User();
			$t->name = $request->input("name");
			$t->firstname = $request->input("firstname");
			$t->email = $request->input("email");
			$t->password = md5($request->input("password"));
			$t->date_creation = date("Y-m-d H:i:s");

			if(!$t->save())
				$error++;

			if($error>=1){
				DB::rollback();
				$data["alert"] = [
					"type" => "danger",
					"icon" => "attention",
					"message" => "Une erreur est survenue durant l'ajout de l'utilisateur. Merci de réessayer ultérieurement."
				];
				return $response($data);
			}

			DB::commit();
			$data["alert"] = [
				"type" => "success",
				"icon" => "check",
				"message" => "Utilisateur ajouté."
			];
			return $response($data);
		}

		return $response($data);
	}

	//user_edit
	public function user_edit(Request $request, $id){
		$data = [];
		$data["user"] = User::where("id", "=", $id)->first();

		if(is_null($data["user"]))
			return $this->user_list(["alert" => [
				"type" => "warning",
				"icon" => "attention",
				"message" => "Impossible d'identifié l'utilisateur demander."
			]]);

		$response = function($data){
			return view("admin.views.settings.users.edit")->with($data);
		};

		if($request->isMethod("POST")){
			$valid = Validator($request->all(),
				[
					'name' => 'required|max:50',
					'firstname' => 'required|max:50',
					'email' => 'required|email|max:75',
					'password' =>''
				]);

			if($valid->fails()){
				$data["alert"] = [
					"type" => "warning",
					"icon" => "attention",
					"message" => "Merci de compléter tous les champs."
				];
				return $response($data);
			}

			$valid = Validator($request->all(), ['password' =>'required|max:100']);
			if(!is_null($request->input("password")) && $valid->fails()){
				$data["alert"] = [
					"type" => "warning",
					"icon" => "attention",
					"message" => "Le mot de passe dépasse la taille autorisé (100 charactères max)."
				];
				return $response($data);
			}

			if($data["user"]->email != $request->input("email") && User::where("email", "=", $request->input("email"))->where("id", "=", $data["user"]->id)){
				$data["alert"] = [
					"type" => "warning",
					"icon" => "attention",
					"message" => "L'adresse email existe déjà pour un autre compte."
				];
				return $response($data);
			}

			DB::beginTransaction();
			$error = 0;

			$data["user"]->name = $request->input("name");
			$data["user"]->firstname = $request->input("firstname");
			$data["user"]->email = $request->input("email");
			if(!is_null($request->input("password")))
				$data["user"]->password = md5($request->input("password"));
			$data["user"]->date_lastupdate = date("Y-m-d H:i:s");

			if(!$data["user"]->save())
				$error++;

			if($error>=1){
				DB::rollback();
				$data["alert"] = [
					"type" => "danger",
					"icon" => "attention",
					"message" => "Une erreur est survenue durant l'edition de l'utilisateur. Merci de réessayer ultérieurement."
				];
				return $response($data);
			}

			DB::commit();
			$data["alert"] = [
				"type" => "success",
				"icon" => "check",
				"message" => "Utilisateur modifier avec succès."
			];
			return $response($data);
		}

		return $response($data);
	}

	//user_remove
	public function user_remove(Request $request, $id){
		$data = [];

		$data["user"] = User::where("id", "=", $id)->first();

		if(is_null($data["user"]))
			return $this->user_list(["alert" => [
				"type" => "warning",
				"icon" => "attention",
				"message" => "Impossible d'identifié l'utilisateur demander."
			]]);

		if(!$data["user"]->delete())
			return $this->user_list(["alert" => [
				"type" => "danger",
				"icon" => "attention",
				"message" => "Impossible de supprimer l'utilisateur."
			]]);


		return $this->user_list(["alert" => [
			"type" => "success",
			"icon" => "check",
			"message" => "Utilisateur supprimer."
		]]);
	}

	// vars_list
    public function vars_list($data_state = null){
        $data = [];
        $data["vars"] = Config::get();

        if(!is_null($data_state))
            $data["alert"] = $data_state["alert"];

        return view("admin.views.settings.vars.list")->with($data);
    }
    // var_edit
    public function var_edit(Request $request, $id){
        $data = [];
        $data["var"] = Config::where("id", "=", $id)->first();

        if(is_null($data["var"]))
            return $this->user_list(["alert" => [
                "type" => "warning",
                "icon" => "attention",
                "message" => "Impossible d'identifié la variable demander."
            ]]);

        $response = function($data){
            return view("admin.views.settings.vars.edit")->with($data);
        };

        if($request->isMethod("POST")){
            $valid = Validator($request->all(),
                [
                    'value' => 'required|min:1'
                ]);

            if($valid->fails()){
                $data["alert"] = [
                    "type" => "warning",
                    "icon" => "attention",
                    "message" => "Merci de compléter tous les champs."
                ];
                return $response($data);
            }

            DB::beginTransaction();

            $data["var"]->value = $request->input("value");
            if(!$data["var"]->save()){
                DB::rollback();
                $data["alert"] = [
                    "type" => "danger",
                    "icon" => "attention",
                    "message" => "Une erreur est survenue durant l'edition de la variable. Merci de réessayer ultérieurement."
                ];
                return $response($data);
            }

            DB::commit();
            $data["alert"] = [
                "type" => "success",
                "icon" => "check",
                "message" => "Variable modifier avec succès."
            ];
            return $response($data);
        }

        return $response($data);
    }
}

