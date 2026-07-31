@extends('errors.layout')

@section('title', __('Too Many Requests'))
@section('code', '429')
@section('message', __('Too Many Requests'))
@section('description', __('You are sending too many requests to our servers. Please slow down and try again in a few moments.'))

@section('illustration')
<div class="p-6 bg-indigo-50 rounded-full inline-block">
    <svg class="w-20 h-20 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
</div>
@endsection
