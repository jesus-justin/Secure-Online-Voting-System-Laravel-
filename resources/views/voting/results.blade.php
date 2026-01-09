@extends('layouts.app')

@section('title', 'Results - ' . $election->title)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
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
                        <div class="d-flex flex-wrap gap-2 results-actions">
                            <button class="btn btn-light btn-sm" onclick="printResults()">
                                <i class="bi bi-printer me-1"></i> Print
                            </button>
                            <button class="btn btn-outline-light btn-sm" onclick="exportToCSV()">
                                <i class="bi bi-file-spreadsheet me-1"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="results-hero">
                        <div class="container py-4 py-md-5">
                            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                                <div>
                                    <p class="text-uppercase small fw-bold text-muted mb-1">Election Results</p>
                                    <h1 class="fw-bold mb-2">{{ $election->title }}</h1>
                                    <div class="d-flex flex-wrap gap-2 align-items-center">
                                        <span class="chip chip-soft">{{ $election->start_date->format('M d, Y') }} - {{ $election->end_date->format('M d, Y') }}</span>
                                        <span class="chip chip-soft">Total Votes: {{ $totalVotes }}</span>
                                        <span class="chip chip-accent">
                                            <i class="bi bi-shield-lock me-1"></i> Verified & Audited
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap gap-2 results-actions sticky-actions">
                                    <button class="btn btn-light btn-sm" onclick="printResults()">
                                        <i class="bi bi-printer me-1"></i> Print
                                    </button>
                                    <button class="btn btn-outline-light btn-sm" onclick="exportToCSV()">
                                        <i class="bi bi-file-spreadsheet me-1"></i> Export CSV
                                    </button>
                                    <button class="btn btn-outline-light btn-sm" onclick="exportToJSON()">
                                        <i class="bi bi-code-square me-1"></i> Export JSON
                                    </button>
                                    <button class="btn btn-outline-light btn-sm" onclick="copyResultsLink()">
                                        <i class="bi bi-link-45deg me-1"></i> Copy Link
                                    </button>
                                    <button class="btn btn-outline-light btn-sm" onclick="refreshResults()">
                                        <i class="bi bi-arrow-repeat me-1"></i> Refresh
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container py-5">
                        <div class="row g-4">
                            <div class="col-lg-4">
                                <div class="glass-card h-100 p-4">
                                    <div class="d-flex align-items-start justify-content-between mb-3">
                                        <div>
                                            <p class="text-uppercase small text-muted mb-1">Overview</p>
                                            <h4 class="mb-0">Election Snapshot</h4>
                                        </div>
                                        <span class="badge bg-success-subtle text-success">Live Results</span>
                                    </div>

                                    <div class="kpi-grid">
                                        <div class="kpi-card">
                                            <p class="kpi-label">Total Votes</p>
                                            <h3 class="kpi-value">{{ $totalVotes }}</h3>
                                            <span class="kpi-sub">Encrypted & verified</span>
                                        </div>
                                        <div class="kpi-card">
                                            <p class="kpi-label">Timeframe</p>
                                            <h6 class="kpi-value">{{ $election->start_date->format('M d, Y') }}</h6>
                                            <span class="kpi-sub">to {{ $election->end_date->format('M d, Y') }}</span>
                                        </div>
                                        <div class="kpi-card">
                                            <p class="kpi-label">Status</p>
                                            <span class="badge bg-primary-subtle text-primary">{{ $election->hasEnded() ? 'Completed' : 'In Progress' }}</span>
                                            <span class="kpi-sub">Integrity checks enabled</span>
                                        </div>
                                    </div>

                                    <div class="segmented-control mt-4" role="group" aria-label="View type">
                                        <button class="segment active" id="segment-bar" data-view="bar" onclick="switchView('bar')">
                                            <i class="bi bi-bar-chart-fill me-1"></i> Bar
                                        </button>
                                        <button class="segment" id="segment-pie" data-view="pie" onclick="switchView('pie')">
                                            <i class="bi bi-pie-chart-fill me-1"></i> Pie
                                        </button>
                                        <button class="segment" id="segment-table" data-view="table" onclick="switchView('table')">
                                            <i class="bi bi-table me-1"></i> Table
                                        </button>
                                    </div>

                                    <div class="mt-4 d-flex align-items-center gap-2 flex-wrap">
                                        <span class="legend-dot" style="background: rgba(255, 193, 7, 0.9);"></span> Lead
                                        <span class="legend-dot" style="background: rgba(108, 117, 125, 0.9);"></span> Runner-up
                                        <span class="legend-dot" style="background: rgba(23, 162, 184, 0.9);"></span> Third
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-8">
                                <div class="card shadow-lg border-0">
                                    <div class="card-body p-4 p-md-5">
                                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                                            <div>
                                                <h5 class="mb-1">Results Overview</h5>
                                                <p class="text-muted small mb-0">Search candidates or switch between chart views.</p>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-center gap-2">
                                                <div class="input-group" style="max-width: 320px;">
                                                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                                                    <input type="text" id="candidateSearch" class="form-control" placeholder="Search candidates..." aria-label="Search candidates">
                                                </div>
                                                <div class="btn-group" role="group" aria-label="Sort candidates">
                                                    <button class="btn btn-outline-secondary active" id="sortByVotes" type="button">
                                                        <i class="bi bi-sort-numeric-down"></i>
                                                    </button>
                                                    <button class="btn btn-outline-secondary" id="sortByName" type="button">
                                                        <i class="bi bi-sort-alpha-down"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        @if($totalVotes === 0)
                                            <div id="noVotesAlert" class="alert alert-warning d-flex align-items-center gap-2 mb-4" role="status">
                                                <i class="bi bi-info-circle-fill"></i>
                                                <div>No votes have been recorded yet. Charts will appear after the first vote.</div>
                                            </div>
                                        @endif

                                        <div class="chart-container mb-4" id="barChartView">
                                            <canvas id="resultsBarChart"></canvas>
                                        </div>

                                        <div class="chart-container mb-4" id="pieChartView" style="display: none;">
                                            <canvas id="resultsPieChart"></canvas>
                                        </div>

                                        <div id="tableView" style="display: none;">
                                            <div class="table-responsive rounded-4 overflow-hidden shadow-sm">
                                                <table class="table align-middle mb-0">
                                                    <thead class="table-light">
                                                        <tr class="text-uppercase small text-muted">
                                                            <th>Rank</th>
                                                            <th>Candidate</th>
                                                            <th class="text-end">Votes</th>
                                                            <th class="text-end">Percentage</th>
                                                            <th style="width: 200px;">Visual</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($results as $index => $candidate)
                                                            <tr data-name="{{ $candidate->name }}" data-votes="{{ $candidate->votes_count }}">
                                                                <td>
                                                                    @if($index === 0)
                                                                        <i class="bi bi-trophy-fill text-warning" style="font-size: 1.25rem;"></i>
                                                                    @else
                                                                        <span class="badge bg-secondary-subtle text-secondary">{{ $index + 1 }}</span>
                                                                    @endif
                                                                </td>
                                                                <td class="fw-semibold">{{ $candidate->name }}</td>
                                                                <td class="text-end">{{ $candidate->votes_count }}</td>
                                                                <td class="text-end fw-bold">
                                                                    {{ $totalVotes > 0 ? round(($candidate->votes_count / $totalVotes * 100), 2) : 0 }}%
                                                                </td>
                                                                <td>
                                                                    <div class="progress progress-compact">
                                                                        <div class="progress-bar" 
                                                                             role="progressbar"
                                                                             style="width: {{ $totalVotes > 0 ? ($candidate->votes_count / $totalVotes * 100) : 0 }}%"></div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="row g-3 g-md-4" id="candidateCards">
                                            @foreach($results as $index => $candidate)
                                                <div class="col-md-6 candidate-card-item" data-votes="{{ $candidate->votes_count }}" data-name="{{ $candidate->name }}">
                                                    <div class="card border-0 shadow-sm h-100 ribbon-card" data-rank="{{ $index + 1 }}">
                                                        <div class="card-body p-4">
                                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <span class="rank-pill">{{ $index + 1 }}</span>
                                                                    <div>
                                                                        <h5 class="mb-0 fw-bold">{{ $candidate->name }}</h5>
                                                                        @if($candidate->description)
                                                                            <p class="text-muted small mb-0">{{ $candidate->description }}</p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="text-end">
                                                                    <h4 class="mb-0 text-primary vote-count" data-target="{{ $candidate->votes_count }}">0</h4>
                                                                    <small class="text-muted">votes</small>
                                                                </div>
                                                            </div>

                                                            <div class="progress gradient-progress mb-2">
                                                                <div class="progress-bar animated-bar" 
                                                                     role="progressbar" 
                                                                     data-width="{{ $totalVotes > 0 ? ($candidate->votes_count / $totalVotes * 100) : 0 }}"
                                                                     style="width: 0%"
                                                                     aria-valuenow="{{ $candidate->votes_count }}" 
                                                                     aria-valuemin="0" 
                                                                     aria-valuemax="{{ $totalVotes }}">
                                                                    <span class="percentage-text" data-percentage="{{ $totalVotes > 0 ? round(($candidate->votes_count / $totalVotes * 100), 2) : 0 }}">0%</span>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex justify-content-between small text-muted">
                                                                <span>Share of vote</span>
                                                                <span>{{ $totalVotes > 0 ? round(($candidate->votes_count / $totalVotes * 100), 2) : 0 }}%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div id="noResultsNotice" class="alert alert-info d-none mt-3" role="status">
                                            No candidates match your search.
                                        </div>

                                        <div class="text-center mt-4">
                                            <a href="{{ route('voting.index') }}" class="btn btn-outline-secondary">
                                                <i class="bi bi-arrow-left"></i> Back to Elections
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

                @push('styles')
                <style>
                    .segment:focus-visible {
                        outline: 2px solid #2563eb;
                        outline-offset: 2px;
                    }

                    @media print {
                        .results-actions,
                        .segmented-control,
                        #candidateSearch,
                        .btn-group[aria-label="Sort candidates"],
                        #noVotesAlert {
                            display: none !important;
                        }

                        .card,
                        .glass-card {
                            box-shadow: none !important;
                            border: 1px solid #dee2e6 !important;
                        }

                        .table-responsive {
                            box-shadow: none !important;
                        }
                    }

                    .sticky-actions {
                        position: sticky;
                        top: 1rem;
                        z-index: 1020;
                    }

                    @media (max-width: 992px) {
                        .sticky-actions {
                            position: static;
                        }
                    }
                </style>
                @endpush

@push('scripts')
<script>
const candidateNames = @json($results->pluck('name'));
const candidateVotes = @json($results->pluck('votes_count'));
const totalVotes = {{ $totalVotes }};
const electionId = {{ $election->id }};
const hasVotes = totalVotes > 0;

const colors = [
    'rgba(255, 193, 7, 0.8)',
    'rgba(108, 117, 125, 0.8)',
    'rgba(23, 162, 184, 0.8)',
    'rgba(102, 126, 234, 0.8)',
    'rgba(118, 75, 162, 0.8)',
    'rgba(255, 99, 132, 0.8)',
    'rgba(54, 162, 235, 0.8)',
    'rgba(255, 159, 64, 0.8)'
];

const borderColors = colors.map(color => color.replace('0.8', '1'));
let barChart;
let pieChart;

const barCanvas = document.getElementById('resultsBarChart');
const pieCanvas = document.getElementById('resultsPieChart');

if (hasVotes && barCanvas && pieCanvas) {
    const barCtx = barCanvas.getContext('2d');
    barChart = new Chart(barCtx, {
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

    const pieCtx = pieCanvas.getContext('2d');
    pieChart = new Chart(pieCtx, {
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
} else {
    document.getElementById('barChartView').style.display = 'none';
    document.getElementById('pieChartView').style.display = 'none';
}

function filterCandidates(term) {
    const normalized = term.trim().toLowerCase();
    const cardItems = document.querySelectorAll('.candidate-card-item');
    const tableRows = document.querySelectorAll('#tableView tbody tr');
    let visibleCount = 0;

    cardItems.forEach(card => {
        const matches = card.dataset.name.toLowerCase().includes(normalized);
        card.style.display = matches ? '' : 'none';
        if (matches) visibleCount++;
    });

    tableRows.forEach(row => {
        const matches = row.dataset.name.toLowerCase().includes(normalized);
        row.style.display = matches ? '' : 'none';
    });

    const notice = document.getElementById('noResultsNotice');
    if (notice) {
        notice.classList.toggle('d-none', visibleCount > 0);
    }
}

function sortCandidates(criteria) {
    const cardsContainer = document.getElementById('candidateCards');
    const tableBody = document.querySelector('#tableView tbody');

    if (!cardsContainer || !tableBody) return;

    const cardItems = Array.from(cardsContainer.children);
    const tableRows = Array.from(tableBody.querySelectorAll('tr'));

    const comparator = (a, b) => {
        if (criteria === 'name') {
            return a.dataset.name.toLowerCase().localeCompare(b.dataset.name.toLowerCase());
        }
        return Number(b.dataset.votes) - Number(a.dataset.votes);
    };

    cardItems.sort(comparator).forEach(item => cardsContainer.appendChild(item));
    tableRows.sort(comparator).forEach(row => tableBody.appendChild(row));
}

function switchView(viewType) {
    document.getElementById('barChartView').style.display = 'none';
    document.getElementById('pieChartView').style.display = 'none';
    document.getElementById('tableView').style.display = 'none';
    document.getElementById('candidateCards').style.display = 'none';
    document.querySelectorAll('.segment').forEach(btn => btn.classList.remove('active'));

    if (viewType === 'bar') {
        if (hasVotes) {
            document.getElementById('barChartView').style.display = 'block';
            barChart?.update();
        }
        document.getElementById('candidateCards').style.display = 'flex';
        document.getElementById('segment-bar').classList.add('active');
    } else if (viewType === 'pie') {
        if (hasVotes) {
            document.getElementById('pieChartView').style.display = 'block';
            pieChart?.update();
        }
        document.getElementById('segment-pie').classList.add('active');
    } else if (viewType === 'table') {
        document.getElementById('tableView').style.display = 'block';
        document.getElementById('segment-table').classList.add('active');
    }
}

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

window.addEventListener('load', () => {
    document.querySelectorAll('.vote-count').forEach(el => {
        const target = parseInt(el.dataset.target);
        animateValue(el, 0, target, 2000);
    });

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

const searchInput = document.getElementById('candidateSearch');
if (searchInput) {
    searchInput.addEventListener('input', (event) => {
        filterCandidates(event.target.value);
    });
}

const sortByVotes = document.getElementById('sortByVotes');
const sortByName = document.getElementById('sortByName');
const sortButtons = [sortByVotes, sortByName];

function setSortActive(button) {
    sortButtons.forEach(btn => btn?.classList.remove('active'));
    button?.classList.add('active');
}

if (sortByVotes && sortByName) {
    sortByVotes.addEventListener('click', () => {
        sortCandidates('votes');
        setSortActive(sortByVotes);
    });

    sortByName.addEventListener('click', () => {
        sortCandidates('name');
        setSortActive(sortByName);
    });
}

const viewSegments = Array.from(document.querySelectorAll('.segment'));
const segmentBar = document.getElementById('segment-bar');
const segmentPie = document.getElementById('segment-pie');
viewSegments.forEach((segment, index) => {
    segment.addEventListener('keydown', (event) => {
        const isArrowRight = event.key === 'ArrowRight';
        const isArrowLeft = event.key === 'ArrowLeft';
        const isActivate = event.key === 'Enter' || event.key === ' ';

        if (isArrowRight || isArrowLeft) {
            event.preventDefault();
            const nextIndex = isArrowRight
                ? (index + 1) % viewSegments.length
                : (index - 1 + viewSegments.length) % viewSegments.length;
            viewSegments[nextIndex].focus();
            viewSegments[nextIndex].click();
        }

        if (isActivate) {
            event.preventDefault();
            segment.click();
        }
    });
});

if (!hasVotes) {
    segmentBar?.setAttribute('disabled', 'disabled');
    segmentPie?.setAttribute('disabled', 'disabled');
    document.getElementById('barChartView')?.classList.add('d-none');
    document.getElementById('pieChartView')?.classList.add('d-none');
}

function exportToPDF() {
    showToast('PDF export feature coming soon!', 'info');
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
    a.download = `election_results_${electionId}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
    showToast('Results exported successfully!', 'success');
}

function exportToJSON() {
    const payload = candidateNames.map((name, index) => ({
        name,
        votes: candidateVotes[index],
        percentage: totalVotes > 0 ? ((candidateVotes[index] / totalVotes) * 100).toFixed(2) : 0
    }));

    const blob = new Blob([JSON.stringify(payload, null, 2)], { type: 'application/json' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `election_results_${electionId}.json`;
    a.click();
    window.URL.revokeObjectURL(url);
    showToast('JSON exported successfully!', 'success');
}

function copyResultsLink() {
    const link = window.location.href;
    navigator.clipboard.writeText(link)
        .then(() => showToast('Results link copied to clipboard', 'success'))
        .catch(() => showToast('Unable to copy link', 'error'));
}

function refreshResults() {
    window.location.reload();
}

function printResults() {
    window.print();
}
</script>
@endpush
