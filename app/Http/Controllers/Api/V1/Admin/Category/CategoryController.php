<?php

namespace App\Http\Controllers\Api\V1\Admin\Category;

use App\Domains\Category\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $service
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $categories = $this->service->paginate(50);
        return CategoryResource::collection($categories);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->service->create($request->validated());
        return response()->json(new CategoryResource($category), 201);
    }

    public function show(string $id): CategoryResource
    {
        $category = $this->service->findOrFail($id);
        return new CategoryResource($category);
    }

    public function update(UpdateCategoryRequest $request, string $id): CategoryResource
    {
        $category = $this->service->update($id, $request->validated());
        return new CategoryResource($category);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->service->delete($id);
        return response()->json(['message' => 'Categoría eliminada correctamente'], 200);
    }
}
