<div class="sidebar">
    <div class="list-group list-group-flush">
        <a href="{{ route('projects.dashboard', session('active_project_slug')) }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('projects.dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-line me-2"></i> Dashboard
        </a>
        <a href="{{ route('members.index', session('active_project_slug')) }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('members.index') ? 'active' : '' }}">
            <i class="fas fa-users me-2"></i> Members
        </a>
        <a href="{{ route('clients.index', session('active_project_slug')) }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('clients.index') ? 'active' : '' }}">
            <i class="fas fa-user-tie me-2"></i> Clients
        </a>
        <a href="{{ route('units.index', session('active_project_slug')) }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('units.index') ? 'active' : '' }}">
            <i class="fas fa-building me-2"></i> Units
        </a>
        <a href="{{ route('sales.index', session('active_project_slug')) }}" 
           class="list-group-item list-group-item-action {{ (request()->routeIs('sales.index') || request()->routeIs('sales.edit') || request()->routeIs('sales.show') || request()->routeIs('sales.create')) ? 'active' : '' }}">
            <i class="fas fa-dollar-sign me-2"></i> Sales
        </a>
        <a href="{{ route('expenses.index', session('active_project_slug')) }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('expenses.index') ? 'active' : '' }}">
            <i class="fas fa-receipt me-2"></i> Expenses
        </a>
        <a href="{{ route('reports.index', session('active_project_slug')) }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="fas fa-file-alt me-2"></i> Reports
        </a>
    </div>
</div>