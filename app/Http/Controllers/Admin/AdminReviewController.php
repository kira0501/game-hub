<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index()
    {
        return view('admin.reviews.index', [
            'reviews' => Review::with(['user', 'game'])->latest()->paginate(20),
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

        return back()->with('status', 'Статус отзыва обновлен.');
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
