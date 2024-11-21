<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;

/**
 * Class WarehouseController
 *
 * Управление складами
 *
 * @package App\Http\Controllers
 */
class WarehouseController extends Controller
{
    /**
     * Вывод списка складов
     *
     * @return \Illuminate\Database\Eloquent\Collection|Warehouse[]
     */
    public function index()
    {
        return Warehouse::all();
    }
}
