<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewRequest;
use App\Models\Game;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function store(ReviewRequest $request, Game $game): RedirectResponse
    {
        Review::updateOrCreate(
            ['user_id' => $request->user()->id, 'game_id' => $game->id],
            $request->validated() + ['status' => 'pending']
        );

        $game->update([
            'user_score_avg' => round($game->reviews()->where('status', 'approved')->avg('rating') ?: 0, 1),
        ]);

        return back()->with('status', 'Отзыв отправлен на модерацию. После проверки администратором он появится на странице.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $this->authorize('delete', $review);
        $review->delete();

        return back()->with('status', 'Отзыв удален.');
    }
}
