<?php

namespace App\Domains\Product\Contracts\Repositories;

use App\Domains\Product\DTOs\ProductDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function findById(int $id): ?ProductDTO;

    public function findBySku(string $sku): ?ProductDTO;

    public function getAll(array $filters = []): Collection;

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    public function create(ProductDTO $dto): ProductDTO;

    public function update(int $id, ProductDTO $dto): ?ProductDTO;

    public function delete(int $id): bool;

    public function exists(int $id): bool;

    public function count(array $filters = []): int;
}
