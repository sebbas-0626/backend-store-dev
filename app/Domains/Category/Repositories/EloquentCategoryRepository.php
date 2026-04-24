<?php

namespace App\Domains\Category\Repositories;

use App\Domains\Category\Contracts\Repositories\CategoryRepositoryInterface;
use App\Domains\Category\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return Category::query()
            ->with('parent')
            ->orderBy('sort_order')
            ->paginate($perPage);
    }

    public function getAll(array $filters = []): Collection
    {
        return Category::query()->orderBy('sort_order')->get();
    }

    public function roots(): Collection
    {
        return Category::root()->with('children')->get();
    }

    public function active(): Collection
    {
        return Category::active()->get();
    }

    public function tree(): Collection
    {
        return Category::root()->with('children')->get();
    }

    public function findById(string $id): ?Category
    {
        return Category::find($id);
    }

    public function findOrFail(string $id): Category
    {
        return Category::findOrFail($id);
    }

    public function findBySlug(string $slug): ?Category
    {
        return Category::where('slug', $slug)->first();
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);

        return $category->fresh();
    }

    public function delete(Category $category): bool
    {
        return $category->delete();
    }
}
