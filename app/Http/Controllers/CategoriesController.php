<?php

namespace App\Http\Controllers;

use App\Http\Services\CategoryService;

class CategoriesController
{
    public function listCategories()
    {
        try {
            $categories = CategoryService::listCategories();

            return response()->json(['data' => $categories]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }
}
