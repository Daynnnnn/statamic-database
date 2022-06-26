## Statamic Database Driver

An eloquent driver for Statamic V3 which supports:

 - Asset Containers
 - Blueprints
 - Collections
 - Entries
 - Fieldsets
 - Forms / Form Submissions
 - Global Sets
 - Navigation
 - Taxonomies/Terms
 - Trees

## Installation

From a standard Statamic V3 site, you can run:
`composer require daynnnnn/statamic-database`

Add the config files:
`php artisan vendor:publish --tag="statamic-database-config"`

Run migrations:
`php please migrate`

Then in the register function of your AppServiceProvider, add:
```
public function register()
{
    if (config('statamic.database.blueprints')) {
        $this->app->singleton(
            'Statamic\Fields\BlueprintRepository',
            'Daynnnnn\StatamicDatabase\Blueprints\BlueprintRepository'
        );
    }

    if (config('statamic.database.fieldsets')) {
        $this->app->singleton(
            'Statamic\Fields\FieldsetRepository',
            'Daynnnnn\StatamicDatabase\Fieldsets\FieldsetRepository'
        );
    }
}
```
And that should be it!

## Customising what data is stored in the Database
If you want to customise what data is stored in the database, you can override the default behaviour by altering the config file.

For example if you wanted to store some structure data as yaml so that can be easily inserted into version control you could do:

```php
return [
    'assets_containers' => true,
    'asset_metas' => true,
    'blueprints' => false, // Default true
    'collections' => false, // Default true
    'collection_trees' => true,
    'entries' => true,
    'fieldsets' => false, // Default true
    'forms' => false, // Default true
    'form_submissions' => true,
    'globals' => true,
    'navigation' => true,
    'navigation_trees' => true,
    'taxonomies' => true,
    'terms' => true,
];
```

This will make sure that blueprints, collections, fieldset and forms are stored in yaml files, but still keep submissions and entries in the database.

## Issues/Things to work on

 - No tests.
 - Still needs user roles/groups adding.
 - No real world testing done yet, so probably some more to be added.

## Credits

Thanks to [@statamic](https://statamic.dev/)  for creating the entries part of this in [statamic/eloquent-driver](https://github.com/statamic/eloquent-driver), which a lot of this was based off.
