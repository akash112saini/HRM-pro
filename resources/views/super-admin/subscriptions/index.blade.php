@extends('super-admin.layout')

@section('title', 'Subscriptions Management')

@section('content')
    <div class="page-header mb-4">
        <h1 class="page-title">Subscriptions Management</h1>
        <p class="text-muted">Manage tenant subscriptions</p>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <select name="plan" class="form-select">
                        <option value="">All Plans</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->slug }}" {{ request('plan') == $plan->slug ? 'selected' : '' }}>
                                {{ $plan->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="expiring" class="form-select">
                        <option value="">All Subscriptions</option>
                        <option value="1" {{ request('expiring') ? 'selected' : '' }}>Expiring Soon (30 days)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Subscriptions Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tenant</th>
                            <th>Current Plan</th>
                            <th>Expires</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tenants as $tenant)
                            <tr>
                                <td>
                                    <strong>{{ $tenant->name }}</strong><br>
                                    <small class="text-muted">{{ $tenant->contact_email }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ ucfirst($tenant->subscription_plan) }}</span>
                                </td>
                                <td>
                                    @if($tenant->subscription_expires_at)
                                        {{ $tenant->subscription_expires_at->format('M d, Y') }}<br>
                                        <small
                                            class="{{ $tenant->subscription_expires_at->isPast() ? 'text-danger' : 'text-muted' }}">
                                            {{ $tenant->subscription_expires_at->diffForHumans() }}
                                        </small>
                                    @else
                                        <span class="text-muted">No expiry</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $tenant->is_active ? 'success' : 'danger' }}">
                                        {{ $tenant->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#updatePlanModal{{ $tenant->id }}">
                                            <i class="bi bi-arrow-up-circle"></i> Update Plan
                                        </button>
                                        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal"
                                            data-bs-target="#extendModal{{ $tenant->id }}">
                                            <i class="bi bi-clock-history"></i> Extend
                                        </button>
                                        <button type="button" class="btn btn-outline-info" data-bs-toggle="modal"
                                            data-bs-target="#paymentModal{{ $tenant->id }}">
                                            <i class="bi bi-cash"></i> Payment
                                        </button>
                                    </div>

                                    <!-- Update Plan Modal -->
                                    <div class="modal fade" id="updatePlanModal{{ $tenant->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('super-admin.subscriptions.update-plan', $tenant) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Update Subscription Plan</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>Tenant:</strong> {{ $tenant->name }}</p>
                                                        <p><strong>Current Plan:</strong> <span class="badge bg-primary">{{ ucfirst($tenant->subscription_plan) }}</span></p>
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label">New Plan</label>
                                                            <select name="subscription_plan" class="form-select" required>
                                                                <option value="">Select Plan</option>
                                                                @foreach($plans as $plan)
                                                                    <option value="{{ $plan->slug }}" {{ $tenant->subscription_plan == $plan->slug ? 'selected' : '' }}>
                                                                        {{ $plan->name }} - ${{ $plan->price }}/{{ $plan->billing_cycle }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Expiry Date (Optional)</label>
                                                            <input type="date" name="subscription_expires_at" class="form-control" 
                                                                value="{{ $tenant->subscription_expires_at?->format('Y-m-d') }}">
                                                            <small class="text-muted">Leave empty for no expiration</small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Update Plan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Extend Subscription Modal -->
                                    <div class="modal fade" id="extendModal{{ $tenant->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('super-admin.subscriptions.extend', $tenant) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Extend Subscription</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>Tenant:</strong> {{ $tenant->name }}</p>
                                                        <p><strong>Current Expiry:</strong> {{ $tenant->subscription_expires_at ? $tenant->subscription_expires_at->format('M d, Y') : 'No expiry' }}</p>
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label">Extend By</label>
                                                            <select name="months" class="form-select" required>
                                                                <option value="">Select Duration</option>
                                                                <option value="1">1 Month</option>
                                                                <option value="3">3 Months</option>
                                                                <option value="6">6 Months</option>
                                                                <option value="12">12 Months</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-success">Extend Subscription</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Record Payment Modal -->
                                    <div class="modal fade" id="paymentModal{{ $tenant->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('super-admin.subscriptions.record-payment', $tenant) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Record Payment</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>Tenant:</strong> {{ $tenant->name }}</p>
                                                        <p><strong>Current Plan:</strong> <span class="badge bg-primary">{{ ucfirst($tenant->subscription_plan) }}</span></p>
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label">Amount</label>
                                                            <input type="number" name="amount" class="form-control" step="0.01" required placeholder="0.00">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Transaction Date</label>
                                                            <input type="date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Payment Method</label>
                                                            <select name="payment_method" class="form-select" required>
                                                                <option value="">Select Method</option>
                                                                <option value="bank_transfer">Bank Transfer</option>
                                                                <option value="credit_card">Credit Card</option>
                                                                <option value="paypal">PayPal</option>
                                                                <option value="cash">Cash</option>
                                                                <option value="other">Other</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Notes (Optional)</label>
                                                            <textarea name="notes" class="form-control" rows="2" placeholder="Add any notes about this payment..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-info">Record Payment</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">No subscriptions found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($tenants->hasPages())
            <div class="card-footer">
                {{ $tenants->links() }}
            </div>
        @endif
    </div>
@endsection