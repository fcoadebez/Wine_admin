<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Model\Wine;
use App\Model\WineType;
use App\Model\Question;
use App\Model\Profil;
use App\Model\QuestionResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use \Validator;
use \DB;

class QuestionController extends Controller
{
    // list
    public function list()
    {
        $data = [];
        $data["questions"] = Question::get();

        return view("admin.views.question.list")->with($data);
    }

    public function add(Request $request)
    {
        $data = [];

        $data["profils"] = Profil::get();

        $response = function ($data) {
            return view("admin.views.question.add")->with($data);
        };

        if ($request->isMethod("POST")) {
            $valid = Validator($request->all(),
                [
                    'question' => 'required',
                    'response' => 'required',
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

            $question = new Question();
            $question->question = $request->input("question");

            if (!$question->save())
                $error++;

            if ($error >= 1) {
                DB::rollback();
                $data["alert"] = [
                    "type" => "danger",
                    "icon" => "attention",
                    "message" => "Une erreur est survenue durant l'ajout de la question. Merci de réessayer ultérieurement."
                ];
                return $response($data);
            }

            DB::commit();

            $current_question = Question::where('question', '=', $request->input("question"))->first();

            $responses = $request->input("response");
            $profils = $request->input("profil");

            for($i=0; $i<count($responses); $i++) {
              $response = new QuestionResponse();
              $response->profil_id = $profils[$i];
              $response->question_id = $current_question->id;
              $response->response = $responses[$i];
              $response->save();
            }

            $data["alert"] = [
                "type" => "success",
                "icon" => "check",
                "message" => "Question ajouté avec succès."
            ];

            return redirect("/admin/question/list");
        }

        return $response($data);
    }

    public function edit(Request $request, $id)
    {
        $data = [];
        $data["id"] = $id;
        $data["question"] = Question::where("id", "=", $id)->first();
        $data["profils"] = Profil::get();

        $response = function ($data) {
            return view("admin.views.question.edit")->with($data);
        };

        if ($data["question"] == null) {
            return redirect("/admin/question/list");
        }

        if ($request->isMethod("POST")) {
            $valid = Validator($request->all(),
                [
                    'question' => 'required',
                    'response' => 'required',
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

            $data["question"]->question = $request->input("question");

            if (!$data["question"]->save())
                $error++;

            foreach($data["question"]->rQuestionResponse()->get() as $responseQuestion) {
              $responseQuestion->delete();
            }

            $responses = $request->input("response");
            $profils = $request->input("profil");

            for($i=0; $i<count($responses); $i++) {
              $responseQuestion = new QuestionResponse();
              $responseQuestion->profil_id = $profils[$i];
              $responseQuestion->question_id = $data["question"]->id;
              $responseQuestion->response = $responses[$i];
              $responseQuestion->save();
            }

            if ($error >= 1) {
                DB::rollback();
                $data["alert"] = [
                    "type" => "danger",
                    "icon" => "attention",
                    "message" => "Une erreur est survenue durant la modification de la question. Merci de réessayer ultérieurement."
                ];

                return $response($data);
            }

            DB::commit();
            $data["alert"] = [
                "type" => "success",
                "icon" => "check",
                "message" => "Question modifié."
            ];
            return $response($data);
        }

        return $response($data);
    }

    public function remove($id)
    {
        DB::beginTransaction();
        $t = Question::where('id', "=", $id);
        $qs = Question::where('id', "<>", $id)->get();

        $responses = QuestionResponse::where('question_id', "=", $id)->get();

        foreach($responses as $responseQuestion) {
          $responseQuestion->delete();
        }
        if ($t->count() != 1) {
            return redirect('/admin/question/list')->with('alert', [
                "type" => "danger",
                "icon" => "attention",
                "message" => "Impossible d'identifié la question à supprimer."
            ]);
        }


        $t = $t->first();

        if (!$t->delete()) {
            DB::rollback();
            return redirect('/admin/question/list')->with('alert', [
                "type" => "danger",
                "icon" => "attention",
                "message" => "Une erreur s'est produite durant la suppression de la question. Merci de réessayer ultérieurement."
            ]);
        }

        DB::commit();
        return redirect('/admin/question/list')->with('alert', [
            "type" => "success",
            "icon" => "check",
            "message" => "Question supprimée."
        ]);
    }
}
