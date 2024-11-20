<?php

namespace App\Http\Controllers;

use App\Http\Filters\MovementFilter;
use App\Models\Movement;
use Illuminate\Http\Request;

class MovementController extends Controller
{
    public function index(Request $request, MovementFilter $filters)
    {
        $perPage = $request->input('per_page', 5);
        $page = $request->input('page', 1);
        $movements = Movement::filter($filters)->paginate($perPage, ['*'], 'page', $page);

        return response()->json($movements);
    }
}
