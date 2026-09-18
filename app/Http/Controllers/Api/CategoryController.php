<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all()
            ->unique(function ($item) {
                return strtolower(trim($item->name)) . '_' . $item->type;
            })
            ->values();

        return response()->json($categories);
    }
}
