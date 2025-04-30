<?php

use App\Services\AutocompliteService\Controllers\AutocompleteController;

Route::get('api/autocomplete/search', [AutocompleteController::class, 'search']);

