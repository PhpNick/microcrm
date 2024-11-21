<?php

namespace App\Http\Controllers;

use App\Http\Filters\MovementFilter;
use App\Models\Movement;
use Illuminate\Http\Request;

/**
 * Class MovementController
 *
 * Управление историей движения товаров.
 *
 * @package App\Http\Controllers
 */
class MovementController extends Controller
{
    /**
     * Вывод списка движения товаров.
     *
     * @param Request $request
     * @param MovementFilter $filters
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, MovementFilter $filters)
    {
        $perPage = $request->input('per_page', 5);
        $page = $request->input('page', 1);
        $movements = Movement::filter($filters)->paginate($perPage, ['*'], 'page', $page);

        return response()->json($movements);
    }
}
