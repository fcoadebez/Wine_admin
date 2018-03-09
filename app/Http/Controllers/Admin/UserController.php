<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Model\User;
use Illuminate\Support\Facades\URL;
use Validator;

class UserController extends Controller
{
    public function login(Request $request){
        $data = [];
        $data["inputs"] = $request->all();

        if ($request->session()->exists('user')) {
            return redirect("/admin/wine/list");
        }

        $response = (function($data = null){
            return view("admin.views.user.login")->with($data);
        });

        if($request->isMethod('POST')){
            // FORM VALIDATION
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);
            if ($validator->fails()){
                $data["inputs"]["email"] = "";
                $data["alert"] = [
                    "type" => "warning",
                    "icon" => "attention",
                    "message" => $validator->errors()->first()
                ];
                return $response($data);
            }

            // user verification
            $user = User::where("email", "=", $request->input('email'))->where("password", "=", md5($request->input('password')))->first();
            if(is_null($user)){
                $data["inputs"]["email"] = "";
                $data["alert"] = [
                    "type" => "warning",
                    "icon" => "attention",
                    "message" => "Votre adresse email ou mot de passe est invalide."
                ];
                return $response($data);
            }

            $request->session()->put('user', $user);
            return redirect("/admin/");
        }

        return $response($data);
    }

    public function password(Request $request){
        $data = [];

        $response = function($data){
            return view("admin.views.user.password")->with($data);
        };

        if($request->isMethod('POST')){
            // FORM VALIDATION
            $validator = Validator::make($request->all(), [
                'password_good' => 'required',
                'password_new' => 'required',
                'password_new2' => 'required',
            ]);
            if ($validator->fails()){
                $data["alert"] = [
                    "type" => "warning",
                    "icon" => "attention",
                    "message" => "Merci de compléter tous les champs."
                ];
                return $response($data);
            }

            $user = User::where("id", "=", $request->session()->get("user")->id)->first();

            if(is_null($user)){
                $data["alert"] = [
                    "type" => "danger",
                    "icon" => "attention",
                    "message" => "Impossible de trouver le compte utilisateur à modifier."
                ];
                return $response($data);
            }
            //dd(!($user->password == md5($request->input("password_good"))));
            if(!($user->password == md5($request->input("password_good")))) {
                $data["alert"] = [
                    "type" => "warning",
                    "icon" => "attention",
                    "message" => "Le mot de passe de votre compte n'est pas celui saisie."
                ];
                return $response($data);
            }
            if(md5($request->input("password_new")) != md5($request->input("password_new2"))) {
                $data["alert"] = [
                    "type" => "warning",
                    "icon" => "attention",
                    "message" => "Le nouveau mot de passe et ça confirmation ne sont pas identique."
                ];
                return $response($data);
            }

            $user->password = md5($request->input("password_new2"));
            //dd($user->password);

            if(!$user->save()){
                $data["alert"] = [
                    "type" => "warning",
                    "icon" => "attention",
                    "message" => "Impossible de modifier votre mot de passe."
                ];
                return $response($data);
            }

            $data["redirect"] = [
                "url" => Url::to("/admin"),
                "time" => 2
            ];
            $data["alert"] = [
                "type" => "success",
                "icon" => "check",
                "message" => "Mot de passe modifier avec succès. Vous allez être rediriger ..."
            ];

            $request->session()->put('user', User::where("id", "=", $request->session()->get("user")->id)->first());
            return $response($data);
        }

        return $response($data);
    }

    public function logout(Request $request){
        $request->session()->flush();
        return redirect("/admin/user/login");
    }
}
