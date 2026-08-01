@extends('layouts.operations-dashboard')

@section('title', 'Manage Users')
@section('page-title', 'Users')
@section('page-subtitle', 'Manage platform users and roles')

@section('content')
<div class="ap-shell ap-users-page">
    <section class="ap-page-head">
        <div class="ap-head-copy">
            <span class="ap-eyebrow">Identity & access</span>
            <h1>People behind every delivery</h1>
            <p>Review your customers, coordinate agents, and keep administrator access clear from one secure workspace.</p>
        </div>
        <div class="ap-head-actions">
            <a href="{{ route('admin.users.create') }}" class="ap-button light">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                Add new user
            </a>
        </div>
    </section>

    <section class="ap-kpi-grid">
        @foreach([
            ['All users', $stats['total'], '', 'Complete platform directory'],
            ['Customers', $stats['customers'], 'green', 'Shipment owners'],
            ['Agents', $stats['agents'], 'blue', 'Operations team'],
            ['Administrators', $stats['admins'], 'orange', 'Full platform access'],
        ] as [$label, $value, $tone, $note])
            <article class="ap-kpi {{ $tone }}">
                <div class="ap-kpi-top">
                    <span class="ap-kpi-icon"><svg viewBox="0 0 24 24"><circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0 1 12 0M16 4a3 3 0 0 1 0 6M17 14a5 5 0 0 1 4 5"/></svg></span>
                    <span class="ap-kpi-label">{{ $label }}</span>
                </div>
                <p class="ap-kpi-value">{{ number_format($value) }}</p>
                <p class="ap-kpi-note">{{ $note }}</p>
            </article>
        @endforeach
    </section>

    <section class="ap-toolbar">
        <div>
            <strong class="ap-toolbar-title">User directory</strong>
            <span class="ap-toolbar-copy">Choose a role to focus the table.</span>
        </div>
        <nav class="ap-tabs" aria-label="Filter users by role">
            @foreach(['all', 'admin', 'customer', 'agent'] as $r)
                <a href="{{ route('admin.users.index', ['role' => $r]) }}" class="ap-tab {{ $role === $r ? 'active' : '' }}">
                    {{ ucfirst($r) }}
                </a>
            @endforeach
        </nav>
    </section>

    <section class="ap-card ap-table-card">
        <div class="ap-card-head">
            <div>
                <h2 class="ap-card-title">{{ $role === 'all' ? 'Everyone' : ucfirst($role).' accounts' }}</h2>
                <p class="ap-card-copy">{{ $users->total() }} {{ str('account')->plural($users->total()) }} in this view</p>
            </div>
            <span class="ap-directory-pill">Live directory</span>
        </div>
        <div class="ap-table-scroll">
            <table class="ap-table ap-users-table">
                <thead>
                    <tr><th>User</th><th>Email</th><th>Phone</th><th>Role</th><th>Joined</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="ap-person">
                                    <span class="ap-avatar">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                    <div><strong>{{ $user->name }}</strong><small>Account #{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</small></div>
                                </div>
                            </td>
                            <td><a class="ap-email" href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
                            <td>
                                @if($user->phone)
                                    <a class="ap-phone" href="tel:{{ $user->phone }}"><svg viewBox="0 0 24 24"><path d="M21 16.4v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.3 19.3 0 0 1-6-6A19.8 19.8 0 0 1 1.1 3.7 2 2 0 0 1 3.1 1.5h3A2 2 0 0 1 8 3.2c.1 1 .4 2 .7 2.9a2 2 0 0 1-.4 2.1L7 9.5a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.7 1.9Z"/></svg>{{ $user->phone }}</a>
                                @else
                                    <span class="ap-not-provided">Not provided</span>
                                @endif
                            </td>
                            <td>
                                @foreach($user->roles as $r)
                                    <span class="ap-badge {{ $r->name }}">{{ ucfirst($r->name) }}</span>
                                @endforeach
                            </td>
                            <td><span class="ap-date">{{ $user->created_at->format('M d, Y') }}</span><small class="ap-date-relative">{{ $user->created_at->diffForHumans() }}</small></td>
                            <td>
                                <div class="ap-row-actions">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="ap-row-button">Edit</a>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Delete {{ addslashes($user->name) }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="ap-row-button danger">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="ap-empty"><span class="ap-empty-icon">＋</span><strong>No users in this view</strong><p>Choose another role or create a new account.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="ap-pagination">{{ $users->links() }}</div>
        @endif
    </section>
</div>
@endsection

@push('styles')
<style>
    .ap-toolbar-title{display:block;color:#1a2744;font-size:12px;font-weight:800}.ap-toolbar-copy{display:block;margin-top:3px;color:#7d8aa1;font-size:9px}
    .ap-directory-pill{display:inline-flex;align-items:center;gap:7px;padding:7px 10px;border-radius:99px;color:#087c58;background:#e9f9f2;font-size:9px;font-weight:800}.ap-directory-pill:before{content:'';width:6px;height:6px;border-radius:50%;background:#1ec58b;box-shadow:0 0 0 4px rgba(30,197,139,.11)}
    .ap-users-table{min-width:960px}.ap-email{color:#52627e}.ap-email:hover{color:#6137df}.ap-phone{display:inline-flex;align-items:center;gap:6px;color:#4e5e79;font-weight:700}.ap-phone svg{width:14px;height:14px;fill:none;stroke:#16a779;stroke-width:2}.ap-not-provided{color:#9ba6b8;font-size:9px}.ap-date{display:block;color:#43516b;font-weight:700}.ap-date-relative{display:block;margin-top:3px;color:#96a1b3;font-size:8px}.ap-row-actions{display:flex;align-items:center;gap:7px}.ap-row-button{min-height:31px;display:inline-flex;align-items:center;padding:0 11px;border:1px solid #dfe5ef;border-radius:9px;color:#52617c;background:#fff;font-size:9px;font-weight:800;cursor:pointer}.ap-row-button:hover{color:#5c31d5;border-color:#d4c8f7;background:#f8f5ff}.ap-row-button.danger{color:#d93b54;border-color:#ffd8de;background:#fff5f6}.ap-pagination{padding:15px 20px;border-top:1px solid #edf1f6}
</style>
@endpush
