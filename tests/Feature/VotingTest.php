<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Election;
use App\Models\Candidate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VotingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_active_elections()
    {
        $user = User::factory()->create(['is_verified' => true]);
        $election = Election::factory()->create(['is_active' => true]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee($election->title);
    }

    public function test_user_can_view_election_details()
    {
        $user = User::factory()->create(['is_verified' => true]);
        $election = Election::factory()->create([
            'is_active' => true,
            'start_time' => now()->subHour(),
            'end_time' => now()->addHour(),
        ]);

        $response = $this->actingAs($user)->get("/elections/{$election->id}");

        $response->assertStatus(200);
        $response->assertSee($election->title);
    }

    public function test_verified_user_can_vote()
    {
        $user = User::factory()->create(['is_verified' => true]);
        $election = Election::factory()->create([
            'is_active' => true,
            'start_time' => now()->subHour(),
            'end_time' => now()->addHour(),
        ]);
        $candidate = Candidate::factory()->create(['election_id' => $election->id]);

        $response = $this->actingAs($user)->post("/elections/{$election->id}/vote", [
            'candidate_id' => $candidate->id,
            'recaptcha_token' => 'test_token',
        ]);

        $this->assertTrue($user->hasVotedInElection($election->id));
    }

    public function test_user_cannot_vote_twice()
    {
        $user = User::factory()->create(['is_verified' => true]);
        $election = Election::factory()->create([
            'is_active' => true,
            'start_time' => now()->subHour(),
            'end_time' => now()->addHour(),
        ]);
        $candidate = Candidate::factory()->create(['election_id' => $election->id]);

        // First vote
        $this->actingAs($user)->post("/elections/{$election->id}/vote", [
            'candidate_id' => $candidate->id,
            'recaptcha_token' => 'test_token',
        ]);

        // Second vote attempt
        $response = $this->actingAs($user)->post("/elections/{$election->id}/vote", [
            'candidate_id' => $candidate->id,
            'recaptcha_token' => 'test_token',
        ]);

        $response->assertRedirect();
        $this->assertEquals(1, $user->votes()->where('election_id', $election->id)->count());
    }

    public function test_unverified_user_cannot_vote()
    {
        $user = User::factory()->create(['is_verified' => false]);
        $election = Election::factory()->create([
            'is_active' => true,
            'start_time' => now()->subHour(),
            'end_time' => now()->addHour(),
        ]);
        $candidate = Candidate::factory()->create(['election_id' => $election->id]);

        $response = $this->actingAs($user)->post("/elections/{$election->id}/vote", [
            'candidate_id' => $candidate->id,
            'recaptcha_token' => 'test_token',
        ]);

        $response->assertRedirect();
        $this->assertFalse($user->hasVotedInElection($election->id));
    }

    public function test_admin_can_access_dashboard()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_access_dashboard()
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertStatus(403);
    }
}
