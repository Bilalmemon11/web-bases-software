<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Junaid Builders')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/sortTable.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    @stack('styles')
</head>

<body class="position-relative">

    <div class="main-container">
        <!-- Header -->
        @include('layouts.components.header')
        <!-- End Header -->
        <!-- Content area -->
        <div class="content">
            <!-- Sidebar -->
            @include('layouts.components.sidebar')
            <!-- End Sidebar -->
            <!-- Main Content -->
            <div class="main-content">
                @yield('content')
            </div>
        </div>
    </div>
    <!-- modal-start -->
    @include('layouts.components.global-modals')
    @if(session('active_project_data'))
    @php
    $project = session('active_project_data');
    @endphp
    <div class="modal fade" id="editProjectModal" tabindex="-1" aria-labelledby="editProjectModalLabel-{{ $project->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('projects.update', $project->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProjectModalLabel-{{ $project->id }}">
                            <i class="fas fa-edit text-primary me-2"></i> Edit Project
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="edit_name_{{ $project->id }}" class="form-label">Project Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="edit_name_{{ $project->id }}" name="name" value="{{ old('name', $project->name) }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="edit_description_{{ $project->id }}" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="edit_description_{{ $project->id }}" name="description" rows="3">{{ old('description', $project->description) }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="edit_start_date_{{ $project->id }}" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="edit_start_date_{{ $project->id }}" name="start_date" value="{{ old('start_date', $project->start_date) }}" required>
                                @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="edit_end_date_{{ $project->id }}" class="form-label">End Date</label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="edit_end_date_{{ $project->id }}" name="end_date" value="{{ old('end_date', $project->end_date) }}">
                                @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="edit_total_investment_{{ $project->id }}" class="form-label">Total Investment</label>
                                <input type="number" disabled step="0.01" class="form-control @error('total_investment') is-invalid @enderror" id="edit_total_investment_{{ $project->id }}" name="total_investment" value="{{ old('total_investment', $project->total_investment) }}">
                                @error('total_investment')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="edit_land_cost_{{ $project->id }}" class="form-label">Land Cost</label>
                                <input type="number" step="0.01" class="form-control @error('land_cost') is-invalid @enderror" id="edit_land_cost_{{ $project->id }}" name="land_cost" oninput="currencyFormat(this,'curr-display-for-project-{{$project->id}}')" value="{{ old('land_cost', $project->land_cost) }}">
                                <small id="curr-display-for-project-{{$project->id}}" class="d-block"></small>
                                @error('land_cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="edit_sale_price_{{ $project->id }}" class="form-label">Sale Price</label>
                                <input type="number" step="0.01" class="form-control @error('sale_price') is-invalid @enderror" id="edit_sale_price_{{ $project->id }}" name="sale_price" value="{{ old('sale_price', $project->sale_price) }}">
                                @error('sale_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="edit_status_{{ $project->id }}" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="edit_status_{{ $project->id }}" name="status" required>
                                    <option value="active" {{ old('status', $project->status) === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="completed" {{ old('status', $project->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="on_hold" {{ old('status', $project->status) === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                    <option value="archived" {{ old('status', $project->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Project
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    @stack('modals')
    <div class="toast-container position-fixed bottom-0 end-0 p-3" id="custom-toasts" style="z-index: 1100">
        @if(session('success'))
        <div class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="toast align-items-center text-bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        @endif

        @if(session('warning'))
        <div class="toast align-items-center text-bg-warning border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-exclamation-triangle me-2"></i> {{ session('warning') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        @endif

        @if(session('info'))
        <div class="toast align-items-center text-bg-info border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-info-circle me-2"></i> {{ session('info') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        @endif

        {{-- Validation Errors Toast --}}
        @if($errors->any())
        <div class="toast align-items-center text-bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-times-circle me-2"></i>
                    <strong>Validation Errors:</strong>
                    <ul class="mb-0 mt-1 ps-3">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        @endif
    </div>

    {{-- Auto-hide toasts after 8 seconds (except errors which stay longer) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toasts = document.querySelectorAll('.toast.show');
            toasts.forEach(toast => {
                const isDanger = toast.classList.contains('text-bg-danger');
                const delay = isDanger ? 12000 : 8000; // Errors stay 12 seconds, others 8 seconds

                setTimeout(() => {
                    const bsToast = new bootstrap.Toast(toast);
                    bsToast.hide();
                }, delay);
            });
        });
    </script>
    <!-- modal-end -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('assets/js/sortTable.js') }}"></script>
    <script src="{{ asset('assets/js/modals.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @stack('scripts')
    <!-- <script>
        // Function to show the loading bar
        function showLoadingBar() {
            document.getElementById('loading-bar').style.width = '100%';
        }

        // Function to hide the loading bar
        function hideLoadingBar() {
            document.getElementById('loading-bar').style.width = '0';
        }

        // Event listener for DOMContentLoaded
        document.addEventListener('DOMContentLoaded', function() {
            hideLoadingBar(); // Hide the loading bar once the DOM content is loaded
        });

        // Event listener for page unload (before leaving the page)
        window.addEventListener('beforeunload', function() {
            showLoadingBar(); // Show the loading bar when leaving the page
        });
    </script>
    <div id="loading-bar"></div>
    <style>
        #loading-bar {
            height: 8px;
            /* Increased height for better visibility */
            background: linear-gradient(45deg, #ffffffff, #00000032, #d15e16ff);
            background-size: 400% 400%;
            animation: gradient 3s linear infinite;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 9999;
            transition: width 0.5s;
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }

            100% {
                background-position: 100% 50%;
            }
        }
    </style> -->
</body>

</html>