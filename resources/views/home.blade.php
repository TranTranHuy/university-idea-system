@extends('layouts.master')

@section('content')
    <div class="p-5 mb-4 bg-light rounded-3 shadow-sm hero-section">
        <div class="container-fluid py-5">
            <h1 class="display-5 fw-bold">Welcome to UIS</h1>
            <p class="col-md-8 fs-4">A creative hub for students and staff to share, discuss, and improve ideas across the university.</p>
            <div class="d-flex gap-2">
                <button class="btn btn-primary btn-lg px-4" type="button">Submit Idea Now</button>
                <button class="btn btn-outline-secondary btn-lg px-4" type="button">Learn More</button>
            </div>
        </div>
    </div>

    <div class="row align-items-md-stretch">
        <div class="col-md-6 mb-3">
            <div class="h-100 p-5 text-white bg-dark rounded-3 shadow-sm">
                <h2>Latest Ideas</h2>
                <p>Stay updated with the newest contributions from various departments. Don't miss out on fresh innovations.</p>
                <button class="btn btn-outline-light" type="button">View Latest</button>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="h-100 p-5 bg-body-tertiary border rounded-3 shadow-sm">
                <h2>Most Popular</h2>
                <p>Discover the top-rated ideas voted by the community this month. See what's trending right now.</p>
                <button class="btn btn-outline-dark" type="button">View Leaderboard</button>
            </div>
        </div>
    </div>
@endsection
