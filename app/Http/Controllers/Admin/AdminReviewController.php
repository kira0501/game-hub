<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');
        $query = Review::with(['user', 'game'])->latest();

        if (in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $status);
        }

        return view('admin.reviews.index', [
            'reviews' => $query->paginate(20)->withQueryString(),
            'status' => $status,
            'counts' => [
                'pending' => Review::where('status', 'pending')->count(),
                'approved' => Review::where('status', 'approved')->count(),
                'rejected' => Review::where('status', 'rejected')->count(),
            ],
        ]);
    }

    public function update(Request $request, Review $review)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
        ]);

        $review->update($data);
        $review->game->update([
            'user_score_avg' => round($review->game->reviews()->where('status', 'approved')->avg('rating') ?: 0, 1),
        ]);

        return back()->with('status', 'Статус отзыва обновлён.');
    }

    public function destroy(Review $review)
    {
        $game = $review->game;
        $review->delete();
        $game->update([
            'user_score_avg' => round($game->reviews()->where('status', 'approved')->avg('rating') ?: 0, 1),
        ]);

        return back()->with('status', 'Отзыв удален.');
    }
}
