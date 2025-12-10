@extends('layouts.app')

@section('title', 'Members - Junaid Builders')

@section('content')
<h3 class="mb-4">Members / Investors</h3>
<section class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3 d-flex align-items-center justify-content-between"><span><i class="fas fa-user-plus text-primary me-2"></i> Add Member</span> <button class="btn btn-sm btn-outline-primary" id="fromListBtn" onclick="toggleClassMulti(['create-member-form', 'add-from-list-form','fromListBtn','addNewBtn'], 'd-none')"><i class="fas fa-users me-2"></i> Add From List</button> <button class="btn btn-sm btn-outline-primary d-none" id="addNewBtn" onclick="toggleClassMulti(['create-member-form', 'add-from-list-form','fromListBtn','addNewBtn'], 'd-none')"><i class="fas fa-user-plus me-2"></i> Add New</button></h5>
            <form id="create-member-form" novalidate
                class="row g-3 needs-validation"
                method="POST"
                action="{{ route('members.store', session('active_project_slug')) }}">
                @csrf

                <div class="col-md-4">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{old('name')}}" placeholder="Full name" required>
                    @error('name')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Contact</label>
                    <input type="text" name="phone" class="form-control" placeholder="03XX-XXXXXXX" value="{{old('phone')}}" required>
                    @error('phone')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">CNIC (optional)</label>
                    <input type="text" name="cnic" class="form-control" value="{{old('cnic')}}" placeholder="42101-XXXXXXX-X">
                    @error('cnic')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Investment Amount</label>
                    <div class="input-group">
                        <span class="input-group-text">₨</span>
                        <input type="number" step="0.01" name="investment_amount" id="investment-amount" class="form-control" value="{{old('investment_amount')}}" placeholder="e.g., 500000" required>
                    </div>
                    <small id="formatted-amount" class="text-muted"></small>
                    @error('investment_amount')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Profit Share (%)</label>
                    <input type="text" name="profit_share" id="profit-share" class="form-control" readonly>
                    <small class="text-muted">Calculated automatically</small>
                    @error('profit_share')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Save Member
                    </button>
                </div>
            </form>
            <form id="add-from-list-form" class="d-none" method="POST" action="{{ route('members.addFromList', session('active_project_slug')) }}">
                @csrf
                <div class="row g-3 align-items-start">
                    <div class="col-md-6">
                        <label class="form-label">Select Member</label>
                        <select name="member_id" class="form-select" required>
                            <option value="" selected disabled>Select a member</option>
                            @foreach($allMembers as $member)
                            <option value="{{ $member->id }}">{{ $member->name }} - {{ $member->phone }}</option>
                            @endforeach
                        </select>
                        @error('member_id')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                    </div>
                    <div class="col-md-3">
                    <label class="form-label">Investment Amount</label>
                    <div class="input-group">
                        <span class="input-group-text">₨</span>
                        <input type="number" step="0.01" name="investment_amount" id="ext-mem-investment-amount" class="form-control" value="{{old('investment_amount')}}" placeholder="e.g., 500000" required>
                    </div>
                    <small id="ext-formatted-amount" class="text-muted"></small>
                    @error('investment_amount')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Profit Share (%)</label>
                    <input type="text" name="profit_share" id="ext-mem-profit-share" class="form-control" readonly>
                    <small class="text-muted">Calculated automatically</small>
                    @error('profit_share')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Save Member
                    </button>
                </div>
                </div>
            </form>
        </div>
    </div>
</section>

<section>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0"><i class="fas fa-list text-secondary me-2"></i> Project Members</h5>
                <small class="text-muted">Auto-calculated share % based on total investment</small>
            </div>
            <div class="table-responsive">
                <table class="table sortable-table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Sr.No</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>CNIC</th>
                            <th class="text-end">Investment</th>
                            <th class="text-end">Share %</th>
                            <th class="text-end">Share Amount</th>
                            <th class="text-center no-sort">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $member)
                        <tr>
                            <td>{{ $members->firstItem() + $loop->index }}</td>
                            <td>{{ $member->name ?? 'N/A' }}</td>
                            <td>{{ $member->phone ?? 'N/A' }}</td>
                            <td>{{ $member->cnic ?? 'N/A' }}</td>
                            <td class="text-end">
                                ₨ {{ number_format($member->pivot->investment_amount ?? 0, 2) }}
                            </td>
                            <td class="text-end">{{ number_format($member->pivot->profit_share, 2) }}%</td>
                            @php
$investedAmount = $member->pivot->investment_amount ?? 0;
$totalInvestment = $project->total_investment ? ($project->total_investment - $project->total_expenses + $project->total_sales - $project->totaldiscounts) : 0;

// Calculate share amount directly from investment ratio (more accurate)
$shareAmount = $totalInvestment > 0 
    ? ($investedAmount / $project->total_investment) * $totalInvestment 
    : 0;

$difference = $shareAmount - $investedAmount;
$percentageChange = $investedAmount > 0 ? (($difference / $investedAmount) * 100) : 0;

// For display, still show the percentage
$share = number_format($member->pivot->profit_share, 2);

// Determine status with tolerance
$tolerance = 0.01;
$isProfit = $difference > $tolerance;
$isLoss = $difference < -$tolerance;
$isNeutral = abs($difference) <= $tolerance;
@endphp
                                <td class="text-end">
                                <div class="d-flex flex-column align-items-end gap-1">
                                    <!-- Main Amount -->
                                    <span class="fw-bold {{ $isProfit ? 'text-success' : ($isLoss ? 'text-danger' : 'text-dark') }}">
                                        ₨ {{ number_format($shareAmount, 2) }}
                                    </span>

                                    <!-- Profit/Loss/Neutral Indicator -->
                                    @if($isNeutral)
                                    <small class="badge bg-secondary-subtle text-secondary border border-secondary">
                                        <i class="bi bi-dash-circle"></i>
                                        ₨0.00 (0.0%)
                                    </small>
                                    @elseif($isProfit)
                                    <small class="badge bg-success-subtle text-success border border-success">
                                        <i class="bi bi-arrow-up"></i>
                                        +₨{{ number_format(abs($difference), 2) }}
                                        <span class="opacity-75">(+{{ number_format($percentageChange, 2) }}%)</span>
                                    </small>
                                    @else
                                    <small class="badge bg-danger-subtle text-danger border border-danger">
                                        <i class="bi bi-arrow-down"></i>
                                        -₨{{ number_format(abs($difference), 2) }}
                                        <span class="opacity-75">({{ number_format($percentageChange, 2) }}%)</span>
                                    </small>
                                    @endif
                                </div>
                                </td>

                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editMemberModal-{{$member->id}}"><i class="fas fa-edit"></i></button>
                                    @if($member->pivot->role !== 'manager')
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteModal('deleteModal','{{ route('members.destroy', ['member' => $member,'project' => session('active_project_slug')]) }}','Are you sure to remove this member from the project?')"><i class="fas fa-trash"></i></button>
                                    @endif
                                    <a class="btn btn-sm btn-primary" href="{{ route('reports.member', [session('active_project_slug'),$member]) }}">
                                    <i class="fas fa-file-alt"></i>
                                </a>
                                </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="100%" class="text-muted text-center">No Records Found</td>
                        </tr>
                        @endforelse

                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="100%">
                                {{ $members->links('pagination::bootstrap-5') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
@push('modals')
@foreach($members as $member)
<!-- Edit Member Modal -->
<div class="modal fade" id="editMemberModal-{{$member->id}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" class="needs-validation" novalidate action="{{route('members.update', ['member' => $member,'project' => session('active_project_slug')])}}">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title" id="editMemberModalLabel">
                        <i class="fas fa-user-edit me-2"></i>Edit Member
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="edit_name" value="{{$member->name ?? old('name')}}" class="form-control" required>
                            @error('edit_name')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Contact <span class="text-danger">*</span></label>
                            <input type="text" name="edit_phone" class="form-control" value="{{$member->phone ?? old('phone')}}" placeholder="03XXXXXXXXX" required>
                            @error('edit_phone')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">CNIC</label>
                            <input type="text" name="edit_cnic" value="{{$member->cnic ?? old('cnic')}}" class="form-control" placeholder="42101-XXXXXXX-X">
                            @error('edit_cnic')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Investment Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₨</span>
                                <input type="number" step="0.01" value="{{ $member->pivot->investment_amount ?? 0 }}" name="edit_investment_amount" id="edit_investment_amount_{{$loop->index}}" class="form-control" required>
                            </div>
                            <small class="text-muted" id="edit_investment_amount_formatted_{{$loop->index}}"></small>
                            @error('edit_investment_amount')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Profit Share (%)</label>
                            <input type="text" value="{{ $member->pivot->profit_share }}%" id="edit_profit_share_{{$loop->index}}" name="profit_share" class="form-control" readonly>
                            <small class="text-muted">Calculated automatically</small>
                        </div>

                        <div class="col-12">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Note:</strong> Updating this member will recalculate all members' profit shares based on new investment amounts.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Member
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Total project investment (target)
        const totalInvestment = {{$project -> total_investment ?? 0}};
        const totalMembers = {{$members -> count()}};

        // Sum of existing members' investments
        const existingInvested = {{$members -> sum('pivot.investment_amount') ?? 0}};

        const investmentInput = document.getElementById('investment-amount');
        const extInvestmentInput = document.getElementById('ext-mem-investment-amount');
        const profitShareInput = document.getElementById('profit-share');
        const extProfitShareInput = document.getElementById('ext-mem-profit-share');
        const formattedDisplay = document.getElementById('formatted-amount');
        const extFormattedDisplay = document.getElementById('ext-formatted-amount');
        // Live currency formatting
        setupCurrencyFormatter(investmentInput, formattedDisplay);
        setupCurrencyFormatter(extInvestmentInput, extFormattedDisplay);
        for (let i = 0; i < totalMembers; i++) {
            let input = document.getElementById(`edit_investment_amount_${i}`);
            let display = document.getElementById(`edit_investment_amount_formatted_${i}`);
            let shareDisplay = document.getElementById(`edit_profit_share_${i}`);
            input.addEventListener('input', function() {
                const investmentAmount = parseFloat(this.value) || 0;
                // Option 2: Share relative to total invested including this member
                const share = (investmentAmount / (existingInvested + investmentAmount)) * 100;
                shareDisplay.value = share ? share.toFixed(2) + '%' : '';
            });
            setupCurrencyFormatter(input, display);
        }

        investmentInput.addEventListener('input', function() {
            const investmentAmount = parseFloat(this.value) || 0;
            // Option 2: Share relative to total invested including this member
            const share = (investmentAmount / (existingInvested + investmentAmount)) * 100;
            profitShareInput.value = share ? share.toFixed(2) + '%' : '';
        });
        extInvestmentInput.addEventListener('input', function() {
            const investmentAmount = parseFloat(this.value) || 0;
            // Option 2: Share relative to total invested including this member
            const share = (investmentAmount / (existingInvested + investmentAmount)) * 100;
            extProfitShareInput.value = share ? share.toFixed(2) + '%' : '';
        });
    });
</script>

@endpush