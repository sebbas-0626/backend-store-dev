<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Obtener el ID de la categoría desde la ruta para la validación de unicidad
    public function rules(): array
    {
        $categoryId = $this->route('category');

        return [
            'parent_id' => ['nullable', 'uuid', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:120', "unique:categories,slug,{$categoryId}"],
            'description' => ['nullable', 'string', 'max:255'],
            'image_url' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }
}
