@extends('layouts.app')

@section('title', 'Edit Tax Slab')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">Edit Tax Slab</h1>
            <a href="{{ roleRoute('tax-slabs.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ roleRoute('tax-slabs.update', $taxSlab->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="financial_year" class="form-label">Financial Year <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('financial_year') is-invalid @enderror"
                                id="financial_year" name="financial_year"
                                value="{{ old('financial_year', $taxSlab->financial_year) }}" placeholder="e.g. 2024-2025"
                                required>
                            @error('financial_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="tax_rate" class="form-label">Tax Rate (%) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('tax_rate') is-invalid @enderror"
                                id="tax_rate" name="tax_rate" value="{{ old('tax_rate', $taxSlab->tax_rate) }}" required>
                            @error('tax_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="min_amount" class="form-label">Min Income <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('min_amount') is-invalid @enderror"
                                id="min_amount" name="min_amount" value="{{ old('min_amount', $taxSlab->min_amount) }}"
                                required>
                            @error('min_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="max_amount" class="form-label">Max Income</label>
                            <input type="number" step="0.01" class="form-control @error('max_amount') is-invalid @enderror"
                                id="max_amount" name="max_amount" value="{{ old('max_amount', $taxSlab->max_amount) }}">
                            <div class="form-text">Leave empty for infinite upper limit.</div>
                            @error('max_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Update Tax Slab
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection