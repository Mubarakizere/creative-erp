@extends('errors.layout')

@section('title', __('Unprocessable Entity'))
@section('code', '422')
@section('message', __('Validation Error'))
@section('description', __('The data you submitted was invalid or could not be processed. Please go back and check your inputs.'))

@section('illustration')
<div class="p-6 bg-pink-50 rounded-full inline-block">
    <svg class="w-20 h-20 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
</div>
@endsection
