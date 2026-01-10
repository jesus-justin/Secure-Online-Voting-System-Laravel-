<?php

namespace App\Services;

use App\Models\Election;

class ElectionCloningService
{
    /**
     * Clone an election with all its settings and candidates
     */
    public function clone(Election $source): Election
    {
        $clone = $source->replicate();
        $clone->title = $source->title . ' (Copy)';
        $clone->start_date = $source->start_date?->addDays(7);
        $clone->end_date = $source->end_date?->addDays(7);
        $clone->save();

        // Clone candidates
        $source->candidates->each(function ($candidate) use ($clone) {
            $newCandidate = $candidate->replicate();
            $newCandidate->election_id = $clone->id;
            $newCandidate->save();

            // Copy avatar if exists
            if ($candidate->avatar_path) {
                $this->copyAvatar($candidate, $newCandidate);
            }
        });

        return $clone;
    }

    /**
     * Clone with custom settings
     */
    public function cloneWithSettings(
        Election $source,
        array $settings = []
    ): Election {
        $clone = $source->replicate();
        
        // Apply custom settings
        if (isset($settings['title'])) {
            $clone->title = $settings['title'];
        } else {
            $clone->title = $source->title . ' (Copy)';
        }

        if (isset($settings['start_date'])) {
            $clone->start_date = $settings['start_date'];
        }

        if (isset($settings['end_date'])) {
            $clone->end_date = $settings['end_date'];
        }

        $clone->save();

        // Clone candidates
        $source->candidates->each(function ($candidate) use ($clone) {
            $newCandidate = $candidate->replicate();
            $newCandidate->election_id = $clone->id;
            $newCandidate->save();

            if ($candidate->avatar_path) {
                $this->copyAvatar($candidate, $newCandidate);
            }
        });

        return $clone;
    }

    /**
     * Copy avatar from source to cloned candidate
     */
    private function copyAvatar($source, $target): void
    {
        if (!$source->avatar_path) {
            return;
        }

        $sourceFile = storage_path("app/public/{$source->avatar_path}");
        if (file_exists($sourceFile)) {
            $filename = basename($source->avatar_path);
            $targetPath = "candidates/{$target->election_id}/{$filename}";
            
            copy($sourceFile, storage_path("app/public/{$targetPath}"));
            $target->update(['avatar_path' => $targetPath]);
        }
    }
}
