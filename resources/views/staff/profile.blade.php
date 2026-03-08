@extends('layouts.master') 

@section('content')
<div class="container py-5">
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li><i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body text-center py-5">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->full_name) }}&background=0d6efd&color=fff&size=120" 
                         alt="User Avatar" class="rounded-circle mb-3 shadow">
                    <h4 class="mb-1 fw-bold">{{ $user->full_name }}</h4>
                    <p class="text-muted mb-0"><i class="bi bi-envelope-fill me-2 text-primary"></i>{{ $user->email }}</p>
                    <div class="mt-4">
                        <span class="badge bg-primary px-4 py-2 rounded-pill fs-6 fw-normal">Staff Member</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white pt-4 pb-0 border-0">
                    <ul class="nav nav-tabs border-bottom-0" id="profileTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-semibold text-dark pb-3" data-bs-toggle="tab" data-bs-target="#info">
                                <i class="bi bi-person-lines-fill me-2 text-primary"></i>Personal Info
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-semibold text-dark pb-3" data-bs-toggle="tab" data-bs-target="#ideas">
                                <i class="bi bi-lightbulb-fill me-2 text-warning"></i>My Ideas <span class="badge bg-secondary ms-1">{{ $myIdeas->total() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-semibold text-dark pb-3" data-bs-toggle="tab" data-bs-target="#activities">
                                <i class="bi bi-activity me-2 text-success"></i>Recent Activities
                            </button>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body p-4 bg-light rounded-bottom">
                    <div class="tab-content" id="profileTabContent">
                        
                        <div class="tab-pane fade show active" id="info">
                            <h5 class="mb-4 text-primary fw-bold">Update Profile</h5>
                            <form action="{{ route('staff.profile.update') }}" method="POST" class="bg-white p-4 rounded shadow-sm mb-4">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Full Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ $user->full_name }}">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control text-muted" value="{{ $user->email }}" disabled>
                                    <div class="form-text">Email address cannot be changed.</div>
                                </div>
                                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i>Save Changes</button>
                            </form>

                            <h5 class="mb-4 text-warning fw-bold">Change Password</h5>
                            <form action="{{ route('staff.profile.password') }}" method="POST" class="bg-white p-4 rounded shadow-sm">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Current Password</label>
                                    <input type="password" class="form-control" name="current_password" placeholder="Enter current password">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">New Password</label>
                                        <input type="password" class="form-control" name="new_password" placeholder="Enter new password">
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-semibold">Confirm New Password</label>
                                        <input type="password" class="form-control" name="new_password_confirmation" placeholder="Confirm new password">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-warning px-4 text-dark"><i class="bi bi-shield-lock me-2"></i>Update Password</button>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="ideas">
                            <div class="bg-white p-4 rounded shadow-sm">
                                <h5 class="mb-4 text-primary fw-bold">Submitted Ideas</h5>
                                <div class="list-group list-group-flush mb-3">
                                    @forelse($myIdeas as $idea)
                                        <div class="list-group-item py-3 px-0 border-bottom hover-bg-light transition-all">
                                            <div class="d-flex w-100 justify-content-between align-items-center">
                                                
                                                <div>
                                                    <h6 class="mb-1 text-dark fw-bold">{{ $idea->title }}</h6>
                                                    <small class="text-muted"><i class="bi bi-calendar-event me-1"></i>{{ $idea->created_at->format('M d, Y') }}</small>
                                                    
                                                    {{-- Lưu ý: Bạn cần sửa $idea->academicYear->closure_date cho đúng với tên bảng/cột trong DB của bạn --}}
                                                    @if(now() <= $idea->academicYear->closure_date) 
                                                        <span class="badge bg-success ms-2 px-2 py-1"><i class="bi bi-unlock-fill me-1"></i>Open</span>
                                                    @else
                                                        <span class="badge bg-secondary ms-2 px-2 py-1"><i class="bi bi-lock-fill me-1"></i>Closed</span>
                                                    @endif
                                                </div>
                                                
                                                <div class="btn-group shadow-sm" role="group">
                                                    <a href="{{ route('ideas.show', $idea->id) }}" class="btn btn-sm btn-outline-info" title="View Details">
                                                        <i class="bi bi-eye-fill"></i> View
                                                    </a>
                                                    
                                                    @if(now() <= $idea->academicYear->closure_date)
                                                        <a href="{{ route('ideas.edit', $idea->id) }}" class="btn btn-sm btn-outline-warning" title="Edit Idea">
                                                            <i class="bi bi-pencil-square"></i> Edit
                                                        </a>
                                                    @endif
                                                </div>

                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-3 text-secondary"></i>
                                            <p class="mb-0">You haven't submitted any ideas yet.</p>
                                        </div>
                                    @endforelse
                                </div>
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $myIdeas->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="activities">
                            <div class="bg-white p-4 rounded shadow-sm">
                                <h5 class="mb-4 text-success fw-bold">Recent Activities</h5>
                                <ul class="list-group list-group-flush">
                                    @forelse($recentComments as $cmt)
                                        <li class="list-group-item px-0 py-3 border-bottom d-flex align-items-start">
                                            <i class="bi bi-chat-dots-fill text-info mt-1 me-3 fs-5"></i>
                                            <div>
                                                <p class="mb-1">You commented on idea <strong>"{{ $cmt->idea->title }}"</strong></p>
                                                <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $cmt->created_at->diffForHumans() }}</small>
                                            </div>
                                        </li>
                                    @empty
                                        <div class="text-center py-5 text-muted">
                                            <i class="bi bi-activity fs-1 d-block mb-3 text-secondary"></i>
                                            <p class="mb-0">No recent activities found.</p>
                                        </div>
                                    @endforelse
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection