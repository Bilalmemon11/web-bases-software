@extends('layouts.app')
@section('title', 'Reports - Junaid Builders')
@section('content')
<h3 class="mb-4"><i class="fas fa-chart-bar me-2"></i>Reports & Analytics</h3>

<section class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-4">
                <i class="fas fa-file-alt text-primary me-2"></i> Available Reports
            </h5>
            <div class="row g-4">
                {{-- Overall Report --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-primary shadow-sm">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-chart-pie fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title">Overall Project Report</h5>
                            <p class="card-text text-muted">
                                Comprehensive overview of project finances, progress, and statistics
                            </p>
                            <div class="d-grid gap-2">
                                <a href="{{ route('reports.overall', $project->slug) }}" 
                                   class="btn btn-primary">
                                    <i class="fas fa-eye me-2"></i>View Report
                                </a>
                                <a href="{{ route('reports.download', ['project' => $project->slug, 'type' => 'overall']) }}" 
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-download me-2"></i>Download PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Members Report --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-success shadow-sm">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-users fa-3x text-success"></i>
                            </div>
                            <h5 class="card-title">Members Report</h5>
                            <p class="card-text text-muted">
                                Investment details, profit shares, and member roles
                            </p>
                            <div class="d-grid gap-2">
                                <a href="{{ route('reports.members', $project->slug) }}" 
                                   class="btn btn-success">
                                    <i class="fas fa-eye me-2"></i>View Report
                                </a>
                                <a href="{{ route('reports.download', ['project' => $project->slug, 'type' => 'members']) }}" 
                                   class="btn btn-outline-success">
                                    <i class="fas fa-download me-2"></i>Download PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Clients Report --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-info shadow-sm">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-user-tie fa-3x text-info"></i>
                            </div>
                            <h5 class="card-title">Clients Report</h5>
                            <p class="card-text text-muted">
                                Client purchases, payments, and outstanding balances
                            </p>
                            <div class="d-grid gap-2">
                                <a href="{{ route('reports.clients', $project->slug) }}" 
                                   class="btn btn-info text-white">
                                    <i class="fas fa-eye me-2"></i>View Report
                                </a>
                                <a href="{{ route('reports.download', ['project' => $project->slug, 'type' => 'clients']) }}" 
                                   class="btn btn-outline-info">
                                    <i class="fas fa-download me-2"></i>Download PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Units Report --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-warning shadow-sm">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-building fa-3x text-warning"></i>
                            </div>
                            <h5 class="card-title">Units Report</h5>
                            <p class="card-text text-muted">
                                Inventory status, unit types, and availability
                            </p>
                            <div class="d-grid gap-2">
                                <a href="{{ route('reports.units', $project->slug) }}" 
                                   class="btn btn-warning">
                                    <i class="fas fa-eye me-2"></i>View Report
                                </a>
                                <a href="{{ route('reports.download', ['project' => $project->slug, 'type' => 'units']) }}" 
                                   class="btn btn-outline-warning">
                                    <i class="fas fa-download me-2"></i>Download PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sales Report --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-danger shadow-sm">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-money-bill-wave fa-3x text-danger"></i>
                            </div>
                            <h5 class="card-title">Sales Report</h5>
                            <p class="card-text text-muted">
                                Sales transactions, revenue, and payment tracking
                            </p>
                            <div class="d-grid gap-2">
                                <a href="{{ route('reports.sales', $project->slug) }}" 
                                   class="btn btn-danger">
                                    <i class="fas fa-eye me-2"></i>View Report
                                </a>
                                <a href="{{ route('reports.download', ['project' => $project->slug, 'type' => 'sales']) }}" 
                                   class="btn btn-outline-danger">
                                    <i class="fas fa-download me-2"></i>Download PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Expenses Report --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-secondary shadow-sm">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-receipt fa-3x text-secondary"></i>
                            </div>
                            <h5 class="card-title">Expenses Report</h5>
                            <p class="card-text text-muted">
                                Cost breakdown, expense categories, and spending trends
                            </p>
                            <div class="d-grid gap-2">
                                <a href="{{ route('reports.expenses', $project->slug) }}" 
                                   class="btn btn-secondary">
                                    <i class="fas fa-eye me-2"></i>View Report
                                </a>
                                <a href="{{ route('reports.download', ['project' => $project->slug, 'type' => 'expenses']) }}" 
                                   class="btn btn-outline-secondary">
                                    <i class="fas fa-download me-2"></i>Download PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection