# Backend Store Dev

API RESTful para gestión de tienda online construida con Laravel y Filament.

## Requisitos

- PHP 8.2+
- Composer 2.x
- Node.js 18+
- MySQL 8.0+ / PostgreSQL 15+
- Laravel 11.x

## Instalación

```bash
# Clonar repositorio
git clone <repo-url>
cd backend-store-dev

# Instalar dependencias PHP
composer install

# Instalar dependencias Node
npm install

# Configurar entorno
cp .env.example .env
php artisan key:generate

# Ejecutar migraciones
php artisan migrate

# Iniciar servidor de desarrollo
php artisan serve
```

## Estructura del Proyecto

```
app/
├── Domains/                    # Arquitectura basada en dominio
│   └── Category/
│       ├── Models/            # Entidades del dominio
│       ├── Services/           # Lógica de negocio
│       ├── Repositories/       # Acceso a datos
│       └── Contracts/          # Interfaces
├── Http/
│   ├── Controllers/Api/       # Controladores API
│   ├── Requests/              # Form Requests
│   └── Resources/             # API Resources (Transformers)
├── Filament/                   # Panel de administración
│   └── Resources/             # Recursos Filament
└── Models/                    # Modelos Eloquent
```

## API Endpoints

Base URL: `/api/v1/admin/category`

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/categories` | Listar categorías (paginado) |
| POST | `/categories` | Crear categoría |
| GET | `/categories/{id}` | Ver categoría |
| PUT | `/categories/{id}` | Actualizar categoría |
| DELETE | `/categories/{id}` | Eliminar categoría |

### Ejemplo de request

```json
POST /api/v1/admin/category/categories
{
    "name": "Electrónica",
    "slug": "electronica",
    "description": "Productos electrónicos",
    "parent_id": null,
    "is_active": true,
    "sort_order": 0
}
```

### Estructura de respuesta

```json
{
    "data": {
        "id": "uuid",
        "name": "Electrónica",
        "slug": "electronica",
        "description": "Productos electrónicos",
        "parent_id": null,
        "is_active": true,
        "sort_order": 0,
        "path": "Electrónica",
        "children": []
    }
}
```

## Modelo Category

### Campos

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | UUID | Identificador único |
| parent_id | UUID? | Categoría padre (null si es raíz) |
| name | string | Nombre de la categoría |
| slug | string | Slug URL amigable |
| description | string? | Descripción |
| image_url | string? | URL de imagen |
| is_active | boolean | Visible en storefront |
| sort_order | integer | Orden de visualización |
| created_at | timestamp | Fecha creación |
| updated_at | timestamp | Fecha actualización |
| deleted_at | timestamp? | Soft delete |

### Relaciones

- `parent`: BelongsTo → Categoría padre
- `children`: HasMany → Subcategorías
- `products`: HasMany → Productos asociados

## Panel de Administración

Accede a Filament en `/admin` después de crear un usuario:

```bash
php artisan make:filament-user
```

## Comandos útiles

```bash
# Regenerar slugs únicos
php artisan categories:regenerate-slugs

# Reordenar categorías
php artisan categories:reorder {ordered_ids}

# Importar categorías desde CSV
php artisan import:categories path/to/file.csv
```

## Licencia

MIT