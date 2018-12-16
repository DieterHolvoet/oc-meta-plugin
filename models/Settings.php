<?php

namespace DieterHolvoet\Meta\Models;

use Backend\Models\BrandSetting;
use DieterHolvoet\Meta\Classes\WebAppManifest;
use Model;
use System\Behaviors\SettingsModel;

class Settings extends Model
{
    public $implement = [
        'System.Behaviors.SettingsModel',
        'RainLab.Translate.Behaviors.TranslatableModel',
    ];

    public $settingsCode = 'dieterholvoet_meta_settings';
    public $settingsFields = 'fields.yaml';

    public $translatable = ['description'];

    /**
     * Initialize the seed data for this model. This only executes when the
     * model is first created or reset to default.
     * @return void
     */
    public function initSettingsData()
    {
        /** @var BrandSetting|SettingsModel $brandSettings */
        $brandSettings = BrandSetting::instance();

        $this->application_name = config('app.name');
        $this->description = $brandSettings->get('app_tagline');
        $this->color = $brandSettings->get('primary_color');
    }

    public function afterSave()
    {
        WebAppManifest::instance()->invalidate();
    }
}
