<?php

namespace App\Http\Controllers;

use App\Models\BookRequest;
use App\Models\Review;
use App\Models\User;
use App\Notifications\NewReviewNotification;
use App\Notifications\ReviewApprovedNotification;
use App\Notifications\ReviewRefusedNotification;
use Database\Factories\BookFactory;
use Illuminate\Http\Request;


class ReviewController extends Controller
{

    public function index(Request $request)
    {
        $query = Review::with(['user', 'bookRequest.book'])
            ->where('status', 'suspended')
            ->when($request->search, function ($q) use ($request) {
                $search = $request->search;

                $q->where(function ($q) use ($search) {
                    $q->whereHas('bookRequest.book', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })->orWhereHas('bookRequest.user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
                });
            });

        $reviews = $query->latest()->paginate(10);

        return view('reviews.index', compact('reviews'));
    }

    public function create(BookRequest $bookRequest)
    {
        if (auth()->id() !== $bookRequest->user_id) {
            abort(403, 'Ação não autorizada.');
        }

        return view('reviews.create', compact('bookRequest'));
    }

    public function show(Review $review)
    {
        $review->load(['user', 'bookRequest.book']);
        return view('reviews.show', compact('review'));
    }


    public function store(Request $request, BookRequest $bookRequest)
    {
        $request->validate([
            'rating' => 'required|numeric|min:0.5|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review = Review::create([
            'book_request_id' => $bookRequest->id,
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
            'status' => 'suspended',

        ]);
        //mail
        try {
            User::where('role', 'admin')->each(function ($admin) use ($review) {
                $admin->notify(new NewReviewNotification($review));
            });

        } catch (\Exception $e) {
            \Log::error('Erro ao enviar email: ' . $e->getMessage());
        }

        return redirect()->route('dashboard')->with('success', 'A sua avaliação foi enviada com sucesso!');
    }

    public function update(Request $request, Review $review)
    {
        $request->validate([
            'status' => 'required|in:active,refused',
            'reason' => 'nullable|string|max:500',
        ]);

        $review->status = $request->status;

        if ($review->status === 'refused') {
            $review->reason = $request->reason;
        } else {
            $review->reason = null;
        }

        $review->save();

        //NOTIFICATION
        if ($review->status === 'active') {
            $review->user->notify(new ReviewApprovedNotification($review));
        } elseif ($review->status === 'refused') {
            $review->user->notify(new ReviewRefusedNotification($review, $request->reason));
        }

        return redirect()->route('reviews.index')
            ->with('success', 'Avaliação atualizada com sucesso.');
    }
}
