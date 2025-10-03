<?php

namespace App\Http\Services;

use App\Models\categories;

class CategoryService
{
  private $ALL_CATEGORIES = [];

  public function __construct()
  {
    $this->ALL_CATEGORIES = categories::pluck('code')->toArray();
  }

  static function listCategories()
  {
    return categories::select('code', 'name')->get();
  }

  static function validateCategoryCode($categoryCode)
  {
    $instance = new self();
    return in_array($categoryCode, $instance->ALL_CATEGORIES);
  }
}
