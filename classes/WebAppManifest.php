<?php

namespace DieterHolvoet\Meta\Classes;

use Cache;
use DieterHolvoet\Meta\Models\Settings;
use October\Rain\Support\Traits\Singleton;
use System\Behaviors\SettingsModel;
use ToughDeveloper\ImageResizer\Classes\Image;

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

        if ($favicon = $settings->get('favicon')) {
            $favicon = new Image(config('cms.storage.media.path') . $favicon);

            foreach ([[192, 192], [512, 512]] as $dimensions) {
                list($width, $height) = $dimensions;
                /** @var Image $image */
                $image = (clone $favicon)->resize($width, $height);

                $manifest['icons'][] = [
                    'src' => $image->getCachedImagePath(true),
                    'type' => mime_content_type($image->getCachedImagePath(false)),
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
