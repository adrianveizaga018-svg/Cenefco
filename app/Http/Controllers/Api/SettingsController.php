<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Settings\GeneralSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function __construct(
        private GeneralSettings $settings
    ) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'site_name' => $this->settings->site_name,
            'site_active' => $this->settings->site_active,
            'contact_email' => $this->settings->contact_email,
            'items_per_page' => $this->settings->items_per_page,
            'maintenance_mode' => $this->settings->maintenance_mode,
            'site_logo' => $this->settings->site_logo
                ? asset('storage/'.$this->settings->site_logo)
                : null,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'site_name' => 'sometimes|string|max:255',
            'site_active' => 'sometimes|boolean',
            'contact_email' => 'sometimes|email',
            'items_per_page' => 'sometimes|integer|min:1|max:100',
            'maintenance_mode' => 'sometimes|boolean',
            'site_logo' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        if ($request->hasFile('site_logo')) {
            if ($this->settings->site_logo) {
                Storage::disk('public')->delete($this->settings->site_logo);
            }
            $path = $request->file('site_logo')->store('logos', 'public');
            $this->settings->site_logo = $path;
        }

        foreach (['site_name', 'site_active', 'contact_email', 'items_per_page', 'maintenance_mode'] as $key) {
            if (isset($validated[$key])) {
                $this->settings->$key = $validated[$key];
            }
        }

        $this->settings->save();

        return response()->json([
            'message' => 'Configuraciones actualizadas correctamente',
            'settings' => [
                'site_name' => $this->settings->site_name,
                'site_active' => $this->settings->site_active,
                'contact_email' => $this->settings->contact_email,
                'items_per_page' => $this->settings->items_per_page,
                'maintenance_mode' => $this->settings->maintenance_mode,
                'site_logo' => $this->settings->site_logo
                    ? asset('storage/'.$this->settings->site_logo)
                    : null,
            ],
        ]);
    }
}
