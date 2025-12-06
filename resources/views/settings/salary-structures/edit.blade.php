@extends('layouts.app')

@section('title', 'Edit Salary Structure')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">Edit Salary Structure</h1>
            <a href="{{ roleRoute('salary-structures.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to List
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ roleRoute('salary-structures.update', $salaryStructure->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Structure Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                            value="{{ old('name', $salaryStructure->name) }}" required placeholder="e.g. Regular Staff Structure">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Salary Components</label>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i> Define the components that make up this salary structure.
                        </div>

                        <div id="components-container">
                            @php
                                $components = old('components', $salaryStructure->components ?? []);
                                if (empty($components)) {
                                    $components = [['name' => '', 'type' => 'fixed', 'value' => '']];
                                }
                            @endphp

                            @foreach($components as $index => $component)
                                <div class="row mb-2 component-row">
                                    <div class="col-md-4">
                                        <input type="text" name="components[{{ $index }}][name]" class="form-control"
                                            value="{{ $component['name'] ?? '' }}"
                                            placeholder="Component Name (e.g. Basic)" required>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="components[{{ $index }}][type]" class="form-select" required>
                                            <option value="fixed" {{ ($component['type'] ?? '') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                            <option value="percentage" {{ ($component['type'] ?? '') == 'percentage' ? 'selected' : '' }}>Percentage of Basic</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" step="0.01" name="components[{{ $index }}][value]" class="form-control"
                                            value="{{ $component['value'] ?? '' }}"
                                            placeholder="Value" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-danger remove-component" {{ count($components) == 1 ? 'disabled' : '' }}>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="btn btn-outline-primary mt-2" id="add-component">
                            <i class="bi bi-plus-circle me-2"></i>Add Component
                        </button>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                {{ old('is_active', $salaryStructure->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Update Structure
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let componentIndex = {{ count($components) }};

                document.getElementById('add-component').addEventListener('click', function () {
                    const container = document.getElementById('components-container');
                    const newRow = document.createElement('div');
                    newRow.className = 'row mb-2 component-row';
                    newRow.innerHTML = `
                            <div class="col-md-4">
                                <input type="text" name="components[${componentIndex}][name]" class="form-control" placeholder="Component Name" required>
                            </div>
                            <div class="col-md-3">
                                <select name="components[${componentIndex}][type]" class="form-select" required>
                                    <option value="fixed">Fixed Amount</option>
                                    <option value="percentage">Percentage of Basic</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="number" step="0.01" name="components[${componentIndex}][value]" class="form-control" placeholder="Value" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-outline-danger remove-component">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        `;
                    container.appendChild(newRow);
                    componentIndex++;
                    updateRemoveButtons();
                });

                document.getElementById('components-container').addEventListener('click', function (e) {
                    if (e.target.closest('.remove-component')) {
                        const row = e.target.closest('.component-row');
                        if (document.querySelectorAll('.component-row').length > 1) {
                            row.remove();
                            updateRemoveButtons();
                        }
                    }
                });

                function updateRemoveButtons() {
                    const buttons = document.querySelectorAll('.remove-component');
                    const disabled = buttons.length === 1;
                    buttons.forEach(btn => btn.disabled = disabled);
                }
            });
        </script>
    @endpush
@endsection
