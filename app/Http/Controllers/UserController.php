<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() : JsonResponse
    {
        $draw = request()->input('draw');
        $start = request()->input('start');
        $length = request()->input('length');
        $searchValue = request()->input('search.value');
        $orderColumn = request()->input('order.0.column');
        $orderDir = request()->input('order.0.dir');

        $columns = [
            0 => 'name',
            1 => 'email'
            // add more columns here...
        ];


        $query = User::select(['id', 'name', 'email']);

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'LIKE', "%$searchValue%")
                    ->orWhere('email', 'LIKE', "%$searchValue%");
            });
        }

        if ($orderColumn != null) {
            $query->orderBy($columns[$orderColumn], $orderDir);
        }

        $totalCount = $query->count();
        $query->skip($start)->take($length);
        $data = $query->get();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalCount,
            'recordsFiltered' => $totalCount,
            'data' => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) : JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email']
        ]);

        User::factory()->create([
            'name' => $validated['name'],
            'email' => $validated['email']
        ]);

        return response()->json([
            'message' => 'Success! The data has been created successfully.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user) : JsonResponse
    {
        return response()->json([
            'data' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user) : JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required'],
        ]);

        $user->name = $validated['name'];
        $user->save();

        return response()->json([
            'message' => 'Success! The data has been updated successfully.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'message' => 'Success! The data has been deleted successfully.'
        ]);
    }
}
