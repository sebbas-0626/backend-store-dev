<?php

namespace App\Domains\Category\Contracts\Repositories;

use App\Domains\Category\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryRepositoryInterface
{
    public function paginate(int $perPage = 5, array $filters = []): LengthAwarePaginator;

    public function active(): Collection;

    public function roots(): Collection;

    public function tree(): Collection;

    public function findById(string $id): ?Category;

    public function findOrFail(string $id): Category;

    public function findBySlug(string $slug): ?Category;

    public function create(array $data): Category;

    public function update(Category $category, array $data): Category;

    public function delete(Category $category): bool;
}
