@extends('layouts.home')
@section('title', 'Create Project')
@push('styles')
<style>
    .section-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
    }

    .section-title {
        color: #495057;
        font-size: 1.1rem;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #dee2e6;
    }

    .form-label.required::after {
        content: " *";
        color: #dc3545;
    }

    .member-card,
    .unit-card {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 10px;
        position: relative;
    }

    .remove-btn {
        position: absolute;
        top: 10px;
        right: 10px;
    }

    .member-card h6,
    .unit-card h6 {
        margin-bottom: 10px;
        color: #495057;
    }

    /* Investment Summary Card */
    .investment-summary-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 20px;
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        margin-bottom: 20px;
    }

    .investment-summary-card .summary-label {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-bottom: 5px;
    }

    .investment-summary-card .summary-value {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .investment-summary-card .summary-small-value {
        font-size: 1.5rem;
        font-weight: 600;
    }

    .investment-summary-card .divider {
        height: 60px;
        width: 1px;
        background: rgba(255, 255, 255, 0.3);
    }
</style>
@endpush
@section('content')
<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-4 text-primary"><i class="fas fa-folder-plus me-2"></i> Create New Project</h4>
            <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Projects
            </a>
        </div>

        {{-- Investment Summary Display --}}
        <div class="investment-summary-card">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="summary-label">
                        <i class="fas fa-coins me-2"></i>Total Investment (Auto-Calculated)
                    </div>
                    <h2 class="summary-value" id="total-investment-display">₨ 0.00</h2>
                </div>
                <div class="col-md-1 d-none d-md-flex justify-content-center">
                    <div class="divider"></div>
                </div>
                <div class="col-md-5">
                    <div class="summary-label">
                        <i class="fas fa-chart-pie me-2"></i>Total Profit Share
                    </div>
                    <h3 class="summary-small-value" id="total-profit-share-display">0.00%</h3>
                    <small class="d-block mt-1" style="opacity: 0.8;">Should equal 100%</small>
                </div>
            </div>
        </div>

        <form class="ajax-form needs-validation"
            action="{{ route('projects.store') }}"
            method="POST"
            id="project-form"
            data-redirect="{{ route('projects.index') }}"
            novalidate>
            @csrf

            {{-- STEP 1: Basic Information --}}
            <div class="section-card mb-4">
                <h5 class="section-title"><i class="fas fa-info-circle me-2"></i> Basic Information</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label required">Project Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                            rows="2" placeholder="Project description...">{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label required">Start Date</label>
                        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                            value="{{ old('start_date') }}" required>
                        @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Expected Completion Date</label>
                        <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                            value="{{ old('end_date') }}">
                        @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">
                            Total Investment (₨)
                            <span class="badge bg-info ms-2">Auto-Calculated</span>
                        </label>
                        <input type="number" step="0.01" name="total_investment" id="total_investment"
                            class="form-control  @error('total_investment') is-invalid @enderror"
                            value="{{ old('total_investment') }}" readonly>
                        <small class="text-muted" id="investment-formatted">Calculated from member investments</small>
                        @error('total_investment')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Land Cost (₨)</label>
                        <input type="number" step="0.01" name="land_cost" id="land_cost"
                            class="form-control @error('land_cost') is-invalid @enderror"
                            value="{{ old('land_cost') }}" placeholder="0.00">
                        <small class="text-muted" id="land-cost-formatted"></small>
                        @error('land_cost')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- STEP 2: Members --}}
            <div class="section-card mb-4">
                <h5 class="section-title"><i class="fas fa-users me-2"></i> Project Members</h5>

                <div class="mb-3">
                    <label class="form-label">Add Existing Member</label>
                    <select id="existing-member-select" class="form-select">
                        <option value="">-- Search and select a member --</option>
                        @foreach($members as $member)
                        <option value="{{ $member->id }}"
                            data-name="{{ $member->name }}"
                            data-phone="{{ $member->phone }}"
                            data-cnic="{{ $member->cnic }}">
                            {{ $member->name }} {{ $member->phone ? '(' . $member->phone . ')' : '' }}
                        </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Select a member and they'll be added to the list below</small>
                </div>

                <div id="selected-members" class="mb-3 row g-1">
                    <div class="col col-md-6">
                        <div class="member-card m-0" data-member-id="0">
                            <h6 class="mb-1"><i class="fas fa-user-shield me-2 text-primary"></i>{{$manager->name}} <span class="badge bg-primary">Manager</span></h6>
                            <div class="row g-2 mb-2">
                                <div class="col-12">
                                    <small class="text-muted">Phone: {{$manager->phone}} | CNIC: {{$manager->cnic}}</small>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label form-label-sm required">Investment Amount (₨)</label>
                                    <input type="number" name="manager_investment_amount"
                                        class="form-control form-control-sm"
                                        placeholder="0.00" step="0.01" data-member-id="0" required>
                                    <small class="investment-formatted-existing-0"></small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label form-label-sm required">Profit Share (%)</label>
                                    <input type="number" name="manager_profit_share"
                                        class="form-control form-control-sm" placeholder="0" min="0" max="100" step="0.01" required>
                                    <small class="text-muted">Auto-calculated, editable</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-sm btn-outline-success" id="add-new-member-btn">
                    <i class="fas fa-user-plus"></i> Create & Add New Member
                </button>

                <div id="new-members-container" class="mt-3"></div>
            </div>

            {{-- STEP 3: Units --}}
            <div class="section-card mb-4">
                <h5 class="section-title"><i class="fas fa-building me-2"></i> Project Units</h5>

                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label class="form-label">Select Predefined Unit Type</label>
                        <select id="predefined-unit-select" class="form-select">
                            <option value="">-- Select a unit type --</option>
                            @foreach($predefinedUnits as $u)
                            <option value="{{ $u->id }}"
                                data-type="{{ $u->type }}"
                                data-size="{{ $u->size }}"
                                data-sale="{{ $u->default_sale_price }}">
                                {{ $u->type }} - {{ $u->size }}, Sale: ₨{{ number_format($u->default_sale_price) }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Quantity</label>
                        <input type="number" id="unit-quantity" class="form-control" value="1" min="1" max="100">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-primary w-100" id="add-predefined-units-btn">
                            <i class="fas fa-plus"></i> Add
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted">Or add custom units:</span>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-custom-unit-btn">
                        <i class="fas fa-plus"></i> Add Custom Unit
                    </button>
                </div>

                <div id="units-container" class="mt-3"></div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Create Project
                </button>
                <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let memberIndex = 0;
        let unitIndex = 0;
        const selectedMemberIds = new Set();

        // ==================== AUTO-CALCULATE TOTAL INVESTMENT ====================

        function calculateTotalInvestment() {
            let total = 0;

            // Manager investment
            const managerInvestment = parseFloat(document.querySelector('[name="manager_investment_amount"]')?.value) || 0;
            total += managerInvestment;

            // Existing members
            document.querySelectorAll('[name^="existing_members"][name$="[investment_amount]"]').forEach(input => {
                total += parseFloat(input.value) || 0;
            });

            // New members
            document.querySelectorAll('.member-investment-new').forEach(input => {
                total += parseFloat(input.value) || 0;
            });

            return total;
        }

        function updateTotalInvestmentDisplay() {
            const total = calculateTotalInvestment();
            const totalInput = document.getElementById('total_investment');
            const displayElement = document.getElementById('total-investment-display');
            const formattedElement = document.getElementById('investment-formatted');

            // Update the input value (read-only now)
            if (totalInput) {
                totalInput.value = total.toFixed(2);
            }

            // Update the large display
            if (displayElement) {
                displayElement.textContent = formatPakistaniCurrency(total);
            }

            // Update the small formatted text
            if (formattedElement) {
                formattedElement.textContent = formatPakistaniCurrency(total);
            }

            // Recalculate all profit shares when total changes
            updateAllProfitShares();
        }

        // ==================== AUTO-CALCULATE PROFIT SHARES ====================


        function updateAllProfitShares() {
            const totalInvestment = calculateTotalInvestment();

            if (totalInvestment === 0) return;

            // Update manager share
            const managerInvestmentInput = document.querySelector('[name="manager_investment_amount"]');
            const managerShareInput = document.querySelector('[name="manager_profit_share"]');
            if (managerInvestmentInput && managerShareInput && !managerShareInput.dataset.manuallyEdited) {
                const investment = parseFloat(managerInvestmentInput.value) || 0;
                managerShareInput.value = calculateProfitShare(investment, totalInvestment);
            }

            // Update existing members
            document.querySelectorAll('[name^="existing_members"][name$="[investment_amount]"]').forEach(input => {
                const memberId = input.name.match(/existing_members\[(\d+)\]/)[1];
                const investmentAmount = parseFloat(input.value) || 0;
                const shareInput = document.querySelector(`[name="existing_members[${memberId}][profit_share]"]`);

                if (investmentAmount > 0 && shareInput && !shareInput.dataset.manuallyEdited) {
                    shareInput.value = calculateProfitShare(investmentAmount, totalInvestment);
                }
            });

            // Update new members
            document.querySelectorAll('.member-investment-new').forEach(input => {
                const index = input.dataset.index;
                const investmentAmount = parseFloat(input.value) || 0;
                const shareInput = document.querySelector(`[name="new_members[${index}][profit_share]"]`);

                if (investmentAmount > 0 && shareInput && !shareInput.dataset.manuallyEdited) {
                    shareInput.value = calculateProfitShare(investmentAmount, totalInvestment);
                }
            });

            // Update total shares display
            updateTotalProfitShareDisplay();
        }

        function updateTotalProfitShareDisplay() {
            let totalShare = 0;

            // Manager share
            const managerShare = parseFloat(document.querySelector('[name="manager_profit_share"]')?.value) || 0;
            totalShare += managerShare;

            // Existing members
            document.querySelectorAll('[name^="existing_members"][name$="[profit_share]"]').forEach(input => {
                totalShare += parseFloat(input.value) || 0;
            });

            // New members
            document.querySelectorAll('[name^="new_members"][name$="[profit_share]"]').forEach(input => {
                totalShare += parseFloat(input.value) || 0;
            });

            const displayElement = document.getElementById('total-profit-share-display');
            if (displayElement) {
                displayElement.textContent = totalShare.toFixed(2) + '%';

                // Color coding
                displayElement.classList.remove('text-white', 'text-warning', 'text-danger');
                if (Math.abs(totalShare - 100) < 0.01) {
                    displayElement.classList.add('text-white');
                } else if (totalShare > 100) {
                    displayElement.classList.add('text-danger');
                } else {
                    displayElement.classList.add('text-warning');
                }
            }
        }

        // ==================== SETUP EXISTING FIELDS ====================

        // Setup land cost formatter
        const landCostInput = document.getElementById('land_cost');
        const landCostDisplay = document.getElementById('land-cost-formatted');
        if (landCostInput && landCostDisplay) {
            setupCurrencyFormatter(landCostInput, landCostDisplay);
        }

        // Setup manager investment formatter and auto-calculation
        const managerInvestmentInput = document.querySelector('[name="manager_investment_amount"]');
        if (managerInvestmentInput) {
            const displayElement = document.querySelector('.investment-formatted-existing-0');
            if (displayElement) {
                setupCurrencyFormatter(managerInvestmentInput, displayElement);
            }

            // Trigger total calculation on change
            managerInvestmentInput.addEventListener('input', updateTotalInvestmentDisplay);
        }

        // Setup manager profit share to update total when changed
        const managerShareInput = document.querySelector('[name="manager_profit_share"]');
        if (managerShareInput) {
            managerShareInput.addEventListener('input', function() {
                this.dataset.manuallyEdited = 'true';
                updateTotalProfitShareDisplay();
            });
        }

        // ==================== MEMBERS ====================

        // Add existing member
        document.getElementById('existing-member-select').addEventListener('change', function() {
            const select = this;
            const memberId = select.value;

            if (!memberId || selectedMemberIds.has(memberId)) {
                if (selectedMemberIds.has(memberId)) {
                    alert('This member is already added!');
                }
                select.value = '';
                return;
            }

            const option = select.options[select.selectedIndex];
            const name = option.dataset.name;
            const phone = option.dataset.phone || 'N/A';
            const cnic = option.dataset.cnic || 'N/A';

            addExistingMemberCard(memberId, name, phone, cnic);
            selectedMemberIds.add(memberId);
            select.value = '';
        });

        function addExistingMemberCard(memberId, name, phone, cnic) {
            const html = `
            <div class="col col-md-6">
        <div class="member-card m-0" data-member-id="${memberId}">
            <button type="button" class="btn btn-sm btn-danger remove-btn remove-member">
                <i class="fas fa-times"></i>
            </button>
            <h6><i class="fas fa-user me-2"></i>${name}</h6>
            <div class="row g-2 mb-2">
                <div class="col-12">
                    <small class="text-muted">Phone: ${phone} | CNIC: ${cnic}</small>
                </div>
            </div>
            <div class="row g-2">
                <div class="col-md-6">
                    <label class="form-label form-label-sm">Investment Amount (₨)</label>
                    <input type="number" name="existing_members[${memberId}][investment_amount]" 
                           class="form-control form-control-sm member-investment-existing" 
                           placeholder="0.00" step="0.01" data-member-id="${memberId}" required>
                    <small class="investment-formatted-existing-${memberId}"></small>
                </div>
                <div class="col-md-6">
                    <label class="form-label form-label-sm">Profit Share (%)</label>
                    <input type="number" name="existing_members[${memberId}][profit_share]" 
                           class="form-control form-control-sm profit-share-existing" placeholder="0" 
                           min="0" max="100" step="0.01" data-member-id="${memberId}" required>
                    <small class="text-muted">Auto-calculated</small>
                </div>
            </div>
        </div>
        </div>
    `;
            document.getElementById('selected-members').insertAdjacentHTML('beforeend', html);

            // Setup formatter for the new input
            const input = document.querySelector(`[name="existing_members[${memberId}][investment_amount]"]`);
            const display = document.querySelector(`.investment-formatted-existing-${memberId}`);
            setupCurrencyFormatter(input, display);

            // Add event listener for total calculation
            input.addEventListener('input', updateTotalInvestmentDisplay);

            // Add event listener for profit share manual edit
            const shareInput = document.querySelector(`[name="existing_members[${memberId}][profit_share]"]`);
            shareInput.addEventListener('input', function() {
                this.dataset.manuallyEdited = 'true';
                updateTotalProfitShareDisplay();
            });
        }

        // Add new member
        document.getElementById('add-new-member-btn').addEventListener('click', function() {
            const html = `
        <div class="member-card new-member">
            <button type="button" class="btn btn-sm btn-danger remove-btn remove-new-member">
                <i class="fas fa-times"></i>
            </button>
            <h6><i class="fas fa-user-plus me-2"></i>New Member #${memberIndex + 1}</h6>
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label form-label-sm required">Name</label>
                    <input type="text" name="new_members[${memberIndex}][name]" 
                           class="form-control form-control-sm" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label form-label-sm">Phone</label>
                    <input type="text" name="new_members[${memberIndex}][phone]" 
                           class="form-control form-control-sm" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label form-label-sm">CNIC</label>
                    <input type="text" name="new_members[${memberIndex}][cnic]" 
                           class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <label class="form-label form-label-sm">Address</label>
                    <textarea  name="new_members[${memberIndex}][address]" 
                           class="form-control form-control-sm"></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label form-label-sm">Investment (₨)</label>
                    <input type="number" name="new_members[${memberIndex}][investment_amount]" 
                           class="form-control form-control-sm member-investment-new" step="0.01"
                           data-index="${memberIndex}" required>
                    <small class="investment-formatted-new-${memberIndex}"></small>
                </div>
                <div class="col-md-4">
                    <label class="form-label form-label-sm">Profit Share (%)</label>
                    <input type="number" name="new_members[${memberIndex}][profit_share]" 
                           class="form-control form-control-sm profit-share-input-new" 
                           min="0" max="100" step="0.01" data-index="${memberIndex}" required>
                    <small class="text-muted">Auto-calculated</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label form-label-sm">Role</label>
                    <select name="new_members[${memberIndex}][role]" class="form-select form-select-sm">
                        <option value="investor">Investor</option>
                    </select>
                </div>
            </div>
        </div>
    `;
            document.getElementById('new-members-container').insertAdjacentHTML('beforeend', html);

            // Setup formatter for the new input
            const input = document.querySelector(`[name="new_members[${memberIndex}][investment_amount]"]`);
            const display = document.querySelector(`.investment-formatted-new-${memberIndex}`);
            setupCurrencyFormatter(input, display);

            // Add event listener for total calculation
            input.addEventListener('input', updateTotalInvestmentDisplay);

            // Add event listener for profit share manual edit
            const shareInput = document.querySelector(`[name="new_members[${memberIndex}][profit_share]"]`);
            shareInput.addEventListener('input', function() {
                this.dataset.manuallyEdited = 'true';
                updateTotalProfitShareDisplay();
            });

            memberIndex++;
        });

        // Remove members
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-member')) {
                const card = e.target.closest('.member-card');
                const memberId = card.dataset.memberId;
                selectedMemberIds.delete(memberId);
                card.remove();
                updateTotalInvestmentDisplay();
            }
            if (e.target.closest('.remove-new-member')) {
                e.target.closest('.member-card').remove();
                updateTotalInvestmentDisplay();
            }
        });

        // ==================== UNITS ====================

        // Add predefined units
        document.getElementById('add-predefined-units-btn').addEventListener('click', function() {
            const select = document.getElementById('predefined-unit-select');
            const quantity = parseInt(document.getElementById('unit-quantity').value) || 1;

            if (!select.value) {
                alert('Please select a unit type first!');
                return;
            }

            const option = select.options[select.selectedIndex];
            const type = option.dataset.type;
            const size = option.dataset.size;
            const cost = option.dataset.cost;
            const sale = option.dataset.sale;

            addUnitCard(type, size, cost, sale, quantity, 'predefined');

            select.value = '';
            document.getElementById('unit-quantity').value = 1;
            reindexUnitCards();
        });

        // Add custom unit
        document.getElementById('add-custom-unit-btn').addEventListener('click', function() {
            addUnitCard('', '', 0, 0, 1);
            reindexUnitCards();
        });

        function addUnitCard(type, size, cost, sale, qty = 0, source = 'custom') {
            const html = `
                 <div class="unit-card">
                     <button type="button" class="btn btn-sm btn-danger remove-btn remove-unit">
                         <i class="fas fa-times"></i>
                     </button>
                     <h6><i class="fas fa-home me-2"></i>Unit #${unitIndex + 1}</h6>
                     <div class="row g-2">
                         <div class="col-md-2">
                             <label class="form-label form-label-sm">Type</label>
                             <input type="text" name="units[${unitIndex}][type]" 
                                    class="form-control form-control-sm" value="${type}" placeholder="e.g., 2BHK" required>
                         </div>
                         <div class="col-md-2">
                             <label class="form-label form-label-sm">Size</label>
                             <input type="text" name="units[${unitIndex}][size]" 
                                    class="form-control form-control-sm" value="${size}" placeholder="e.g., 1200 sqft" required>
                         </div>
                         <div class="col-md-2">
                             <label class="form-label form-label-sm">Sale Price (₨)</label>
                             <input type="number" name="units[${unitIndex}][sale_price]" 
                                    class="form-control form-control-sm" value="${sale}" step="0.01" oninput="currencyFormat(this,'unit-price-format-${unitIndex}')">
                                    <small id="unit-price-format-${unitIndex}"></small>
                         </div>
                         
                         <div class="col-md-2">
                             <label class="form-label form-label-sm">Quantity</label>
                             <input type="number" name="units[${unitIndex}][qty]" 
                                    class="form-control form-control-sm unit-qty" required value="${qty}" min="1">
                         </div>
                     </div>
                 </div>
                 `;
            document.getElementById('units-container').insertAdjacentHTML('beforeend', html);
            unitIndex++;
        }

        // Remove units
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-unit')) {
                e.target.closest('.unit-card').remove();
                reindexUnitCards();
            }
        });

        // When user edits any qty field, reindex dynamically
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('unit-qty')) {
                reindexUnitCards();
            }
        });

        function reindexUnitCards() {
            const unitCards = document.querySelectorAll('.unit-card');
            let start = 1;

            unitCards.forEach((card, index) => {
                const qtyInput = card.querySelector('.unit-qty');
                const qty = parseInt(qtyInput.value) || 1;
                const end = start + qty - 1;

                // Update the title
                const title = card.querySelector('h6');
                title.innerHTML = `<i class="fas fa-home me-2"></i>Unit #${start}${qty > 1 ? ' - #' + end : ''}`;

                // Update name attributes
                card.querySelectorAll('input, select').forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        input.setAttribute('name', name.replace(/units\[\d+\]/, `units[${index}]`));
                    }
                });

                start = end + 1; // next group starts after this batch
            });

            unitIndex = unitCards.length;
        }



        // ==================== FORM VALIDATION ====================

        // Clear date custom validity when user edits dates
        document.querySelectorAll('[name="start_date"], [name="end_date"]').forEach(input => {
            input.addEventListener('input', function() {
                const form = document.getElementById('project-form');
                if (form) form.classList.remove('was-validated');
                const end = document.querySelector('[name="end_date"]');
                if (end) end.setCustomValidity('');
            });
        });

        document.getElementById('project-form').addEventListener('submit', function(e) {
            const form = this;
            const startInput = form.querySelector('[name="start_date"]');
            const endInput = form.querySelector('[name="end_date"]');

            // reset any previous custom validity
            if (endInput) endInput.setCustomValidity('');

            // custom date validation
            if (startInput && endInput && startInput.value && endInput.value) {
                const startDate = new Date(startInput.value);
                const endDate = new Date(endInput.value);
                if (endDate < startDate) {
                    endInput.setCustomValidity('End date cannot be before start date!');
                }
            }

            // Bootstrap validation check
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                form.classList.add('was-validated');

                // If the date is the problem, surface a message
                if (endInput && endInput.validationMessage) {
                    alert(endInput.validationMessage);
                }

                return false;
            }
        });

        // Initialize displays on page load
        updateTotalInvestmentDisplay();
        updateTotalProfitShareDisplay();
    });
</script>
@endpush
@endsection