@extends('layouts.app')

@section('title', 'Tax Slabs')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">Tax Slabs</h1>
            <a href="{{ roleRoute('tax-slabs.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Add Tax Slab
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Financial Year</th>
                                <th>Min Amount</th>
                                <th>Max Amount</th>
                                <th>Tax Rate (%)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($taxSlabs as $slab)
                                <tr>
                                    <td>{{ $slab->financial_year }}</td>
                                    <td>{{ number_format($slab->min_amount, 2) }}</td>
                                    <td>{{ $slab->max_amount ? number_format($slab->max_amount, 2) : 'Above' }}</td>
                                    <td>{{ $slab->tax_rate }}%</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ roleRoute('tax-slabs.edit', $slab->id) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ roleRoute('tax-slabs.destroy', $slab->id) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <i class="bi bi-inbox fs-1 text-muted"></i>
                                        <p class="text-muted mt-2">No tax slabs found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection