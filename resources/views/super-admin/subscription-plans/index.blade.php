@extends('super-admin.layout')

@section('title', 'Subscription Plans')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Subscription Plans</h1>
            <p class="text-muted">Manage subscription plan catalog</p>
        </div>
        <a href="{{ route('super-admin.subscription-plans.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Add New Plan
        </a>
    </div>

    <div class="row g-4">
        @forelse($plans as $plan)
            <div class="col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-header text-center bg-primary text-white">
                        <h4>{{ $plan->name }}</h4>
                        <h2 class="mb-0">${{ number_format($plan->price, 2) }}</h2>
                        <small>{{ $plan->billing_period }}</small>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            @if($plan->features)
                                @foreach($plan->features as $feature)
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>{{ $feature }}</li>
                                @endforeach
                            @endif
                        </ul>
                        <div class="mt-3">
                            <small class="text-muted">
                                Max Employees: {{ $plan->max_employees ?? 'Unlimited' }}<br>
                                Max Users: {{ $plan->max_users }}
                            </small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex gap-2">
                            <a href="{{ route('super-admin.subscription-plans.edit', $plan) }}"
                                class="btn btn-sm btn-outline-primary flex-fill">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form action="{{ route('super-admin.subscription-plans.destroy', $plan) }}" method="POST"
                                class="flex-fill">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger w-100"
                                    onclick="return confirm('Are you sure?')">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                        <p class="text-muted">No subscription plans found</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
@endsection