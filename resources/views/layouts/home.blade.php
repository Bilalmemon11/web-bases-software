<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Junaid Builders')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    @stack('styles')
</head>

<body class="position-relative">
    <!-- <div id="global-loader" class="position-absolute flex-column  top-0 start-0 loader-overlay active justify-content-center align-items-center w-100" style="z-index:1070; height:100vh; backdrop-filter:blur(2px)">
        <div class="global-loader"></div>
        <p id="loading-label" class="text-center text-primary m-0 mt-1 d-none"></p>
    </div>
    <div id="overlay" class="overlay"></div> -->
    <!-- Top Header -->
    <header class="top-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-building fs-4"></i>
            <h4>Real Estate Project Manager</h4>
        </div>
        <a href="{{route('projects.create')}}" class="btn btn-light btn-sm fw-semibold shadow-sm">
            <i class="fas fa-plus-circle me-2"></i> Create New Project
        </a>
    </header>
    <!-- wrapper-start -->
    <!-- Main Content -->
    <main class="content-wrapper">
        @yield('content')
    </main>
    <!-- wrapper-end -->

    <!-- modal-start -->
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
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('assets/js/ajaxFormHandler.js') }}"></script>
    @stack('scripts')

</body>

</html>