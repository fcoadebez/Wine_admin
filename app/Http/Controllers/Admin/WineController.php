<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Model\Wine;
use App\Model\Taste;
use App\Model\Question;
use App\Model\QuestionReponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use \Validator;
use \DB;

class WineController extends Controller
{
    // list
    public function list()
    {
        $data = [];
        $data["vins"] = Wine::get();

        return view("admin.views.wine.list")->with($data);
    }

    public function autocomplete(Request $request)
    {
        $term=$request->term;
        $data = Taste::where('name','LIKE','%'.$term.'%')
        ->take(10)
        ->get();
        $result=array();

        foreach ($data as $key => $v){
            array_push($result, $v->name);
        }
        return response()->json($result);
    }

    public function add(Request $request)
    {
        $data = [];

        $response = function ($data) {
            return view("admin.views.wine.add")->with($data);
        };

        if ($request->isMethod("POST")) {
            $valid = Validator($request->all(),
                [
                    'denomination' => 'required|min:2|max:200',
                    'millesime' => 'required',
                    'photo' => 'required',
                    'categorie' => 'required',
                    'description' => 'required|min:2|max:200',
                    'prix' => 'required'
                ]);

            if ($valid->fails()) {
                $data["alert"] = [
                    "type" => "warning",
                    "icon" => "attention",
                    "message" => "Merci de compléter tous les champs"
                ];
            }

            DB::beginTransaction();
            $error = 0;

            $timestamp = Carbon::now()->timestamp;

            $photo = $timestamp . "_" . $request->file("photo")->getClientOriginalName();

            $photo_name = str_replace(" ", "_", $photo);

            $request->file('photo')->move(public_path() . "/vins/", $photo_name);

            $vin = new Wine();
            $vin->denomination = $request->input("denomination");
            $vin->annee = $request->input("millesime");
            if ($request->hasFile("photo")) {
                $vin->photo = $photo_name;
            }
            $vin->categorie = $request->input("categorie");
            $vin->description = $request->input("description");
            $vin->prix = $request->input("prix");

            if (!$vin->save())
                $error++;

            if ($error >= 1) {
                DB::rollback();
                $data["alert"] = [
                    "type" => "danger",
                    "icon" => "attention",
                    "message" => "Une erreur est survenue durant l'ajout du vin. Merci de réessayer ultérieurement."
                ];
                return $response($data);
            }



            DB::commit();
            $data["alert"] = [
                "type" => "success",
                "icon" => "check",
                "message" => "Vin ajouté avec succès."
            ];

            return redirect("/admin/wine/list");
        } else {
            $tastes = Taste::get();
            $result = [];
            foreach ($tastes as $taste) {
                array_push($result, $taste->name);
            }
            return $response($result);
        }

        return $response($data);
    }

    public function edit(Request $request, $id)
    {
        $data = [];
        $data["id"] = $id;
        $data["wine"] = Wine::where("id", "=", $id)->first();

        $response = function ($data) {

            return view("admin.views.wine.edit")->with($data);
        };

        if ($data["wine"] == null) {
            return redirect("/admin/vins/list");
        }

        if ($request->isMethod("POST")) {
            $valid = Validator($request->all(),
                [
                    'denomination' => 'required|min:2|max:200',
                    'millesime' => 'required',
                    'categorie' => 'required',
                    'description' => 'required|min:2|max:200',
                    'prix' => 'required'
                ]);

            if ($valid->fails()) {
                $data["alert"] = [
                    "type" => "warning",
                    "icon" => "attention",
                    "message" => "Merci de compléter tous les champs."
                ];
                return $response($data);
            }

            DB::beginTransaction();
            $error = 0;

            if ($request->hasFile("photo")) {
                $timestamp = Carbon::now()->timestamp;

                $photo = $timestamp . "_" . $request->file("photo")->getClientOriginalName();
                $photo_name = str_replace(" ", "_", $photo);

                File::delete(public_path() . "/vins/" . $data["wine"]->photo);

                $request->file('photo')->move(public_path() . "/vins/", $photo_name);
                $data["wine"]->photo = $photo_name;

                
            }

            $data["wine"]->denomination = $request->input("denomination");
            $data["wine"]->annee = $request->input("millesime");
            $data["wine"]->categorie = $request->input("categorie");
            $data["wine"]->description = $request->input("description");
            $data["wine"]->prix = $request->input("prix");

            if (!$data["wine"]->save())
                $error++;

            if ($error >= 1) {
                DB::rollback();
                $data["alert"] = [
                    "type" => "danger",
                    "icon" => "attention",
                    "message" => "Une erreur est survenue durant la modification du vin. Merci de réessayer ultérieurement."
                ];

                return $response($data);
            }

            DB::commit();
            $data["alert"] = [
                "type" => "success",
                "icon" => "check",
                "message" => "Vin modifié."
            ];
            return $response($data);
        }

        return $response($data);
    }

    public function order_up($id)
    {
        DB::beginTransaction();
        $t = Wine::where('id', "=", $id);
        if ($t->count() != 1) {
            return redirect('/admin/wine/list')->with('alert', [
                "type" => "danger",
                "icon" => "attention",
                "message" => "Impossible d'identifié la vin à re-ordonné."
            ]);
        }

        $t = $t->first();
        $t->order = $t->order - 1;

        $a = Wine::where('order', "=", $t->order);
        if ($a->count() != 1) {
            return redirect('/admin/wine/list')->with('alert', [
                "type" => "danger",
                "icon" => "attention",
                "message" => "Une erreur s'est produite durant le re-ordonnage des vins. Merci de réessayer ultérieurement."
            ]);
        }
        $a = $a->first();
        $a->order = $a->order + 1;

        if (!$t->save() || !$a->save()) {
            DB::rollback();
            return redirect('/admin/wine/list')->with('alert', [
                "type" => "danger",
                "icon" => "attention",
                "message" => "Une erreur s'est produite durant le re-ordonnage des vins. Merci de réessayer ultérieurement."
            ]);
        }

        DB::commit();
        return redirect('/admin/wine/list')->with('alert', [
            "type" => "success",
            "icon" => "check",
            "message" => "Vin re-ordonné."
        ]);
    }

    public function order_down($id)
    {
        DB::beginTransaction();
        $t = Question::where('id', "=", $id);
        if ($t->count() != 1) {
            return redirect('/admin/wine/list')->with('alert', [
                "type" => "danger",
                "icon" => "attention",
                "message" => "Impossible d'identifié le vin à re-ordonné."
            ]);
        }

        $t = $t->first();
        $t->order = $t->order + 1;

        $a = Wine::where('order', "=", $t->order);
        if ($a->count() != 1) {
            return redirect('/admin/wine/list')->with('alert', [
                "type" => "danger",
                "icon" => "attention",
                "message" => "Une erreur s'est produite durant le re-ordonnage des vins. Merci de réessayer ultérieurement."
            ]);
        }
        $a = $a->first();
        $a->order = $a->order - 1;

        if (!$t->save() || !$a->save()) {
            DB::rollback();
            return redirect('/admin/wine/list')->with('alert', [
                "type" => "danger",
                "icon" => "attention",
                "message" => "Une erreur s'est produite durant le re-ordonnage des vins. Merci de réessayer ultérieurement."
            ]);
        }

        DB::commit();
        return redirect('/admin/wine/list')->with('alert', [
            "type" => "success",
            "icon" => "check",
            "message" => "Vin re-ordonné."
        ]);
    }

    public function remove($id)
    {
        DB::beginTransaction();
        $t = Wine::where('id', "=", $id);
        $qs = Wine::where('id', "<>", $id)->get();
        if ($t->count() != 1) {
            return redirect('/admin/wine/list')->with('alert', [
                "type" => "danger",
                "icon" => "attention",
                "message" => "Impossible d'identifié le vin à supprimer."
            ]);
        }


        $t = $t->first();

        File::delete(public_path() . "/vins/" . $t->photo);

        if (!$t->delete()) {
            DB::rollback();
            return redirect('/admin/wine/list')->with('alert', [
                "type" => "danger",
                "icon" => "attention",
                "message" => "Une erreur s'est produite durant la suppression du vin. Merci de réessayer ultérieurement."
            ]);
        }

        DB::commit();
        return redirect('/admin/wine/list')->with('alert', [
            "type" => "success",
            "icon" => "check",
            "message" => "Vin supprimé."
        ]);
    }

    public function choices($idQuestion, $data_state = null)
    {
        $data = [];
        $data["idQuestion"] = $idQuestion;
        $data["choices"] = QuestionReponse::where("question_id", "=", $idQuestion)->get();

        if ($data_state == null) {
            $data["alert"] = $data_state["alert"];
        }

        return view("admin.views.configurator.choices.list")->with($data);
    }

    public function choice_add(Request $request, $idQuestion)
    {
        $data = [];
        $data["idQuestion"] = $idQuestion;
        $data["questions"] = Question::orderBy("order", "asc")->get();

        $response = function ($data) {
            return view("admin.views.configurator.choices.add")->with($data);
        };

        if ($request->isMethod("POST")) {
            $valid = Validator($request->all(),
                [
                    'name' => 'required|max:100',
                    'description' => 'required',
                    'image' => '',
                    'question_next_end' => '',
                    'question_next_id' => '',
                    'price' => 'required|regex:/^[0-9]+[.,]?[0-9]{1,2}$/'
                ]);

            if ($valid->fails()) {
                $data["alert"] = [
                    "type" => "warning",
                    "icon" => "attention",
                    "message" => "Merci de compléter tous les champs."
                ];
                return $response($data);
            }

            if ($request->input("question_next_id") == null && $request->input("question_next_end") == null) {
                $data["alert"] = [
                    "type" => "warning",
                    "icon" => "attention",
                    "message" => "Merci de préciser la suite (question suivante, question finale) en cas de choix de cette réponse."
                ];
                return $response($data);
            }
            if ($request->input("question_next_id") != null && Question::where("id", "=", $request->input("question_next_id"))->count() <= 0) {
                $data["alert"] = [
                    "type" => "warning",
                    "icon" => "attention",
                    "message" => "La question suivante n'as pas été trouvé."
                ];
                return $response($data);
            }

            $validator = Validator::make(
                $request->all(),
                [
                    "image" => "file|max:3512|image|mimes:jpeg,jpg,bmp,png"
                ]
            );
            if ($validator->fails()) {
                $data["alert"] = [
                    "type" => "warning",
                    "icon" => "attention",
                    "message" => "L'image est requise et doit être compris dans un format d'images (.jpg, .jpeg, .bmp ou .png) et ne pas dépassé 3 Mo."
                ];
                return $response($data);
            }
            /*if ($request->hasFile("image") != null && !(function() use ($request) {
                $t = getimagesize($request->image->path());
                return ($t[0] == "800" && $t[1] == "800");
            })()) {
                $data["alert"] = [
                    "type" => "warning",
                    "icon" => "attention",
                    "message" => "L'image doit avoir la taille 800px de large sur 800px de haut."
                ];
                return $response($data);
            }*/

            DB::beginTransaction();
            $error = 0;

            $t = new QuestionReponse();
            $t->question_id = $idQuestion;
            $t->name = $request->input("name");
            $t->description = $request->input("description");
            if ($request->hasFile("image") && $request->file("image")->isValid()) {
                $t->image = 'data: ' . mime_content_type($request->image->path()) . ';base64,' . base64_encode(file_get_contents($request->image->path()));
            }
            $t->question_next_id = (is_null($request->input("question_next_end")) || $request->input("question_next_end") != "on" ? $request->input("question_next_id") : null);
            $t->question_next_end = (is_null($request->input("question_next_end")) || $request->input("question_next_end") != "on" ? false : true);
            $t->price = str_replace(",", ".", $request->input("price"));
            $t->user_id = $request->session()->get('user')->id;

            if (!$t->save())
                $error++;

            if ($error >= 1) {
                DB::rollback();
                $data["alert"] = [
                    "type" => "danger",
                    "icon" => "attention",
                    "message" => "Une erreur est survenue durant l'ajout de la réponse. Merci de réessayer ultérieurement."
                ];
                return $response($data);
            }

            DB::commit();
            $data["alert"] = [
                "type" => "success",
                "icon" => "check",
                "message" => "Réponse ajouté."
            ];

            $request->session()->flash('alert', $data["alert"]);
            return redirect("/admin/configurator/".$idQuestion."/choice/add");
        }

        return $response($data);
    }

    public function choice_edit(Request $request, $idQuestion, $idReponse)
    {
        $data = [];
        $data["idQuestion"] = $idQuestion;
        $data["idReponse"] = $idReponse;
        $data["questions"] = Question::orderBy("order", "asc")->get();
        $data["reponse"] = QuestionReponse::where("id", "=", $idReponse)->where('question_id', "=", $idQuestion)->first();

        if (is_null($data["reponse"])) {
            return redirect('/admin/configurator/' . $idQuestion . '/choices');
        }

        $response = function ($data) {
            return view("admin.views.configurator.choices.edit")->with($data);
        };

        if ($request->isMethod("POST")) {
            $valid = Validator($request->all(),
                [
                    'name' => 'required|max:100',
                    'description' => 'required',
                    'image' => '',
                    'question_next_end' => '',
                    'question_next_id' => '',
                    'price' => 'required|regex:/^[0-9]+[.,]?[0-9]{1,2}$/'
                ]);

            if ($valid->fails()) {
                $data["alert"] = [
                    "type" => "warning",
                    "icon" => "attention",
                    "message" => "Merci de compléter tous les champs."
                ];
                return $response($data);
            }

            if ($request->input("question_next_id") == null && $request->input("question_next_end") == null) {
                $data["alert"] = [
                    "type" => "warning",
                    "icon" => "attention",
                    "message" => "Merci de préciser la suite (question suivante, question finale) en cas de choix de cette réponse."
                ];
                return $response($data);
            }
            if ($request->input("question_next_id") != null && Question::where("id", "=", $request->input("question_next_id"))->count() <= 0) {
                $data["alert"] = [
                    "type" => "warning",
                    "icon" => "attention",
                    "message" => "La question suivante n'as pas été trouvé."
                ];
                return $response($data);
            }

            $validator = Validator::make(
                $request->all(),
                [
                    "image" => "file|max:3512|image|mimes:jpeg,jpg,bmp,png"
                ]
            );
            if ($request->hasFile("image") != null && $validator->fails()) {
                $data["alert"] = [
                    "type" => "warning",
                    "icon" => "attention",
                    "message" => "L'image est requise et doit être compris dans un format d'images (.jpg, .jpeg, .bmp ou .png) et ne pas dépassé 3 Mo."
                ];
                return $response($data);
            }
            /*if ($request->hasFile("image") != null && !(function() use ($request) {
                $t = getimagesize($request->image->path());
                return ($t[0] == "800" && $t[1] == "800");
            })()) {
                $data["alert"] = [
                    "type" => "warning",
                    "icon" => "attention",
                    "message" => "L'image doit avoir la taille 800px de large sur 800px de haut."
                ];
                return $response($data);
            }*/

            DB::beginTransaction();
            $error = 0;


            $data["reponse"]->question_id = $idQuestion;
            $data["reponse"]->name = $request->input("name");
            $data["reponse"]->description = $request->input("description");
            if ($request->hasFile("image") && $request->file("image")->isValid()) {
                $data["reponse"]->image = 'data: ' . mime_content_type($request->image->path()) . ';base64,' . base64_encode(file_get_contents($request->image->path()));
            }
            $data["reponse"]->question_next_id = (is_null($request->input("question_next_end")) || $request->input("question_next_end") != "on" ? $request->input("question_next_id") : null);
            $data["reponse"]->question_next_end = (is_null($request->input("question_next_end")) || $request->input("question_next_end") != "on" ? false : true);
            $data["reponse"]->price = str_replace(",", ".", $request->input("price"));
            $data["reponse"]->user_id = $request->session()->get('user')->id;

            if (!$data["reponse"]->save())
                $error++;

            if ($error >= 1) {
                DB::rollback();
                $data["alert"] = [
                    "type" => "danger",
                    "icon" => "attention",
                    "message" => "Une erreur est survenue durant la modification de la réponse. Merci de réessayer ultérieurement."
                ];
                return $response($data);
            }

            DB::commit();
            $data["alert"] = [
                "type" => "success",
                "icon" => "check",
                "message" => "Réponse modifié."
            ];
            return $response($data);
        }

        return $response($data);
    }

    public function choice_remove($idQuestion, $idReponse)
    {
        $data = [];
        $data["idQuestion"] = $idQuestion;

        $t = QuestionReponse::where("id", "=", $idReponse)->where('question_id', "=", $idQuestion);

        if ($t->count() <= 0) {
            $data["alert"] = [
                "type" => "warning",
                "icon" => "attention",
                "message" => "Impossible de trouver la question à supprimer."
            ];
            return redirect('/admin/configurator/' . $idQuestion . '/choices')->with('alert', $data["alert"]);
        }

        if (!$t->first()->delete()) {
            $data["alert"] = [
                "type" => "danger",
                "icon" => "attention",
                "message" => "Impossible de supprimer la question."
            ];
            return redirect('/admin/configurator/' . $idQuestion . '/choices')->with('alert', $data["alert"]);
        }

        $data["alert"] = [
            "type" => "success",
            "icon" => "check",
            "message" => "Question supprimé."
        ];
        return redirect('/admin/configurator/' . $idQuestion . '/choices')->with('alert', $data["alert"]);
    }
}
