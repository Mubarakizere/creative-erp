@extends('errors.layout')

@section('title', __('Forbidden'))
@section('code', '403')
@section('message', __('Access Denied'))
@section('description', __('You don\'t have permission to access this resource. If you believe this is a mistake, please contact your administrator.'))

@section('illustration')
<div class="p-6 bg-orange-50 rounded-full inline-block">
    <svg class="w-20 h-20 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
</div>
@endsection
