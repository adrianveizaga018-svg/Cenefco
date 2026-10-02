<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $site_name;

    public bool $site_active;

    public string $contact_email;

    public int $items_per_page;

    public bool $maintenance_mode;

    public string $site_logo;

    public static function group(): string
    {
        return 'general';
    }
}
