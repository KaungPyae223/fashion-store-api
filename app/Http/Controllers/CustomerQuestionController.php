<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerQuestionRequest;
use App\Http\Requests\UpdateCustomerQuestionRequest;
use App\Models\CustomerQuestion;
use App\Repositories\CustomerQuestionRepository;
use Carbon\Carbon;

class CustomerQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     protected $customerQuestionRepository;

     function __construct(CustomerQuestionRepository $customerQuestionRepository)
     {
        $this->customerQuestionRepository = $customerQuestionRepository;
     }

    public function getAllQuestions()
    {

        $query = CustomerQuestion::query()->whereNull("answer")->orderBy('created_at', 'desc')->paginate(10);

        $questions = $query->map(function($question){
            return [
                "customer_name" => $question->customer->user->name,
                "customer_email" => $question->customer->user->email,
                "question" => $question->question,
                "question_at" => Carbon::parse($question->created_at)->diffForHumans(),

            ];
        });


        return response()->json([
            "data" => $questions,
            'meta' => [
                'current_page' => $query->currentPage(),
                'last_page' => $query->lastPage(),
                'total' => $query->total(),
            ],
            "status" => 200,
        ]);

    }

    public function getAllAnswers()
    {
        $query = CustomerQuestion::query()->whereNotNull('answer')->orderBy('updated_at', 'desc')->paginate(8);


        $questions = $query->map(function($question){
            return [
                "customer_name" => $question->customer->user->name,
                "admin_name" => $question->admin->user->name,
                "customer_email" => $question->customer->user->email,
                "question" => $question->question,
                "answer" => $question->answer,
                "question_at" => $question->created_at,
                "answer_at" => $question->updated_at
            ];
        });

        return response()->json([
            "data" => $questions,
            'meta' => [
                'current_page' => $query->currentPage(),
                'last_page' => $query->lastPage(),
                'total' => $query->total(),
            ],
            "status" => 200,
        ]);

    }

    public function getAllCustomerQuestions($id)
    {
        $questions = CustomerQuestion::whereNull("answer")
            ->where("customer_id", $id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($question) {
                return [
                    "id" => $question->id,
                    "question" => $question->question,
                    "question_at" => Carbon::parse($question->created_at)->diffForHumans(),
                ];
            });

        return response()->json([
            "data" => $questions,
            "status" => 200,
        ]);
    }

    public function getAllCustomerAnswers($id)
    {


        $questions = CustomerQuestion::query()
            ->whereNotNull('answer')
            ->where("customer_id",$id)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function($question){
                return [
                    "question" => $question->question,
                    "answer" => $question->answer,
                    "question_at" => $question->created_at,
                    "answer_at" => $question->updated_at
                ];
        });

        return response()->json([
            "data" => $questions,
            "status" => 200,
        ]);

    }


    public function askQuestion(StoreCustomerQuestionRequest $request)
    {
        $question = $this->customerQuestionRepository->askQuestion([
            "customer_id" => $request->customer_id,
            "question" => $request->question
        ]);

        return response()->json([
            "data" => [
                "question" => $question->question,
                "ask_at" => $question->created_at,
            ],
            "status" => 200,
        ]);

    }

    public function answerQuestion(UpdateCustomerQuestionRequest $request, CustomerQuestion $customerQuestion)
    {
        $question = $this->customerQuestionRepository->answerQuestion([
            "answer" => $request->answer,
            "admin_id" => $request->admin_id,
            "id" => $request->id
        ]);

        return response()->json([
            "data" => [
                "question" => $question->question,
                "answer" => $question->answer,
                "question_at" => $question->created_at,
                "answer_at" => $question->updated_at
            ],
            "status" => 200,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerQuestion $customerQuestion)
    {
        $customerQuestion->delete();

        return response()->json([
            "message" => "successfully delete",
            "status" => 200
        ]);

    }
}
