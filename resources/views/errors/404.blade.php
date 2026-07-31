@extends('errors.layout')

@section('title', __('Not Found'))
@section('code', '404')
@section('message', __('Record Not Found'))
@section('description', __('The page or record you are looking for does not exist. It might have been moved or deleted.'))

@section('illustration')
<div class="p-6 bg-blue-50 rounded-full inline-block">
    <svg class="w-20 h-20 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
</div>
@endsection
