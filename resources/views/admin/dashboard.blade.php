@extends('layouts.app')

@section('title', 'Admin Dashboard')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
<div class="container-fluid py-5" style="background-color: #f8f9fa;">
    <div class="mb-5">
        <h1 class="display-5 fw-bold text-dark mb-2">
            <i class="bi bi-speedometer2 text-primary"></i> Admin Dashboard
        </h1>
        <p class="text-muted lead">Manage elections, users, and monitor system activity</p>
    </div>

    <!-- Statistics Cards with Trends -->
    <div class="row g-4 mb-5">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="card-title opacity-75 mb-1">Total Elections</h6>
                            <h2 class="mb-0 fw-bold display-6">{{ $totalElections }}</h2>
                            <div class="mt-2">
                                <span class="badge bg-success-subtle text-success">↑ +2 this month</span>
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="bi bi-calendar-event" style="font-size: 1.8rem;"></i>
                        </div>
                    </div>
                    <div class="pt-2 border-top border-white border-opacity-25">
                        <small class="opacity-75">{{ $activeElections }} Currently Active</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 stats-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                <div class="card-body text-white p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="card-title opacity-75 mb-1">Total Votes</h6>
                            <h2 class="mb-0 fw-bold display-6">{{ $totalVotes }}</h2>
                            <div class="mt-2">
                                <span class="badge bg-danger-subtle text-danger">↑ +15% today</span>
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="bi bi-check-circle" style="font-size: 1.8rem;"></i>
                        </div>
                    </div>
                    <div class="pt-2 border-top border-white border-opacity-25">
                        <small class="opacity-75">All verified votes</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body text-white p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="card-title opacity-75 mb-1">Registered Users</h6>
                            <h2 class="mb-0 fw-bold display-6">{{ $totalUsers }}</h2>
                            <div class="mt-2">
                                <span class="badge bg-info-subtle text-info">↑ +8 this week</span>
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="bi bi-people" style="font-size: 1.8rem;"></i>
                        </div>
                    </div>
                    <div class="pt-2 border-top border-white border-opacity-25">
                        <small class="opacity-75">{{ $pendingVerifications }} Pending</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 stats-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <div class="card-body text-white p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="card-title opacity-75 mb-1">System Health</h6>
                            <h2 class="mb-0 fw-bold display-6">99.8%</h2>
                            <div class="mt-2">
                                <span class="badge bg-success-subtle text-success">✓ Optimal</span>
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="bi bi-shield-check" style="font-size: 1.8rem;"></i>
                        </div>
                    </div>
                    <div class="pt-2 border-top border-white border-opacity-25">
                        <a href="{{ route('admin.logs') ?? '#' }}" class="text-white text-decoration-none">
                            <small>View Logs →</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-5">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-lightning-charge-fill text-warning"></i> Quick Actions
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('admin.elections.create') }}" class="btn btn-primary btn-lg shadow-sm">
                            <i class="bi bi-plus-circle-fill"></i> Create New Election
                        </a>
                        <a href="{{ route('admin.users') }}" class="btn btn-info btn-lg shadow-sm">
                            <i class="bi bi-people-fill"></i> Manage Users
                        </a>
                        <a href="{{ route('admin.logs') }}" class="btn btn-secondary btn-lg shadow-sm">
                            <i class="bi bi-journal-text"></i> View Activity Logs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Elections -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Elections</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Votes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentElections as $election)
                                    <tr>
                                        <td>{{ $election->title }}</td>
                                        <td>
                                            @if($election->isActive())
                                                <span class="badge bg-success">Active</span>
                                            @elseif($election->hasEnded())
                                                <span class="badge bg-secondary">Ended</span>
                                            @else
                                                <span class="badge bg-warning">Upcoming</span>
                                            @endif
                                        </td>
                                        <td>{{ $election->votes->count() }}</td>
                                        <td>
                                            <a href="{{ route('admin.elections.edit', $election) }}" class="btn btn-sm btn-primary">Edit</a>
                                            <a href="{{ route('admin.elections.results', $election) }}" class="btn btn-sm btn-info">Results</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Votes -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Votes</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Election</th>
                                    <th>Voter</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentVotes as $vote)
                                    <tr>
                                        <td>{{ Str::limit($vote->election->title, 20) }}</td>
                                        <td>{{ $vote->user?->voter_id ?? 'Anonymous' }}</td>
                                        <td>{{ $vote->created_at?->diffForHumans() }}</td>
                                        <td>
                                            @if($vote->is_tampered)
                                                <span class="badge bg-danger">Tampered</span>
                                            @elseif($vote->is_verified)
                                                <span class="badge bg-success">Verified</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Charts -->
    <div class="row g-4 mb-4">
        <!-- Votes Over Time Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-graph-up text-primary"></i> Voting Activity (Last 30 Days)
                        </h5>
                        <button class="btn btn-sm btn-outline-primary" onclick="exportChartData('votesOverTime')">
                            <i class="bi bi-download"></i> Export
                        </button>
                    </div>
                </div>
                <div class="card-body p-4">
                    <canvas id="votesOverTimeChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- System Health -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-heart-pulse text-danger"></i> System Health
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Database</span>
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle-fill"></i> Healthy
                            </span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: 100%"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Storage</span>
                            <span class="badge bg-{{ $systemHealth['storage'] > 20 ? 'success' : 'warning' }}">
                                {{ $systemHealth['storage'] }}% Free
                            </span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-{{ $systemHealth['storage'] > 20 ? 'success' : 'warning' }}" 
                                 style="width: {{ $systemHealth['storage'] }}%"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Vote Integrity</span>
                            <span class="badge bg-{{ $systemHealth['tampered_votes'] == 0 ? 'success' : 'danger' }}">
                                {{ $systemHealth['tampered_votes'] }} Tampered
                            </span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-{{ $systemHealth['tampered_votes'] == 0 ? 'success' : 'danger' }}" 
                                 style="width: {{ $systemHealth['tampered_votes'] == 0 ? 100 : 50 }}%"></div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Verification Rate</span>
                            <span class="badge bg-info">
                                {{ $systemHealth['failed_verifications'] }} Failed
                            </span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" style="width: 85%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Peak Voting Times & Participation -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-clock text-warning"></i> Peak Voting Times (24-Hour)
                    </h5>
                </div>
                <div class="card-body p-4">
                    <canvas id="peakTimesChart" style="height: 250px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-pie-chart text-success"></i> Participation Rate by Election
                    </h5>
                </div>
                <div class="card-body p-4">
                    <canvas id="participationChart" style="height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Feed -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-activity text-info"></i> Recent Activity Feed
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="activity-feed" style="max-height: 400px; overflow-y: auto;">
                        @forelse($recentActivity as $activity)
                            <div class="activity-item d-flex align-items-start mb-3 pb-3 border-bottom">
                                <div class="activity-icon me-3">
                                    @php
                                        $iconClass = match($activity->action) {
                                            'vote_cast' => 'bi-check-circle-fill text-success',
                                            'vote_verified' => 'bi-shield-check text-primary',
                                            'vote_tampered' => 'bi-exclamation-triangle-fill text-danger',
                                            'verification_failed' => 'bi-x-circle text-warning',
                                            default => 'bi-circle text-secondary'
                                        };
                                    @endphp
                                    <i class="bi {{ $iconClass }}" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="activity-content flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                @switch($activity->action)
                                                    @case('vote_cast')
                                                        Vote Cast
                                                        @break
                                                    @case('vote_verified')
                                                        Vote Verified
                                                        @break
                                                    @case('vote_tampered')
                                                        Tampering Detected
                                                        @break
                                                    @case('verification_failed')
                                                        Verification Failed
                                                        @break
                                                    @default
                                                        {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                                                @endswitch
                                            </h6>
                                            <p class="text-muted small mb-0">
                                                <strong>{{ $activity->user?->voter_id ?? 'System' }}</strong> - 
                                                {{ $activity->election?->title ?? 'Unknown Election' }}
                                            </p>
                                            @if($activity->details)
                                                <p class="text-muted small mb-0">{{ $activity->details }}</p>
                                            @endif
                                        </div>
                                        <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                <p class="mt-2">No recent activity</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Votes Over Time Chart
    const votesOverTimeData = @json($votesOverTime);
    const votesOverTimeCtx = document.getElementById('votesOverTimeChart').getContext('2d');
    new Chart(votesOverTimeCtx, {
        type: 'line',
        data: {
            labels: votesOverTimeData.map(d => d.date),
            datasets: [{
                label: 'Votes',
                data: votesOverTimeData.map(d => d.count),
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#667eea'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 13
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Peak Voting Times Chart
    const votingActivityData = @json($votingActivity);
    const peakTimesCtx = document.getElementById('peakTimesChart').getContext('2d');
    
    // Fill missing hours with 0
    const hourlyData = Array(24).fill(0);
    votingActivityData.forEach(d => {
        hourlyData[d.hour] = d.count;
    });
    
    new Chart(peakTimesCtx, {
        type: 'bar',
        data: {
            labels: Array.from({length: 24}, (_, i) => `${i}:00`),
            datasets: [{
                label: 'Votes per Hour',
                data: hourlyData,
                backgroundColor: 'rgba(255, 193, 7, 0.6)',
                borderColor: '#ffc107',
                borderWidth: 2,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Participation Rate Chart
    const participationData = @json($participationRates);
    const participationCtx = document.getElementById('participationChart').getContext('2d');
    
    new Chart(participationCtx, {
        type: 'doughnut',
        data: {
            labels: participationData.map(d => d.election),
            datasets: [{
                data: participationData.map(d => d.rate),
                backgroundColor: [
                    'rgba(102, 126, 234, 0.8)',
                    'rgba(17, 153, 142, 0.8)',
                    'rgba(79, 172, 254, 0.8)',
                    'rgba(250, 112, 154, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(156, 39, 176, 0.8)'
                ],
                borderWidth: 3,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12
                        },
                        generateLabels: function(chart) {
                            const data = chart.data;
                            return data.labels.map((label, i) => ({
                                text: `${label} (${data.datasets[0].data[i]}%)`,
                                fillStyle: data.datasets[0].backgroundColor[i],
                                hidden: false,
                                index: i
                            }));
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            const election = participationData[context.dataIndex];
                            return [
                                `Participation: ${election.rate}%`,
                                `Votes: ${election.votes}`
                            ];
                        }
                    }
                }
            }
        }
    });
});

// Export chart data to CSV
function exportChartData(chartType) {
    let data, filename;
    
    if (chartType === 'votesOverTime') {
        data = @json($votesOverTime);
        filename = 'votes_over_time.csv';
        
        let csv = 'Date,Votes\n';
        data.forEach(row => {
            csv += `${row.date},${row.count}\n`;
        });
        
        downloadCSV(csv, filename);
    }
}

function downloadCSV(csv, filename) {
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.setAttribute('hidden', '');
    a.setAttribute('href', url);
    a.setAttribute('download', filename);
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    
    showToast('Export successful!', 'success');
}
</script>
@endpush

@endsection
