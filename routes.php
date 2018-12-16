<?php

use DieterHolvoet\Meta\Classes\WebAppManifest;

Route::get('/manifest.json', function () {
    return WebAppManifest::instance()->get();
});
