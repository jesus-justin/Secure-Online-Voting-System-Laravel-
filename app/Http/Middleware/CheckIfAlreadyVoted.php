<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIfAlreadyVoted
{
    public function handle(Request $request, Closure $next): Response
    {
        $election = $request->route('election');
        $user = auth()->user();

        if ($user->hasVotedInElection($election->id)) {
            return redirect()->route('voting.success', $election)
                ->with('info', 'You have already voted in this election');
        }

        return $next($request);
    }
}
