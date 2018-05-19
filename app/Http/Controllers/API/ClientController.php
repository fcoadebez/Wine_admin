<?php

namespace App\Http\Controllers\API;

use DB;
use JWTAuth;
use \Validator;
use App\Model\Client;
use App\Model\Wine;
use App\Model\WineFav;
use App\Model\WineDrink;
use App\Model\ClientProfil;
use App\Http\APIResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;

class ClientController extends Controller
{
	public function signup(Request $request){
    $data = [];

		if($request->isMethod("POST")){
			$valid = Validator($request->all(),
				[
					'email' => 'required|email|max:75',
					'password' =>'required|max:100'
				]);

			if($valid->fails()){
				$data["alert"] = [
					"type" => "fail",
					"message" => "Merci de compléter tous les champs."
				];
				return $data;
      }

      $clientExist = Client::where("email", "=", $request->input('email'))->where("password", "=", md5($request->input('password')))->first();
      if($clientExist) {
        $data["alert"] = [
					"type" => "fail",
					"message" => "Client déjà existant !"
				];
				return $data;
      }
			DB::beginTransaction();
			$error = 0;

			$client = new Client();
			$client->email = $request->input("email");
			$client->password = md5($request->input("password"));
			$client->date_creation = date("Y-m-d H:i:s");

			if(!$client->save())
				$error++;

			if($error>=1){
				DB::rollback();
				$data["alert"] = [
					"type" => "fail",
					"message" => "Une erreur est survenue durant l'ajout de l'utilisateur. Merci de réessayer ultérieurement."
				];
				return $data;
			}

			DB::commit();
			$data["alert"] = [
				"type" => "success",
			];
			return $data;
		}

		return $data;
  }
  
  public function login(Request $request){
      $data = [];
      if($request->isMethod('POST')){
          // FORM VALIDATION
          $validator = Validator::make($request->all(), [
              'email' => 'required|email',
              'password' => 'required',
          ]);
          if ($validator->fails()){
              $data["alert"] = [
                  "type" => "fail",
                  "message" => $validator->errors()->first()
              ];
              return $data;
          }

          // user verification
		  $client = Client::where("email", "=", $request->input('email'))
							->where("password", "=", md5($request->input('password')))
							->first();

		  $client_profil = ClientProfil::where("client_id", "=", $client->id)->first();

          if(is_null($client)){
              $data["alert"] = [
                  "type" => "fail",
                  "message" => "Votre adresse email ou mot de passe est invalide."
              ];
              return $data;
		  }

		  $token = JWTAuth::fromUser($client);
		  $client->password = "";

		  if(!is_null($client_profil)){
			$winesProfil = [];
			$winesDrink = [];
			$winesFav = [];
			$drinkAll = WineDrink::get()->all();
			$favAll = WineFav::get()->all();
			$winesAll = Wine::get()->all();

			foreach($winesAll as $wine) {
				if($wine->rProfil()->first()->profil_id == $client_profil->profil_id) {
					array_push($winesProfil, $wine);
				}
			}
			foreach($drinkAll as $drink) {
				
				if($drink->user_id == $client->id) {
					array_push($winesDrink, $drink->rWines()->first());
				}
			}
			foreach($favAll as $fav) {
				
				if($fav->user_id == $client->id) {
					array_push($winesFav, $fav->rWines()->first());
				}
			}

			$data["alert"] = [
				"type" => "success",
				"profil" => true,
				"user" => $client,
				"winesProfil" => $winesProfil,
				"winesFav" => $winesFav,
				"winesDrink" => $winesDrink,
				"winesAll" => $winesAll,
				"token" => $token
			];
			return $data;
		}
		  

          $data["alert"] = [
						"type" => "success",
						"profil" => false,
						"user" => $client,
						"token" => $token
          ];
      }

      return $data;
  }
}
