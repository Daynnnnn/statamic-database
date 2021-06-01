## Statamic Database Driver

An eloquent driver for Statamic V3 which supports:

 - Asset Containers
 - Blueprints
 - Collections
 - Entries
 - Fieldsets
 - Global Sets
 - Navigation
 - Taxonomies/Terms
 - Trees

## Installation

From a standard Statamic V3 site, you can run:
`composer require daynnnnn/statamic-database`

Then copy the database migrations into your app using:
`cp -r ./vendor/daynnnnn/statamic-database/database/migrations/* ./database/migrations/`

Run migrations:
`php please migrate`

Then in the register function of your AppServiceProvider, add:
```
public function register()
{
    $this->app->singleton(
        'Statamic\Fields\BlueprintRepository',
        'Daynnnnn\StatamicDatabase\Blueprints\BlueprintRepository'
    );

    $this->app->singleton(
        'Statamic\Fields\FieldsetRepository',
        'Daynnnnn\StatamicDatabase\Fieldsets\FieldsetRepository'
    );
}
```
And that should be it!

## Issues/Things to work on

 - No tests.
 - After creating an asset container, you're shown a 404 page as you're redirected to the container using the model ID instead of the handle.
 - Still needs asset meta and forms adding.
 - No real world testing done yet, so probably some more to be added.

## Credits

Thanks to [@statamic](https://statamic.dev/)  for creating the entries part of this in [statamic/eloquent-driver](https://github.com/statamic/eloquent-driver), which a lot of this was based off.
