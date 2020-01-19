<?php

namespace DieterHolvoet\Meta;

use DieterHolvoet\Meta\Components\Meta;
use DieterHolvoet\Meta\Models\Settings;
use System\Classes\PluginBase;
use System\Classes\SettingsManager;

/**
 * Meta Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'dieterholvoet.meta::plugin.name',
            'description' => 'dieterholvoet.meta::plugin.description',
            'author'      => 'DieterHolvoet',
            'icon'        => 'icon-tags'
        ];
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            Meta::class => 'meta',
        ];
    }

    /**
     * Registers any back-end configuration links used by this plugin.
     *
     * @return array
     */
    public function registerSettings()
    {
        return [
            'location' => [
                'label'       => 'dieterholvoet.meta::menu.settings.label',
                'description' => 'dieterholvoet.meta::menu.settings.description',
                'category'    => SettingsManager::CATEGORY_CMS,
                'icon'        => 'icon-tags',
                'class'       => Settings::class,
                'order'       => 500,
                'keywords'    => 'meta og',
                'permissions' => ['dieterholvoet.meta.manage_settings'],
            ]
        ];
    }

    public function registerPermissions()
    {
        return [
            'dieterholvoet.meta.manage_settings' => [
                'label' => 'dieterholvoet.meta::plugin.permissions.manage_settings',
                'tab' => 'dieterholvoet.meta::plugin.name',
            ],
        ];
    }
}
