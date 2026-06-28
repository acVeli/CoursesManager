<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class DishController extends Controller
{
    public function index(Request $request)
    {
        $dishes = Dish::where('user_id', $request->user()->id)->with('ingredients')->get();

        return view('dishes.index', [
            'dishes' => $dishes,
        ]);
    }

    public function create()
    {
        return view('dishes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.name' => 'required|string|max:255',
            'ingredients.*.quantity' => 'required|numeric',
            'ingredients.*.unit' => 'nullable|string|max:100',
        ]);

        $dish = Dish::create([
            'user_id' => $request->user()->id,
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);

        foreach ($request->input('ingredients') as $ingredient) {
            $dish->ingredients()->create($ingredient);
        }

        return Redirect::route('dishes.index');
    }

    public function edit(Request $request, Dish $dish)
    {
        abort_unless($dish->user_id === $request->user()->id, 403);

        return view('dishes.edit', [
            'dish' => $dish->load('ingredients'),
        ]);
    }

    public function update(Request $request, Dish $dish)
    {
        abort_unless($dish->user_id === $request->user()->id, 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.name' => 'required|string|max:255',
            'ingredients.*.quantity' => 'required|numeric',
            'ingredients.*.unit' => 'nullable|string|max:100',
        ]);

        $dish->update($request->only(['name', 'description']));
        $dish->ingredients()->delete();

        foreach ($request->input('ingredients') as $ingredient) {
            $dish->ingredients()->create($ingredient);
        }

        return Redirect::route('dishes.index');
    }

    public function destroy(Request $request, Dish $dish)
    {
        abort_unless($dish->user_id === $request->user()->id, 403);
        $dish->delete();

        return Redirect::route('dishes.index');
    }
}
