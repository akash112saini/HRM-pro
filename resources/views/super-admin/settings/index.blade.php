@extends('super-admin.layout')

@section('title', 'System Settings')

@section('content')
    <div class="page-header mb-4">
        <h1 class="page-title">System Settings</h1>
        <p class="text-muted">Configure global system settings</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('logo_uploaded'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <strong>Logo Uploaded:</strong> {{ session('logo_uploaded') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('favicon_uploaded'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <strong>Favicon Uploaded:</strong> {{ session('favicon_uploaded') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">

        <div class="card-body">
            <form action="{{ route('super-admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Nav tabs -->
                <ul class="nav nav-tabs mb-4" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#branding" role="tab">
                            <i class="bi bi-palette"></i> Branding
                        </a>
                    </li>
                    @php
                        $groups = $settings->keys();
                    @endphp
                    @foreach($groups as $index => $group)
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#{{ $group }}" role="tab">
                                <i
                                    class="bi bi-{{ $group === 'general' ? 'gear' : ($group === 'email' ? 'envelope' : ($group === 'payment' ? 'credit-card' : 'box')) }}"></i>
                                {{ ucfirst($group) }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <!-- Branding Tab -->
                    <div class="tab-pane active" id="branding" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Super Admin Logo</label>
                                @php $logo = \App\Models\Setting::get('super_admin_logo'); @endphp
                                <div class="mb-3">
                                    @if($logo)
                                        <div class="border rounded p-3 bg-light text-center">
                                            <img src="{{ asset('storage/' . $logo) }}" alt="Current Logo"
                                                style="max-height: 60px; max-width: 100%;">
                                            <div class="mt-2">
                                                <small class="text-muted">Current Logo</small>
                                            </div>
                                        </div>
                                    @else
                                        <div class="border rounded p-3 bg-light text-center"
                                            style="min-height: 100px; display: flex; align-items: center; justify-content: center;">
                                            <div>
                                                <i class="bi bi-image" style="font-size: 2rem; color: #9ca3af;"></i>
                                                <div class="mt-2">
                                                    <small class="text-muted">No Logo Uploaded</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <input type="file" name="super_admin_logo" class="form-control" accept="image/*">
                                <small class="text-muted">Recommended: 200x60px, PNG or SVG</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Favicon</label>
                                @php $favicon = \App\Models\Setting::get('super_admin_favicon'); @endphp
                                <div class="mb-3">
                                    @if($favicon)
                                        <div class="border rounded p-3 bg-light text-center">
                                            <img src="{{ asset('storage/' . $favicon) }}" alt="Current Favicon"
                                                style="max-height: 32px; max-width: 100%;">
                                            <div class="mt-2">
                                                <small class="text-muted">Current Favicon</small>
                                            </div>
                                        </div>
                                    @else
                                        <div class="border rounded p-3 bg-light text-center"
                                            style="min-height: 100px; display: flex; align-items: center; justify-content: center;">
                                            <div>
                                                <i class="bi bi-star" style="font-size: 2rem; color: #9ca3af;"></i>
                                                <div class="mt-2">
                                                    <small class="text-muted">No Favicon Uploaded</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <input type="file" name="super_admin_favicon" class="form-control" accept="image/*">
                                <small class="text-muted">Recommended: 32x32px, ICO or PNG</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Primary Color</label>
                                <input type="color" name="settings[admin_panel_primary_color]"
                                    class="form-control form-control-color"
                                    value="{{ \App\Models\Setting::get('admin_panel_primary_color', '#6366f1') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Secondary Color</label>
                                <input type="color" name="settings[admin_panel_secondary_color]"
                                    class="form-control form-control-color"
                                    value="{{ \App\Models\Setting::get('admin_panel_secondary_color', '#8b5cf6') }}">
                            </div>
                        </div>
                    </div>

                    @foreach($settings as $group => $groupSettings)
                        <div class="tab-pane" id="{{ $group }}" role="tabpanel">
                            <div class="row g-3">
                                @foreach($groupSettings as $setting)
                                    <div class="col-md-6">
                                        <label class="form-label">
                                            {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                            @if($setting->description)
                                                <small class="text-muted d-block">{{ $setting->description }}</small>
                                            @endif
                                        </label>

                                        @if($setting->type === 'boolean')
                                            <select name="settings[{{ $setting->key }}]" class="form-select">
                                                <option value="1" {{ $setting->value == '1' ? 'selected' : '' }}>Enabled</option>
                                                <option value="0" {{ $setting->value == '0' ? 'selected' : '' }}>Disabled</option>
                                            </select>
                                        @elseif($setting->type === 'integer')
                                            <input type="number" name="settings[{{ $setting->key }}]" class="form-control"
                                                value="{{ $setting->value }}">
                                        @else
                                            <input type="text" name="settings[{{ $setting->key }}]" class="form-control"
                                                value="{{ $setting->value }}">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection