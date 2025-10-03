<?php

namespace App\Http\Controllers;

use App\Http\Services\CategoryService;

class CategoriesController
{
    private $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function listCategories()
    {
        try {
            $categories = $this->categoryService->listCategories();

            return response()->json(['data' => $categories]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }
}
