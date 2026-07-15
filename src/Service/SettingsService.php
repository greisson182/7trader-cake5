<?php

declare(strict_types=1);

namespace App\Service;

use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;

class SettingsService
{
    private ?object $_config = null;

    public function get(): object
    {
        if ($this->_config === null) {
            $this->_config = Cache::remember('settings_config', function () {
                $settingsTable = TableRegistry::getTableLocator()->get('Settings');
                return $settingsTable->find('all')->first() ?? (object)[
                    'maintenance' => 0,
                    'title' => '',
                    'description' => '',
                    'email' => '',
                    'aws_api' => '',
                    'accesses' => 0,
                    'redirect_website' => false,
                    'url_site' => '',
                    'mp_access_token' => '',
                    'mp_public_key' => '',
                    'mp_webhook_secret' => '',
                    'mp_sandbox' => true,
                ];
            }, 'default');
        }

        return $this->_config;
    }

    public function clearCache(): void
    {
        Cache::delete('settings_config');
        $this->_config = null;
    }
}
