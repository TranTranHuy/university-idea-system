@extends('layouts.master')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white pt-4 pb-3 border-bottom">
                    <h4 class="mb-0 text-primary fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Idea</h4>
                </div>
                
                <div class="card-body p-4 bg-light rounded-bottom">
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('ideas.update', $idea->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT') <div class="mb-4">
                            <label for="title" class="form-label fw-semibold">Idea Title <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" 
                                   value="{{ old('title', $idea->title) }}" 
                                   placeholder="Enter an engaging title">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="category_id" class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                                <option value="" disabled>Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $idea->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="content" class="form-label fw-semibold">Idea Details <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" name="content" rows="6" 
                                      placeholder="Describe your idea in detail...">{{ old('content', $idea->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="document" class="form-label fw-semibold"><i class="bi bi-paperclip me-1"></i>Attachment (Optional)</label>
                            <input type="file" class="form-control @error('document') is-invalid @enderror" id="document" name="document">
                            
                            @if($idea->document)
                                @php
                                    // Bóc tách dữ liệu vì trong DB đang lưu dạng mảng hoặc JSON
                                    $oldFiles = is_array($idea->document) ? $idea->document : json_decode($idea->document, true);
                                @endphp

                                @if(!empty($oldFiles))
                                    <div class="alert alert-info mt-3 mb-0 py-2">
                                        <div class="fw-semibold mb-1"><i class="bi bi-info-circle-fill me-1"></i>Current attachment(s):</div>
                                        <ul class="mb-1">
                                            @foreach($oldFiles as $file)
                                                <li>
                                                    <a href="{{ asset('storage/' . $file) }}" target="_blank" class="text-decoration-none fw-bold text-primary">
                                                        <i class="bi bi-file-earmark-text me-1"></i>{{ basename($file) }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                        <small class="text-muted"><i class="bi bi-exclamation-triangle me-1"></i>Uploading a new file will replace the current one(s).</small>
                                    </div>
                                @endif
                            @endif
                            
                            @error('document')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('staff.profile') }}" class="btn btn-outline-secondary px-4">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-warning px-4 text-dark fw-bold shadow-sm">
                                <i class="bi bi-save me-2"></i>Update Idea
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection