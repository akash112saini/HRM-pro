<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('group')->orderBy('key')->get()->groupBy('group');
        return view('super-admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'nullable|array',
            'super_admin_logo' => 'nullable|file|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'super_admin_favicon' => 'nullable|file|mimes:png,jpg,jpeg,ico,svg,webp|max:1024',
        ]);

        // Handle logo upload with fixed filename in dedicated folder
        if ($request->hasFile('super_admin_logo')) {
            $file = $request->file('super_admin_logo');
            $extension = $file->getClientOriginalExtension();

            // Delete all old logo files (regardless of extension)
            $oldFiles = \Storage::disk('public')->files('uploads/superadmin/logo');
            foreach ($oldFiles as $oldFile) {
                \Storage::disk('public')->delete($oldFile);
            }

            // Fixed filename in dedicated logo folder
            $filename = 'logo.' . $extension;
            $path = 'uploads/superadmin/logo/' . $filename;

            // Store file with fixed name
            $file->storeAs('uploads/superadmin/logo', $filename, 'public');

            // Update setting with fixed path
            Setting::set('super_admin_logo', $path, 'string', 'branding');

            \Log::info('Logo uploaded', [
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'extension' => $extension
            ]);

            ActivityLog::logActivity(
                'uploaded_logo',
                null,
                [],
                ['path' => $path],
                "Uploaded super admin logo: {$path}"
            );

            session()->flash('logo_uploaded', $path);
        }

        // Handle favicon upload with fixed filename in dedicated folder
        if ($request->hasFile('super_admin_favicon')) {
            $file = $request->file('super_admin_favicon');
            $extension = $file->getClientOriginalExtension();

            // Delete all old favicon files (regardless of extension)
            $oldFiles = \Storage::disk('public')->files('uploads/superadmin/favicon');
            foreach ($oldFiles as $oldFile) {
                \Storage::disk('public')->delete($oldFile);
            }

            // Fixed filename in dedicated favicon folder
            $filename = 'favicon.' . $extension;
            $path = 'uploads/superadmin/favicon/' . $filename;

            // Store file with fixed name
            $file->storeAs('uploads/superadmin/favicon', $filename, 'public');

            // Update setting with fixed path
            Setting::set('super_admin_favicon', $path, 'string', 'branding');

            \Log::info('Favicon uploaded', [
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'extension' => $extension
            ]);

            ActivityLog::logActivity(
                'uploaded_favicon',
                null,
                [],
                ['path' => $path],
                "Uploaded super admin favicon: {$path}"
            );

            session()->flash('favicon_uploaded', $path);
        }

        // Update other settings if provided
        if (isset($validated['settings']) && is_array($validated['settings'])) {
            foreach ($validated['settings'] as $key => $value) {
                // Determine the group for this setting
                $group = 'general'; // default
                if (str_contains($key, 'admin_panel')) {
                    $group = 'branding';
                } elseif (str_contains($key, 'smtp') || str_contains($key, 'mail')) {
                    $group = 'email';
                } elseif (str_contains($key, 'payment') || str_contains($key, 'stripe') || str_contains($key, 'paypal')) {
                    $group = 'payment';
                } elseif (str_contains($key, 'suspend') || str_contains($key, 'grace') || str_contains($key, 'trial')) {
                    $group = 'subscription';
                }

                $setting = Setting::where('key', $key)->first();

                if ($setting) {
                    $oldValue = $setting->value;
                    Setting::set($key, $value, $setting->type, $group);

                    ActivityLog::logActivity(
                        'updated_setting',
                        $setting,
                        ['value' => $oldValue],
                        ['value' => $value],
                        "Updated setting: {$key}"
                    );
                } else {
                    // Create new setting if doesn't exist
                    Setting::set($key, $value, 'string', $group);
                }
            }
        }

        return back()->with('success', 'Settings updated successfully!');
    }
}

