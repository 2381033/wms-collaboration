@extends('layouts.main')

@section('title')
    No Permission
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><b>{{ __('No Permission') }}</b></div>
                <div class="card-body">
                    Sorry you do not have permission to access this page.
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    
@endpush

@push('scripts')
    
@endpush