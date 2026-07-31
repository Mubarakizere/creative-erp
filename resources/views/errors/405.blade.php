@extends('errors.layout')

@section('title', __('Method Not Allowed'))
@section('code', '405')
@section('message', __('Method Not Allowed'))
@section('description', __('The HTTP method used for this request is not supported for this resource.'))

@section('illustration')
<div class="p-6 bg-gray-100 rounded-full inline-block">
    <svg class="w-20 h-20 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
</div>
@endsection
