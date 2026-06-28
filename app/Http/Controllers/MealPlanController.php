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

        $entries = MealPlanEntry::where('user_id', $user->id)
            ->where('week_start', $weekStart)
            ->with('dish.ingredients')
            ->get()
            ->keyBy(fn (MealPlanEntry $entry) => "{$entry->day_of_week}_{$entry->meal}");

        $shopping = collect();

        foreach ($entries as $entry) {
            if ($entry->dish) {
                foreach ($entry->dish->ingredients as $ingredient) {
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
            'dishes' => $dishes,
            'entries' => $entries,
            'weekStart' => $weekStart,
            'shopping' => $shopping->values(),
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
        ]);

        // Normalize incoming week_start to the week's Monday so all dates in the same
        // calendar week map to the same stored `week_start` value.
        $normalizedWeekStart = Carbon::parse($request->input('week_start'))->startOfWeek()->toDateString();

        foreach ($request->input('slots') as $slot) {
            MealPlanEntry::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'week_start' => $normalizedWeekStart,
                    'day_of_week' => $slot['day'],
                    'meal' => $slot['meal'],
                ],
                [
                    'dish_id' => $slot['dish_id'],
                ]
            );
        }

        return Redirect::route('dashboard', ['week_start' => $normalizedWeekStart]);
    }
}
