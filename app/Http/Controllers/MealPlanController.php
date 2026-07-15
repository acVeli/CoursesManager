<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\MealPlanEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;

class MealPlanController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $rawWeek = $request->query('week_start', now()->toDateString());
        $weekStart = Carbon::parse($rawWeek)->startOfWeek()->toDateString();

        $dishes = Dish::where('user_id', $user->id)->with('ingredients')->get();
        $regularDishes = $dishes->where('baby', false)->values();
        $babyDishes = $user->has_baby
            ? $dishes->where('baby', true)->values()
            : collect();

        $entries = MealPlanEntry::where('user_id', $user->id)
            ->where('week_start', $weekStart)
            ->with(['dish.ingredients', 'babyDish.ingredients'])
            ->get()
            ->keyBy(fn (MealPlanEntry $entry) => "{$entry->day_of_week}_{$entry->meal}");

        $shopping = collect();

        foreach ($entries as $entry) {
            $plannedDishes = [$entry->dish];
            if ($user->has_baby) {
                $plannedDishes[] = $entry->babyDish;
            }

            foreach ($plannedDishes as $plannedDish) {
                if (! $plannedDish) {
                    continue;
                }

                foreach ($plannedDish->ingredients as $ingredient) {
                    $key = strtolower(trim($ingredient->name)).'|'.strtolower(trim($ingredient->unit));
                    $shopping->put($key, $shopping->get($key, [
                        'name' => $ingredient->name,
                        'quantity' => 0,
                        'unit' => $ingredient->unit,
                    ]));
                    $row = $shopping->get($key);
                    $row['quantity'] = $row['quantity'] + floatval($ingredient->quantity);
                    $shopping->put($key, $row);
                }
            }
        }

        return view('meal-plan.index', [
            'dishes' => $regularDishes,
            'babyDishes' => $babyDishes,
            'entries' => $entries,
            'weekStart' => $weekStart,
            'shopping' => $shopping->values(),
            'hasBaby' => $user->has_baby,
            'babyFeedingGuide' => $user->babyFeedingRecommendations(),
        ]);
    }

    public function save(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'week_start' => 'required|date',
            'slots' => 'required|array',
            'slots.*.day' => 'required|string',
            'slots.*.meal' => 'required|string',
            'slots.*.dish_id' => 'nullable|exists:dishes,id',
            'slots.*.baby_dish_id' => 'nullable|exists:dishes,id',
        ]);

        // Normalize incoming week_start to the week's Monday so all dates in the same
        // calendar week map to the same stored `week_start` value.
        $normalizedWeekStart = Carbon::parse($request->input('week_start'))->startOfWeek()->toDateString();

        foreach ($request->input('slots') as $slot) {
            $attributes = [];

            if (array_key_exists('dish_id', $slot)) {
                $attributes['dish_id'] = $slot['dish_id'] ?: null;
            }

            if ($user->has_baby && array_key_exists('baby_dish_id', $slot)) {
                $attributes['baby_dish_id'] = $slot['baby_dish_id'] ?: null;
            }

            MealPlanEntry::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'week_start' => $normalizedWeekStart,
                    'day_of_week' => $slot['day'],
                    'meal' => $slot['meal'],
                ],
                $attributes
            );
        }

        return Redirect::route('dashboard', ['week_start' => $normalizedWeekStart]);
    }
}
