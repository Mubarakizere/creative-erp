@extends('errors.layout')

@section('title', __('Page Expired'))
@section('code', '419')
@section('message', __('Session Expired'))
@section('description', __('Your session has expired due to inactivity. Please refresh the page and try again.'))

@section('illustration')
<div class="p-6 bg-purple-50 rounded-full inline-block">
    <svg class="w-20 h-20 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
</div>
@endsection
