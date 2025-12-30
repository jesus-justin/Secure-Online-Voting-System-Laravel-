@extends('layouts.app')

@section('title', 'Edit Election')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4"><i class="bi bi-pencil-square"></i> Edit Election: {{ $election->title }}</h2>

    <div class="row">
        <!-- Election Details -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Election Details</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.elections.update', $election) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $election->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" required>{{ old('description', $election->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="start_time" class="form-label">Start Time</label>
                            <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror" 
                                   id="start_time" name="start_time" 
                                   value="{{ old('start_time', $election->start_time->format('Y-m-d\TH:i')) }}" required>
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="end_time" class="form-label">End Time</label>
                            <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror" 
                                   id="end_time" name="end_time" 
                                   value="{{ old('end_time', $election->end_time->format('Y-m-d\TH:i')) }}" required>
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check form-switch">
                            <input type="checkbox" class="form-check-input" id="is_active" 
                                   name="is_active" {{ old('is_active', $election->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Election</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Candidates -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Candidates</h5>
                </div>
                <div class="card-body">
                    <!-- Add Candidate Form -->
                    <form method="POST" action="{{ route('admin.candidates.store', $election) }}" enctype="multipart/form-data" class="mb-4">
                        @csrf
                        <h6>Add New Candidate</h6>
                        <div class="mb-2">
                            <input type="text" class="form-control form-control-sm" 
                                   name="name" placeholder="Candidate Name" required>
                        </div>
                        <div class="mb-2">
                            <textarea class="form-control form-control-sm" 
                                      name="description" placeholder="Description" rows="2"></textarea>
                        </div>
                        <div class="mb-2">
                            <input type="file" class="form-control form-control-sm" 
                                   name="photo" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="bi bi-plus"></i> Add Candidate
                        </button>
                    </form>

                    <hr>

                    <!-- Candidates List -->
                    <div class="list-group">
                        @foreach($candidates as $candidate)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $candidate->name }}</h6>
                                        <p class="mb-0 small text-muted">{{ $candidate->description }}</p>
                                    </div>
                                    <form action="{{ route('admin.candidates.delete', $candidate) }}" 
                                          method="POST"
                                          onsubmit="return confirm('Delete this candidate?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
