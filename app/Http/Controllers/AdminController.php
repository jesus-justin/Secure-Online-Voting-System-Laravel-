<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\Candidate;
use App\Models\User;
use App\Models\Vote;
use App\Models\VoteLog;
use App\Services\VotingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function __construct(
        protected VotingService $votingService
    ) {
        $this->middleware('admin');
    }

    public function dashboard()
    {
        $totalElections = Election::count();
        $activeElections = Election::where('status', 'active')->count();
        $totalVotes = Vote::count();
        $totalUsers = User::where('is_admin', false)->count();
        $pendingVerifications = User::where('is_verified', false)->count();

        $recentElections = Election::latest()->take(5)->get();
        $recentVotes = Vote::with(['election', 'candidate', 'user'])
            ->latest()
            ->take(10)
            ->get();

        // Analytics data for charts
        $votesOverTime = Vote::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $votingActivity = Vote::selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Participation rate by election
        $participationRates = Election::withCount('votes')
            ->where('end_time', '<=', now())
            ->get()
            ->map(function ($election) {
                $eligibleVoters = User::where('is_verified', true)->count();
                return [
                    'election' => $election->title,
                    'rate' => $eligibleVoters > 0 ? round(($election->votes_count / $eligibleVoters) * 100, 2) : 0,
                    'votes' => $election->votes_count,
                ];
            });

        // System health metrics
        $systemHealth = [
            'database' => 'healthy',
            'storage' => round((disk_free_space('/') / disk_total_space('/')) * 100, 2),
            'tampered_votes' => Vote::where('is_tampered', true)->count(),
            'failed_verifications' => VoteLog::where('action', 'verification_failed')->count(),
        ];

        // Recent activity feed
        $recentActivity = VoteLog::with('user', 'election')
            ->latest()
            ->take(15)
            ->get();

        return view('admin.dashboard', compact(
            'totalElections',
            'activeElections',
            'totalVotes',
            'totalUsers',
            'pendingVerifications',
            'recentElections',
            'recentVotes',
            'votesOverTime',
            'votingActivity',
            'participationRates',
            'systemHealth',
            'recentActivity'
        ));
    }

    // Elections Management
    public function elections()
    {
        $elections = Election::withCount('votes', 'candidates')->latest()->paginate(10);
        return view('admin.elections.index', compact('elections'));
    }

    public function createElection()
    {
        return view('admin.elections.create');
    }

    public function storeElection(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'max_votes_per_user' => 'required|integer|min:1',
            'allow_anonymous' => 'boolean',
        ]);

        $election = Election::create($request->all());

        return redirect()->route('admin.elections.edit', $election)
            ->with('success', 'Election created successfully!');
    }

    public function editElection(Election $election)
    {
        $candidates = $election->candidates;
        return view('admin.elections.edit', compact('election', 'candidates'));
    }

    public function updateElection(Request $request, Election $election)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'max_votes_per_user' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'allow_anonymous' => 'boolean',
        ]);

        $election->update($request->all());

        return back()->with('success', 'Election updated successfully!');
    }

    public function deleteElection(Election $election)
    {
        $election->delete();
        return redirect()->route('admin.elections')
            ->with('success', 'Election deleted successfully!');
    }

    // Candidates Management
    public function addCandidate(Request $request, Election $election)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'position' => 'nullable|integer',
        ]);

        $data = $request->except('photo');

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('candidates', 'public');
        }

        $election->candidates()->create($data);

        return back()->with('success', 'Candidate added successfully!');
    }

    public function updateCandidate(Request $request, Candidate $candidate)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'position' => 'nullable|integer',
        ]);

        $data = $request->except('photo');

        if ($request->hasFile('photo')) {
            if ($candidate->photo) {
                Storage::disk('public')->delete($candidate->photo);
            }
            $data['photo'] = $request->file('photo')->store('candidates', 'public');
        }

        $candidate->update($data);

        return back()->with('success', 'Candidate updated successfully!');
    }

    public function deleteCandidate(Candidate $candidate)
    {
        if ($candidate->photo) {
            Storage::disk('public')->delete($candidate->photo);
        }

        $candidate->delete();
        return back()->with('success', 'Candidate deleted successfully!');
    }

    // Users Management
    public function users()
    {
        $users = User::where('is_admin', false)->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function verifyUser(User $user)
    {
        $user->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);
        return back()->with('success', 'User verified successfully!');
    }

    public function unverifyUser(User $user)
    {
        $user->update([
            'is_verified' => false,
            'verified_at' => null,
        ]);
        return back()->with('success', 'User verification removed!');
    }

    public function deleteUser(User $user)
    {
        if ($user->is_admin) {
            return back()->with('error', 'Cannot delete admin users!');
        }

        $user->delete();
        return back()->with('success', 'User deleted successfully!');
    }

    // Vote Logs & Security
    public function voteLogs(Election $election = null)
    {
        $query = VoteLog::with(['election', 'user']);

        if ($election) {
            $query->where('election_id', $election->id);
        }

        $logs = $query->latest()->paginate(50);
        $elections = Election::all();

        return view('admin.logs.index', compact('logs', 'elections', 'election'));
    }

    public function detectTampering(Election $election)
    {
        $tamperedCount = $this->votingService->detectTampering($election->id);

        return back()->with('success', "Tampering detection complete. Found {$tamperedCount} tampered votes.");
    }

    public function electionResults(Election $election)
    {
        $results = $election->getResults();
        $totalVotes = $election->total_votes;
        $voteLogs = $election->voteLogs()->orderBy('performed_at', 'desc')->take(20)->get();

        return view('admin.elections.results', compact('election', 'results', 'totalVotes', 'voteLogs'));
    }
}
