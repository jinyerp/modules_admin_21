# Admin:Make Command Documentation

## Overview
The `admin:make` command is a powerful artisan command that generates a complete CRUD (Create, Read, Update, Delete) admin panel feature with controllers, views, routes, and configuration files.

## Command Syntax

```bash
php artisan admin:make {module} {feature} {table} [--model={model}]
```

### Parameters

- **module** (required): The module name where the admin feature will be created (e.g., `Admin2`, `Shop`, `Site`)
- **feature** (required): The feature name for the admin panel (e.g., `Product`, `Category`, `User`)
- **table** (required): The database table name (e.g., `products`, `categories`, `users`)
- **--model** (optional): The model class name. If not provided, defaults to the feature name

### Examples

```bash
# Basic usage
php artisan admin:make Admin2 Product products

# With custom model name
php artisan admin:make Shop Category shop_categories --model=ShopCategory

# Another example
php artisan admin:make Site Article articles --model=Article
```

## Generated Files Structure

When you run the command, it creates the following file structure:

```
jiny/{module}/
├── App/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Admin/
│   │           └── Admin{Feature}/
│   │               ├── Admin{Feature}.php           # Main index controller
│   │               ├── Admin{Feature}Create.php     # Create controller
│   │               ├── Admin{Feature}Edit.php       # Edit controller
│   │               ├── Admin{Feature}Show.php       # Show controller
│   │               ├── Admin{Feature}Delete.php     # Delete controller
│   │               └── Admin{Feature}.json          # Configuration file
│   └── Models/
│       └── {Model}.php                              # Eloquent model
├── resources/
│   └── views/
│       └── admin/
│           └── {feature}/
│               ├── table.blade.php                  # Table view
│               ├── create.blade.php                 # Create form
│               ├── edit.blade.php                   # Edit form
│               ├── show.blade.php                   # Detail view
│               └── search.blade.php                 # Search form
└── routes/
    └── admin_{feature}.php                          # Route definitions
```

## Controller Features

### Admin{Feature}.php (Index Controller)
- Handles listing and pagination
- Manages search and filtering
- Provides hooks for customization:
  - `hookIndexing()`: Before data query
  - `hookIndexed()`: After data retrieval
  - `hookTableHeader()`: Customize table headers
  - `hookPagination()`: Configure pagination
  - `hookSorting()`: Set sorting options
  - `hookSearch()`: Configure search
  - `hookFilters()`: Set up filters

### Admin{Feature}Create.php
- Displays creation form
- Handles form submission
- Provides hooks:
  - `hookCreating()`: Before showing create form
  - `hookStoring()`: Before saving to database
  - `hookStored()`: After successful save

### Admin{Feature}Edit.php
- Displays edit form with existing data
- Updates records
- Provides hooks:
  - `hookEditing()`: Before showing edit form
  - `hookUpdating()`: Before updating database
  - `hookUpdated()`: After successful update

### Admin{Feature}Show.php
- Displays detailed view of a single record
- Formats data for display
- Provides hooks:
  - `hookShowing()`: Before displaying data
  - `hookShowed()`: After data preparation

### Admin{Feature}Delete.php
- Handles record deletion
- Supports soft deletes (configurable)
- Transaction support for safe deletion

## JSON Configuration File

The `Admin{Feature}.json` file is the central configuration for your admin feature:

### Structure

```json
{
    "title": "Feature Management",
    "subtitle": "Manage features in the system",
    "route": {
        "name": "admin.feature",
        "prefix": "admin.feature"
    },
    "table": {
        "name": "table_name",
        "model": "\\Namespace\\Model",
        "timestamps": true,
        "softDeletes": false
    },
    "template": {
        "layout": "jiny-admin2::layouts.admin",
        "index": "jiny-admin2::template.index",
        "create": "jiny-admin2::template.create",
        "edit": "jiny-admin2::template.edit",
        "show": "jiny-admin2::template.show"
    },
    "index": {
        "paging": 20,
        "searchable": ["title", "description"],
        "sortable": ["id", "title", "created_at"],
        "features": {
            "enableCreate": true,
            "enableDelete": true,
            "enableEdit": true,
            "enableSearch": true,
            "enableSettingsDrawer": true
        }
    },
    "create": {
        "enableContinueCreate": true,
        "defaults": {
            "enable": true,
            "pos": 0
        }
    },
    "validation": {
        "rules": {
            "title": "required|string|max:255",
            "description": "nullable|string"
        }
    }
}
```

### Key Configuration Options

#### Table Configuration
- `name`: Database table name
- `model`: Full namespace path to the Eloquent model
- `timestamps`: Enable/disable automatic timestamps
- `softDeletes`: Enable/disable soft deletes

#### Index/List Features
- `paging`: Number of items per page
- `searchable`: Fields that can be searched
- `sortable`: Fields that can be sorted
- `features`: Toggle various UI features

#### Form Configuration
- `defaults`: Default values for new records
- `validation`: Laravel validation rules
- `fillable`: Fields that can be mass-assigned

## Route Registration

Routes are automatically generated and follow this pattern:

```php
Route::prefix('admin/{feature}')->name('admin.{feature}.')->group(function () {
    Route::get('/', Admin{Feature}::class)->name('index');
    Route::get('/create', Admin{Feature}Create::class)->name('create');
    Route::post('/store', Admin{Feature}Store::class)->name('store');
    Route::get('/{id}', Admin{Feature}Show::class)->name('show');
    Route::get('/{id}/edit', Admin{Feature}Edit::class)->name('edit');
    Route::put('/{id}', Admin{Feature}Update::class)->name('update');
    Route::delete('/{id}', Admin{Feature}Delete::class)->name('destroy');
});
```

## Views and Templates

### Base Templates
The command uses predefined templates from `jiny-admin2`:
- `jiny-admin2::template.index` - List view template
- `jiny-admin2::template.create` - Create form template
- `jiny-admin2::template.edit` - Edit form template
- `jiny-admin2::template.show` - Detail view template

### Custom Views
Generated blade files in `resources/views/admin/{feature}/`:
- `table.blade.php`: Custom table layout
- `create.blade.php`: Create form fields
- `edit.blade.php`: Edit form fields
- `show.blade.php`: Detail display layout
- `search.blade.php`: Search form inputs

## Settings Drawer

The Settings Drawer is a slide-out panel that allows real-time configuration of:
- Table display options
- Form layouts
- Feature toggles
- Display formats

It's automatically included when `enableSettingsDrawer` is set to `true` in the JSON configuration.

## Customization Hooks

All controllers provide hooks for customization without modifying the core code:

```php
// Example: Customize data before saving
public function hookStoring($wire, $form)
{
    $form['slug'] = Str::slug($form['title']);
    $form['user_id'] = auth()->id();
    return $form;
}

// Example: Format data for display
public function hookShowing($wire, $data)
{
    $data['created_at_formatted'] = Carbon::parse($data['created_at'])->format('F j, Y');
    return $data;
}
```

## Best Practices

1. **Use Hooks**: Instead of modifying generated controllers, use hooks for customization
2. **JSON Configuration**: Keep all settings in the JSON file for easy management
3. **Validation Rules**: Define comprehensive validation rules in the JSON configuration
4. **Naming Convention**: Follow the Admin{Feature} naming pattern for consistency
5. **Database Migration**: Create your database migration before running the command

## Troubleshooting

### Common Issues

1. **Blank Page Error**
   - Check that all required variables are passed to views
   - Verify JSON configuration paths are correct
   - Ensure Livewire components have single root elements

2. **Route Not Found**
   - Register the generated route file in your service provider
   - Clear route cache: `php artisan route:clear`

3. **Missing Model**
   - Ensure the model exists or use `--model` parameter
   - Check namespace in JSON configuration

4. **Permission Issues**
   - Ensure write permissions for the module directory
   - Check file ownership after generation

## Advanced Features

### Multi-level Hierarchical Data
The generated controllers support hierarchical data with `pos`, `depth`, and `ref` fields for tree structures.

### Bulk Operations
Enable bulk actions in the JSON configuration to allow multiple record operations.

### Export/Import
Configure export and import features in the JSON for data portability.

### Custom Livewire Components
Replace default Livewire components by updating paths in the JSON configuration.

## Conclusion

The `admin:make` command streamlines the creation of admin panels by generating all necessary files with a single command. The generated code follows Laravel best practices and provides extensive customization options through hooks and JSON configuration.