@extends('layouts.app')

@section('title', 'Results - ' . $election->title)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-lg border-0">
                <div class="card-header text-white py-4" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        <div class="d-flex align-items-center mb-2 mb-md-0">
                            <i class="bi bi-bar-chart-fill me-3" style="font-size: 2.5rem;" aria-hidden="true"></i>
                            <div>
                                <h3 class="mb-0">Election Results</h3>
                                <p class="mb-0 opacity-75">{{ $election->title }}</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <span class="badge bg-white text-dark px-3 py-2">
                                <i class="bi bi-people-fill" aria-hidden="true"></i> {{ $totalVotes }} Total Votes
                            </span>
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm dropdown-toggle" type="button" 
                                        id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                                        aria-label="Export results">
                                    <i class="bi bi-download"></i> Export
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                    <li><a class="dropdown-item" href="#" onclick="exportToPDF(); return false;">
                                        <i class="bi bi-file-pdf"></i> Export as PDF
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="exportToCSV(); return false;">
                                        <i class="bi bi-file-spreadsheet"></i> Export as CSV
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="printResults(); return false;">
                                        <i class="bi bi-printer"></i> Print Results
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4 p-md-5">
                    <!-- Results Info Header -->
                    <div class="alert alert-info border-0 shadow-sm mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <i class="bi bi-info-circle-fill" aria-hidden="true"></i> 
                                <strong>Total Votes Cast:</strong> {{ $totalVotes }}
                            </div>
                            <div class="col-md-6 text-md-end mt-2 mt-md-0">
                                <small>
                                    <i class="bi bi-calendar-range" aria-hidden="true"></i> 
                                    {{ $election->start_date->format('M d, Y H:i') }} - {{ $election->end_date->format('M d, Y H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Visualization Toggle -->
                    <div class="d-flex justify-content-center mb-4">
                        <div class="btn-group" role="group" aria-label="View type">
                            <input type="radio" class="btn-check" name="viewType" id="barView" autocomplete="off" checked>
                            <label class="btn btn-outline-primary" for="barView" onclick="showView('bar')">
                                <i class="bi bi-bar-chart-fill"></i> Bar View
                            </label>
                            
                            <input type="radio" class="btn-check" name="viewType" id="pieView" autocomplete="off">
                            <label class="btn btn-outline-primary" for="pieView" onclick="showView('pie')">
                                <i class="bi bi-pie-chart-fill"></i> Pie Chart
                            </label>
                            
                            <input type="radio" class="btn-check" name="viewType" id="tableView" autocomplete="off">
                            <label class="btn btn-outline-primary" for="tableView" onclick="showView('table')">
                                <i class="bi bi-table"></i> Table View
                            </label>
                        </div>
                    </div>
                    
                    <!-- Pie Chart View -->
                    <div id="pieChartView" class="mb-5" style="display: none;">
                        <div class="row">
                            <div class="col-lg-8 mx-auto">
                                <canvas id="resultsPieChart" style="max-height: 400px;"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bar Chart View -->
                    <div id="barChartView" class="mb-5">
                        <canvas id="resultsBarChart" style="max-height: 350px;"></canvas>
                    </div>
                    
                    <!-- Table View -->
                    <div id="tableView" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Rank</th>
                                        <th>Candidate</th>
                                        <th>Votes</th>
                                        <th>Percentage</th>
                                        <th>Visual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results as $index => $candidate)
                                        <tr>
                                            <td>
                                                @if($index === 0)
                                                    <i class="bi bi-trophy-fill text-warning" style="font-size: 1.5rem;"></i>
                                                @else
                                                    <span class="badge bg-secondary">{{ $index + 1 }}</span>
                                                @endif
                                            </td>
                                            <td><strong>{{ $candidate->name }}</strong></td>
                                            <td>{{ $candidate->votes_count }}</td>
                                            <td>
                                                <strong>{{ $totalVotes > 0 ? round(($candidate->votes_count / $totalVotes * 100), 2) : 0 }}%</strong>
                                            </td>
                                            <td>
                                                <div class="progress" style="width: 200px; height: 20px;">
                                                    <div class="progress-bar bg-primary" 
                                                         style="width: {{ $totalVotes > 0 ? ($candidate->votes_count / $totalVotes * 100) : 0 }}%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Candidate Cards View -->
                    <div class="row g-4" id="candidateCards">
                        @foreach($results as $index => $candidate)
                            <div class="col-md-6 candidate-card-item" data-votes="{{ $candidate->votes_count }}">
                                <div class="card shadow-sm border-0 h-100" 
                                     style="border-left: 4px solid {{ $index === 0 ? '#ffc107' : '#6c757d' }} !important;
                                            animation: fadeInUp 0.5s ease-out {{ $index * 0.1 }}s backwards;">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="mb-0 fw-bold">
                                                @if($index === 0)
                                                    <i class="bi bi-trophy-fill text-warning me-2" style="font-size: 1.5rem;" aria-hidden="true"></i>
                                                @elseif($index === 1)
                                                    <i class="bi bi-award-fill text-secondary me-2" aria-hidden="true"></i>
                                                @elseif($index === 2)
                                                    <i class="bi bi-award text-info me-2" aria-hidden="true"></i>
                                                @else
                                                    <span class="badge bg-light text-dark me-2">{{ $index + 1 }}</span>
                                                @endif
                                                {{ $candidate->name }}
                                            </h5>
                                            <div class="text-end">
                                                <h4 class="mb-0 text-primary vote-count" data-target="{{ $candidate->votes_count }}">0</h4>
                                                <small class="text-muted">votes</small>
                                            </div>
                                        </div>

                                        <div class="progress mb-3 shadow-sm" style="height: 35px; border-radius: 10px;">
                                            <div class="progress-bar {{ $index === 0 ? 'bg-warning' : 'bg-primary' }} bg-gradient animated-bar" 
                                                 role="progressbar" 
                                                 data-width="{{ $totalVotes > 0 ? ($candidate->votes_count / $totalVotes * 100) : 0 }}"
                                                 style="width: 0%"
                                                 aria-valuenow="{{ $candidate->votes_count }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="{{ $totalVotes }}"
                                                 aria-label="{{ $candidate->name }} received {{ $totalVotes > 0 ? round(($candidate->votes_count / $totalVotes * 100), 2) : 0 }}% of votes">
                                                <strong class="percentage-text" style="font-size: 1.1rem;" data-percentage="{{ $totalVotes > 0 ? round(($candidate->votes_count / $totalVotes * 100), 2) : 0 }}">0%</strong>
                                            </div>
                                        </div>

                                        @if($candidate->description)
                                            <p class="text-muted small mb-0">
                                                <i class="bi bi-info-circle" aria-hidden="true"></i> {{ $candidate->description }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('voting.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left" aria-hidden="true"></i> Back to Elections
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart Data
const candidateNames = {!! json_encode($results->pluck('name')) !!};
const candidateVotes = {!! json_encode($results->pluck('votes_count')) !!};
const totalVotes = {{ $totalVotes }};

// Color palette
const colors = [
    'rgba(255, 193, 7, 0.8)',  // Gold for 1st
    'rgba(108, 117, 125, 0.8)', // Silver for 2nd
    'rgba(23, 162, 184, 0.8)',  // Bronze for 3rd
    'rgba(102, 126, 234, 0.8)',
    'rgba(118, 75, 162, 0.8)',
    'rgba(255, 99, 132, 0.8)',
    'rgba(54, 162, 235, 0.8)',
    'rgba(255, 159, 64, 0.8)'
];

const borderColors = colors.map(color => color.replace('0.8', '1'));

// Bar Chart
const barCtx = document.getElementById('resultsBarChart').getContext('2d');
const barChart = new Chart(barCtx, {
    type: 'bar',
    data: {
        labels: candidateNames,
        datasets: [{
            label: 'Votes',
            data: candidateVotes,
            backgroundColor: colors,
            borderColor: borderColors,
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const votes = context.parsed.y;
                        const percentage = totalVotes > 0 ? ((votes / totalVotes) * 100).toFixed(2) : 0;
                        return `Votes: ${votes} (${percentage}%)`;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                },
                title: {
                    display: true,
                    text: 'Number of Votes'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Candidates'
                }
            }
        },
        animation: {
            duration: 2000,
            easing: 'easeOutQuart'
        }
    }
});

// Pie Chart
const pieCtx = document.getElementById('resultsPieChart').getContext('2d');
const pieChart = new Chart(pieCtx, {
    type: 'doughnut',
    data: {
        labels: candidateNames,
        datasets: [{
            data: candidateVotes,
            backgroundColor: colors,
            borderColor: borderColors,
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'right',
                labels: {
                    padding: 15,
                    font: {
                        size: 12
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const votes = context.parsed;
                        const percentage = totalVotes > 0 ? ((votes / totalVotes) * 100).toFixed(2) : 0;
                        return `${context.label}: ${votes} votes (${percentage}%)`;
                    }
                }
            }
        },
        animation: {
            animateRotate: true,
            animateScale: true,
            duration: 2000
        }
    }
});

// View switcher
function showView(viewType) {
    document.getElementById('barChartView').style.display = 'none';
    document.getElementById('pieChartView').style.display = 'none';
    document.getElementById('tableView').style.display = 'none';
    document.getElementById('candidateCards').style.display = 'none';
    
    if (viewType === 'bar') {
        document.getElementById('barChartView').style.display = 'block';
        document.getElementById('candidateCards').style.display = 'flex';
        barChart.update();
    } else if (viewType === 'pie') {
        document.getElementById('pieChartView').style.display = 'block';
        pieChart.update();
    } else if (viewType === 'table') {
        document.getElementById('tableView').style.display = 'block';
    }
}

// Animate vote counts
function animateValue(element, start, end, duration) {
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
            current = end;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current);
    }, 16);
}

// Animate progress bars and counts
window.addEventListener('load', () => {
    // Animate vote counts
    document.querySelectorAll('.vote-count').forEach(el => {
        const target = parseInt(el.dataset.target);
        animateValue(el, 0, target, 2000);
    });
    
    // Animate progress bars
    setTimeout(() => {
        document.querySelectorAll('.animated-bar').forEach(bar => {
            const width = bar.dataset.width;
            bar.style.width = width + '%';
            
            const percentText = bar.querySelector('.percentage-text');
            if (percentText) {
                const targetPercent = parseFloat(percentText.dataset.percentage);
                let current = 0;
                const increment = targetPercent / 100;
                const interval = setInterval(() => {
                    current += increment;
                    if (current >= targetPercent) {
                        current = targetPercent;
                        clearInterval(interval);
                    }
                    percentText.textContent = current.toFixed(2) + '%';
                }, 20);
            }
        });
    }, 500);
});

// Export functions
function exportToPDF() {
    showToast('PDF export feature coming soon!', 'info');
    // Would require a library like jsPDF
}

function exportToCSV() {
    const csv = 'Candidate,Votes,Percentage\n' + 
        candidateNames.map((name, i) => {
            const votes = candidateVotes[i];
            const percentage = totalVotes > 0 ? ((votes / totalVotes) * 100).toFixed(2) : 0;
            return `"${name}",${votes},${percentage}%`;
        }).join('\n');
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'election_results_{{ $election->id }}.csv';
    a.click();
    window.URL.revokeObjectURL(url);
    showToast('Results exported successfully!', 'success');
}

function printResults() {
    window.print();
}
</script>
@endpush
