<?php

namespace App\Http\Controllers;

use App\Http\Requests\Categories\CategoryCreateRequest;
use App\Http\Requests\Categories\CategoryUpdateRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $categories = Category::all();
        return Inertia::render('Categories/List', [
            'categories' => $categories
        ]);
    }

    public function get(Category $category): Category
    {
        return $category;
    }

    public function list(): Collection
    {
        return Category::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryCreateRequest $request): Category
    {
        $data = $request->all();
        /** @var Category $category */
        $category = Category::query()->create($data);
        return $category;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category): Response
    {
        return Inertia::render('Categories/Edit', [
            'category' => $category
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryUpdateRequest $request, Category $category): Category
    {
        $data = $request->all();
        $category->update($data);
        return $category;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): void
    {
        $category->delete();
    }
}
