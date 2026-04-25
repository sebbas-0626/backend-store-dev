<?php

namespace App\Domains\Category\Services;

use App\Domains\Category\Contracts\Repositories\CategoryRepositoryInterface;
use App\Domains\Category\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryService
{
    public function __construct(
        protected CategoryRepositoryInterface $repository
    ) {}

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    // roots para mostrar solo categorías principales, active para mostrar solo categorías activas
    public function roots(): Collection
    {
        return $this->repository->roots();
    }

    public function active(): Collection
    {
        return $this->repository->active();
    }

    public function findById(string $id): ?Category
    {
        return $this->repository->findById($id);
    }

    public function findOrFail(string $id): Category
    {
        return $this->repository->findOrFail($id);
    }

    public function findBySlug(string $slug): ?Category
    {
        return $this->repository->findBySlug($slug);
    }

    public function create(array $data): Category
    {
        $data = $this->prepareData($data);

        return DB::transaction(function () use ($data) {
            return $this->repository->create($data);
        });
    }

    //update con validación para evitar ciclos en la jerarquía de categorías
    public function update(string $id, array $data): Category
    {
        $category = $this->findOrFail($id);

        if (! empty($data['parent_id']) && $this->isDescendantOf($id, $data['parent_id'])) {
            throw new \InvalidArgumentException('Una categoría no puede ser hijo de sus descendientes.');
        }

        $data = $this->prepareData($data);

        return DB::transaction(function () use ($category, $data) {
            return $this->repository->update($category->id, $data);
        });
    }

    public function delete(string $id): bool
    {
        $category = $this->findOrFail($id);

        if ($category->children()->exists()) {
            throw new \RuntimeException('No se puede eliminar una categoría que tiene subcategorías.');
        }

        if ($category->products()->exists()) {
            throw new \RuntimeException('No se puede eliminar una categoría que tiene productos.');
        }

        return DB::transaction(function () use ($category) {
            return $this->repository->delete($category->id);
        });
    }

    public function tree(): Collection
    {
        return $this->repository->tree();
    }

    public function flattenTree(Collection $categories, int $depth = 0): Collection
    {
        $result = new Collection;

        foreach ($categories as $category) {
            $category->depth = $depth;
            $result->push($category);

            if ($category->children->isNotEmpty()) {
                $result = $result->merge(
                    $this->flattenTree($category->children, $depth + 1)
                );
            }
        }

        return $result;
    }

    public function moveToParent(string $id, ?string $parentId): Category
    {
        $category = $this->findOrFail($id);

        if ($parentId && $this->isDescendantOf($id, $parentId)) {
            throw new \InvalidArgumentException('No puede mover una categoría a sus descendientes.');
        }

        if ($parentId && $category->id === $parentId) {
            throw new \InvalidArgumentException('Una categoría no puede ser padre de sí misma.');
        }

        $category->update(['parent_id' => $parentId]);

        return $category->fresh();
    }

    public function reorder(array $orderedIds): void
    {
        DB::transaction(function () use ($orderedIds) {
            foreach ($orderedIds as $index => $id) {
                $this->repository->update($id, ['sort_order' => $index]);
            }
        });
    }
// Preparar datos antes de crear o actualizar, incluyendo generación de slug único y valores por defecto para sort_order e is_active
    protected function prepareData(array $data): array
    {
        if (empty($data['slug']) && ! empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $data['slug'] = $this->generateUniqueSlug($data['slug'] ?? '', $data['id'] ?? null);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $data['is_active'] ?? true;

        return $data;
    }

    protected function generateUniqueSlug(string $slug, ?string $excludeId = null): string
    {
        $originalSlug = $slug;
        $counter = 0;

        while (true) {
            $query = Category::where('slug', $slug);

            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            if (! $query->exists()) {
                break;
            }

            $counter++;
            $slug = "{$originalSlug}-{$counter}";
        }

        return $slug;
    }

    protected function isDescendantOf(string $categoryId, string $potentialAncestorId): bool
    {
        $category = $this->findById($categoryId);

        if (! $category) {
            return false;
        }

        return $category->isDescendantOf(
            $this->findOrFail($potentialAncestorId)
        );
    }
}
