<?php

namespace App\Http\Controllers\API;

use DB;
use JWTAuth;
use \Validator;
use App\Model\Client;
use App\Model\WineFav;
use App\Http\APIResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;

class WineController extends Controller
{
	public function favorite(Request $request){
    	$data = [];

		if($request->isMethod("POST")){
			$valid = Validator($request->all(),
				[
					'user_id' => 'required',
					'wine_id' =>'required'
				]);

			if($valid->fails()){
				$data["alert"] = [
					"type" => "fail",
					"message" => "Un problème est survenu"
				];
				return $data;
      }
			DB::beginTransaction();
			$error = 0;

			$client = new WineFav();
			$client->user_id = $request->input("user_id");
			$client->wine_id = $request->input("wine_id");

			if(!$client->save())
				$error++;

			if($error>=1){
				DB::rollback();
				$data["alert"] = [
					"type" => "fail",
					"message" => "Une erreur est survenue. Merci de réessayer ultérieurement."
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
  public function unFavorite(Request $request){
	$data = [];

	if($request->isMethod("POST")){
		$valid = Validator($request->all(),
			[
				'user_id' => 'required',
				'wine_id' =>'required'
			]);

		if($valid->fails()){
			$data["alert"] = [
				"type" => "fail",
				"message" => "Un problème est survenu"
			];
			return $data;
  		}
		DB::beginTransaction();
		$error = 0;

		$favToRemove = WineFav::where('user_id', '=', $request->input("user_id"))
													->where('wine_id', '=', $request->input("wine_id"))
													->first();

		if(!$favToRemove->delete())
			$error++;

		if($error>=1){
			DB::rollback();
			$data["alert"] = [
				"type" => "fail",
				"message" => "Une erreur est survenue. Merci de réessayer ultérieurement."
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
}
