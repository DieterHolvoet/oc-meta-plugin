<?php

namespace DieterHolvoet\Meta\Components;

use Cms\Classes\ComponentBase;
use DieterHolvoet\ImageResizer\Classes\Image;
use DieterHolvoet\Meta\Models\Settings;
use RainLab\Translate\Behaviors\TranslatableModel;
use System\Behaviors\SettingsModel;
use System\Classes\MediaLibrary;
use System\Classes\PluginManager;

class Meta extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'dieterholvoet.meta::plugin.name',
            'description' => 'dieterholvoet.meta::plugin.description',
        ];
    }

    public function defineProperties()
    {
        return [
            'url' => [
                'title' => 'Website URL',
                'description' => 'The main url of this website.',
                'type' => 'string',
            ],
            'title' => [
                'title' => 'Title',
                'description' => 'Page title',
                'type' => 'string',
            ],
            'description' => [
                'title' => 'Description',
                'description' => 'Page description',
                'type' => 'string',
            ],
            'application_name' => [
                'title' => 'Application name',
                'description' => 'Page description',
                'type' => 'string',
            ],
            'og_image' => [
                'title' => 'og:image',
                'description' => 'Image',
                'type' => 'string',
            ],
            'og_image_width' => [
                'title' => 'og:image',
                'description' => 'Width',
                'type' => 'number',
            ],
            'og_image_height' => [
                'title' => 'og:image',
                'description' => 'Height',
                'type' => 'number',
            ],
            'og_type' => [
                'title' => 'og:type',
                'description' => 'Website type',
                'type' => 'string',
            ],
            'og_url' => [
                'title' => 'og:url',
                'description' => 'Website url',
                'type' => 'string',
            ],
            'color' => [
                'title' => 'Color',
                'description' => 'Website color',
                'type' => 'string',
            ],
            'twitter_summary_image' => [
                'title' => 'Twitter Summary Image',
                'description' => 'Website color',
                'type' => 'string',
            ],
            'twitter_handle' => [
                'title' => 'Twitter Handle',
                'description' => 'Website color',
                'type' => 'string',
            ],
        ];
    }

    public function onRun()
    {
        foreach ($this->getDefaults() as $name => $value) {
            if ($this->property($name) === null) {
                $this->setProperty($name, $value);
            }
        }

        foreach (['og_image', 'twitter_summary_image'] as $propertyName) {
            if ($this->property($propertyName) === null) {
                continue;
            }

            if (PluginManager::instance()->exists('dieterholvoet.imageresizer')) {
                $settings = \DieterHolvoet\ImageResizer\Models\Settings::instance();
                $parameters = $settings->getParameters()
                    ->setWidth(1200)
                    ->setHeight(630);

                try {
                    $image = Image::fromPath($this->property($propertyName));
                    $url = $settings->getProcessor()->getUrl($image, $parameters);
                } catch (\Exception $e) {
                    return null;
                }

                $this->setProperty($propertyName, $url);
            }

            $image = $this->property($propertyName);

            if ($propertyName === 'og_image' && file_exists(base_path($image)) && $imageInfo = getimagesize(base_path($image))) {
                list($width, $height) = $imageInfo;
                $this->setProperty('og_image_width', $width);
                $this->setProperty('og_image_height', $height);
                $this->setProperty('og_image_type', $imageInfo['mime']);
            }
        }
    }

    protected function getDefaults()
    {
        $imageUrl = null;
        $faviconUrl = null;
        $appleTouchIconUrl = null;
        $safariPinnedTabIconUrl = null;

        /** @var Settings|SettingsModel|TranslatableModel $settings */
        $settings = Settings::instance();

        if ($image = $settings->get('image')) {
            $imageUrl = MediaLibrary::url($image);
        }

        if ($favicon = $settings->get('favicon')) {
            $faviconUrl = MediaLibrary::url($favicon);
        }

        if ($appleTouchIcon = $settings->get('apple_touch_icon')) {
            $appleTouchIconUrl = MediaLibrary::url($appleTouchIcon);
        }

        if ($safariPinnedTabIcon = $settings->get('safari_pinned_tab')) {
            $safariPinnedTabIconUrl = MediaLibrary::url($safariPinnedTabIcon);
        }

        return [
            'url' => url('/'),
            'application_name' => $settings->get('application_name'),
            'title' => $this->page->title,
            'description' => $settings->get('description'),
            'google_site_verification' => $settings->get('google_site_verification'),
            'bing_site_verification' => $settings->get('bing_site_verification'),
            'color' => $settings->get('color'),
            'favicon' => $faviconUrl,
            'apple_touch_icon' => $appleTouchIconUrl,
            'safari_pinned_tab' => $safariPinnedTabIconUrl,

            /*
            Image for Facebook
            - at least 1200 x 630 pixels
            - can be up to 8MB in size
            */
            'og_type' => 'website',
            'og_image' => $imageUrl,
            'og_image_width' => 1200,
            'og_image_height' => 630,

            'twitter_summary_image' => $imageUrl,
        ];
    }
}
