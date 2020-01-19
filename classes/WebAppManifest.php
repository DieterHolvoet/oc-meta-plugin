<?php

namespace DieterHolvoet\Meta\Classes;

use Cache;
use DieterHolvoet\ImageResizer\Classes\Image;
use DieterHolvoet\Meta\Models\Settings;
use October\Rain\Support\Traits\Singleton;
use System\Behaviors\SettingsModel;

class WebAppManifest
{
    use Singleton;

    const CACHE_KEY = 'dieterholvoet_meta_manifest';

    public function invalidate()
    {
        return Cache::forget(self::CACHE_KEY);
    }

    public function get()
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return $this->build();
        });
    }

    public function build()
    {
        $manifest = [];
        /** @var Settings|SettingsModel $settings */
        $settings = Settings::instance();

        if ($name = $settings->get('application_name')) {
            $manifest['name'] = $manifest['short_name'] = $name;
        }

        $manifest['start_url'] = url('/');

        if ($color = $settings->get('color')) {
            $manifest['theme_color'] = $color;
        }

        if ($faviconPath = $settings->get('favicon')) {
            $faviconPath = implode(DIRECTORY_SEPARATOR, [config('cms.storage.media.folder'), $faviconPath]);
            $faviconImage = new Image($faviconPath);

            foreach ([[192, 192], [512, 512]] as $dimensions) {
                [$width, $height] = $dimensions;

                $manifest['icons'][] = [
                    'src' => $faviconImage->resize($width, $height),
                    'sizes' => sprintf('%dx%d', $width, $height),
                ];
            }
        }

        if ($name = $settings->get('url')) {
            $manifest['name'] = $manifest['short_name'] = $name;
        }

        return $manifest;
    }
}
