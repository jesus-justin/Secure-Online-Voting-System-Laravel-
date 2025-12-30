<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckElectionActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $election = $request->route('election');

        if (!$election->isActive()) {
            return redirect()->route('voting.index')
                ->with('error', 'This election is not currently active');
        }

        return $next($request);
    }
}
