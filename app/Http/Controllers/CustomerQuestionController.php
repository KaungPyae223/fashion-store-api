<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerQuestionRequest;
use App\Http\Requests\UpdateCustomerQuestionRequest;
use App\Mail\AnswerMail;
use App\Models\CustomerQuestion;
use App\Models\User;
use App\Repositories\CustomerQuestionRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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

        $totalCustomerSupport = User::where("role","Customer Support")->count();

        $customerSupports = User::where('role', 'Customer Support')->get();

        $currentUser = Auth::user();

        $adminRank = $customerSupports->search(function ($admin) use ($currentUser) {
            return $admin->id === $currentUser->id;
        }) + 1;


        $query = CustomerQuestion::query()
        ->whereNull("answer")
        ->where("id", ">=", $adminRank) // Ensure id is at least 2
        ->whereRaw("(id - $adminRank) % $totalCustomerSupport = 0")
        ->orderBy('created_at', 'desc')
        ->paginate(10);


        $questions = $query->map(function($question){
            return [
                "id" => $question->id,
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
        ]);

    }

    public function getAllAnswers(Request $request)
    {
        $customer = $request->input('customer');
        $admin = $request->input('admin');

        // Start the query
        $query = CustomerQuestion::query();

        // Filter by customer name
        if ($customer) {
            $query->whereHas('customer.user', function ($q) use ($customer) {
                $q->where('name', 'like', '%' . $customer . '%');
            });
        }

        // Filter by admin name
        if ($admin) {
            $query->whereHas('admin.user', function ($q) use ($admin) {
                $q->where('name', 'like', '%' . $admin . '%');
            });
        }

        // Filter answered questions and paginate
        $paginatedAnswers = $query->whereNotNull('answer')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        // Transform data
        $answers = $paginatedAnswers->map(function ($answer) {
            return [
                "customer_name" => $answer->customer->user->name ?? null,
                "admin_name" => $answer->admin->user->name ?? null,
                "customer_email" => $answer->customer->user->email ?? null,
                "question" => $answer->question,
                "answer" => $answer->answer,
                "question_at" => $answer->created_at,
                "answer_at" => $answer->updated_at,
            ];
        });

        // Return response with pagination meta
        return response()->json([
            "data" => $answers,
            "meta" => [
                'current_page' => $paginatedAnswers->currentPage(),
                'last_page' => $paginatedAnswers->lastPage(),
                'total' => $paginatedAnswers->total(),
            ],
        ]);
    }



    public function answerQuestion(UpdateCustomerQuestionRequest $request)
    {
        $question = $this->customerQuestionRepository->answerQuestion([
            "answer" => $request->answer,
            "id" => $request->id
        ]);

        Mail::to($question->customer->user->email)->send(new AnswerMail());


        return response()->json([
            "data" => [
                "id" => $question->id,
                "question" => $question->question,
                "answer" => $question->answer,
                "question_at" => $question->created_at,
                "answer_at" => $question->updated_at
            ],
           'message' => 'Question answered successfully',
        ],201);
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
