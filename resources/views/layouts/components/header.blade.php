<header class="header bg-primary text-white py-3 shadow-sm">
    <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-light d-md-none" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#sidebarMenu">
                <i class="fas fa-bars"></i>
            </button>
            <div>
                <h4 class="mb-0">{{session('active_project_name')}}</h4>
                <small>Started: {{ \Carbon\Carbon::parse(session('active_project_start_date'))->format('M Y') }} | Status: <span class="text-capitalize">{{session('active_project_status')}}</span></small>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="#" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#editProjectModal"><i
                    class="fas fa-edit"></i> Edit Project</a>
            <a href="{{ route('projects.index') }}" class="btn btn-light btn-sm"><i class="fas fa-exchange-alt"></i> Switch
                Project</a>
            <a href="{{ route('settings.index') }}" class="btn btn-light btn-sm"><i class="fas fa-gear"></i></a>
        </div>
    </div>
</header>