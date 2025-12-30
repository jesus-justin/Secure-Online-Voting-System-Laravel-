@extends('layouts.app')

@section('title', 'Create Election')

@section('content')
<div class="container">
    <h2 class="mb-4"><i class="bi bi-plus-circle"></i> Create New Election</h2>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.elections.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="title" class="form-label">Election Title</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                           id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_time" class="form-label">Start Date & Time</label>
                        <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror" 
                               id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                        @error('start_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="end_time" class="form-label">End Date & Time</label>
                        <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror" 
                               id="end_time" name="end_time" value="{{ old('end_time') }}" required>
                        @error('end_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="max_votes_per_user" class="form-label">Max Votes Per User</label>
                    <input type="number" class="form-control @error('max_votes_per_user') is-invalid @enderror" 
                           id="max_votes_per_user" name="max_votes_per_user" value="{{ old('max_votes_per_user', 1) }}" 
                           min="1" required>
                    @error('max_votes_per_user')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="allow_anonymous" 
                           name="allow_anonymous" {{ old('allow_anonymous') ? 'checked' : '' }}>
                    <label class="form-check-label" for="allow_anonymous">
                        Allow Anonymous Voting
                    </label>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('admin.elections') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Election</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
