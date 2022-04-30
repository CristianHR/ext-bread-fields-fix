<?php

namespace Cristianhr\ExtendedBreadFormFieldsFix;

use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\ServiceProvider;
use Cristianhr\ExtBreadFieldsFix\FormFields\MultipleImagesWithAttrsFormField;
use Cristianhr\ExtBreadFieldsFix\FormFields\KeyValueJsonFormField;

class ExtendedBreadFormFieldsFixServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'extended-fields');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Voyager::addFormField(KeyValueJsonFormField::class);
        Voyager::addFormField(MultipleImagesWithAttrsFormField::class);

        $this->app->bind(
            'TCG\Voyager\Http\Controllers\VoyagerBaseController',
            'Cristianhr\ExtBreadFieldsFix\Controllers\ExtendedBreadFormFieldsFixController'
        );

        $this->app->bind(
            'TCG\Voyager\Http\Controllers\VoyagerMediaController',
            'Cristianhr\ExtBreadFieldsFix\Controllers\ExtendedBreadFormFieldsFixMediaController'
        );
    }
}
