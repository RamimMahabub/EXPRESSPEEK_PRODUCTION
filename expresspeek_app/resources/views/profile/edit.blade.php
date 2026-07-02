@extends('layouts.dashboard')

@section('title', 'Profile')
@section('page-title', 'My Profile')
@section('page-subtitle', 'Manage your account details and security')

@section('content')
<div class="mx-auto max-w-6xl space-y-6">
    <div class="neon-card rounded-2xl p-5 sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-violet-600 to-indigo-600 text-lg font-bold text-white shadow-md shadow-violet-200">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">{{ $user->name }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ $user->email }}</p>
                </div>
            </div>
            <span class="inline-flex w-fit items-center gap-2 rounded-full border border-violet-200 bg-violet-50 px-3 py-1.5 text-xs font-semibold text-violet-700">
                <span class="h-2 w-2 rounded-full bg-violet-500"></span>
                {{ ucfirst($user->getRoleNames()->first() ?? 'member') }} account
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <div class="neon-card rounded-2xl p-5 sm:p-8">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="neon-card rounded-2xl p-5 sm:p-8">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <div class="rounded-2xl border border-red-200 bg-red-50/70 p-5 shadow-sm sm:p-8">
        <div class="max-w-2xl">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection
