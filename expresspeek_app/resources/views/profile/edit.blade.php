@extends('layouts.operations-dashboard')

@section('title', 'Profile')
@section('page-title', 'My Profile')
@section('page-subtitle', 'Personal details, profile photo, and account security')

@section('content')
<style>
    [x-cloak]{display:none!important}.profile-file-input{display:none!important}.profile-shell{max-width:1180px;margin:0 auto;display:grid;gap:18px}.profile-hero{position:relative;min-height:230px;overflow:hidden;border-radius:24px;color:#fff;background:linear-gradient(115deg,#07143f 0%,#182568 48%,#5733d7 100%);box-shadow:0 22px 55px rgba(27,37,96,.18)}.profile-hero:before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 82% 18%,rgba(103,187,255,.4),transparent 25%),radial-gradient(circle at 65% 110%,rgba(156,115,255,.5),transparent 38%),url('/images/tracking-route-hero.webp') 92% center/auto 120% no-repeat;opacity:.78}.profile-hero:after{content:'';position:absolute;inset:0;background:linear-gradient(90deg,rgba(5,14,50,.96),rgba(11,25,81,.68) 60%,rgba(29,28,106,.2))}.profile-hero-inner{position:relative;z-index:2;min-height:230px;padding:34px;display:flex;align-items:flex-end;justify-content:space-between;gap:28px}.profile-identity{display:flex;align-items:center;gap:22px}.profile-hero-avatar{width:112px;height:112px;flex:0 0 auto;display:grid;place-items:center;overflow:hidden;border:5px solid rgba(255,255,255,.92);border-radius:30px;color:#fff;background:linear-gradient(135deg,#8b5cf6,#2563eb);box-shadow:0 20px 36px rgba(0,0,0,.25);font-size:28px;font-weight:850}.profile-hero-avatar img{width:100%;height:100%;object-fit:cover}.profile-eyebrow{display:inline-flex;align-items:center;gap:7px;margin-bottom:9px;padding:6px 10px;border:1px solid rgba(255,255,255,.16);border-radius:99px;background:rgba(255,255,255,.1);font-size:9px;font-weight:850;letter-spacing:.1em;text-transform:uppercase}.profile-eyebrow i{width:7px;height:7px;border-radius:50%;background:#4ce0a7;box-shadow:0 0 0 4px rgba(76,224,167,.13)}.profile-hero h2{margin:0;font-size:28px;line-height:1.15;font-weight:850;letter-spacing:-.04em}.profile-hero p{margin:7px 0 0;color:rgba(230,235,255,.8);font-size:12px}.profile-completion{min-width:245px;padding:16px 18px;border:1px solid rgba(255,255,255,.14);border-radius:16px;background:rgba(255,255,255,.1);backdrop-filter:blur(12px)}.profile-completion-head{display:flex;align-items:center;justify-content:space-between;gap:12px;color:#fff;font-size:10px;font-weight:800}.profile-completion-track{height:6px;margin-top:10px;overflow:hidden;border-radius:99px;background:rgba(255,255,255,.18)}.profile-completion-track span{display:block;height:100%;border-radius:inherit;background:linear-gradient(90deg,#47e3ae,#7bb8ff)}.profile-grid{display:grid;grid-template-columns:minmax(0,1.15fr) minmax(340px,.85fr);gap:18px}.profile-card{overflow:hidden;border:1px solid #e3e8f1;border-radius:20px;background:#fff;box-shadow:0 10px 34px rgba(30,45,84,.055)}.profile-card-head{display:flex;align-items:center;gap:12px;padding:22px 24px;border-bottom:1px solid #edf0f5}.profile-card-icon{width:42px;height:42px;display:grid;place-items:center;border-radius:13px;color:#6640e8;background:#f1edff}.profile-card-icon.blue{color:#2563eb;background:#ebf3ff}.profile-card-icon svg{width:20px;height:20px}.profile-card-head h3{margin:0;color:#12203f;font-size:16px;font-weight:850}.profile-card-head p{margin:4px 0 0;color:#7b889e;font-size:10px}.profile-card-body{padding:24px}.profile-form-grid{display:grid;grid-template-columns:1fr 1fr;gap:17px}.profile-field.full{grid-column:1/-1}.profile-label{display:flex;justify-content:space-between;gap:8px;margin-bottom:7px;color:#31405c;font-size:10px;font-weight:800}.profile-label span{color:#9aa5b6;font-size:9px;font-weight:650}.profile-input-wrap{position:relative}.profile-input-icon{position:absolute;left:13px;top:50%;width:17px;height:17px;transform:translateY(-50%);color:#8c9ab1;pointer-events:none}.profile-input{width:100%;height:48px;padding:0 15px 0 40px;border:1.5px solid #dde3ed;border-radius:12px;outline:0;color:#13213e;background:#fafbfd;font-size:12px;font-weight:650;transition:.2s}.profile-input:focus{border-color:#7856ee;background:#fff;box-shadow:0 0 0 4px rgba(120,86,238,.1)}.profile-input.no-icon{padding-left:14px}.profile-phone{display:grid;grid-template-columns:122px minmax(0,1fr);gap:8px}.profile-code{padding:0 8px;border:1.5px solid #dde3ed;border-radius:12px;color:#263650;background:#fafbfd;font-size:11px;font-weight:750}.profile-save{min-height:46px;padding:0 20px;display:inline-flex;align-items:center;justify-content:center;gap:8px;border:0;border-radius:12px;color:#fff;background:linear-gradient(135deg,#7549ee,#4338ca 58%,#2475eb);box-shadow:0 12px 24px rgba(82,51,201,.2);font-size:11px;font-weight:850;cursor:pointer}.profile-save:hover{transform:translateY(-1px)}.profile-photo-box{display:flex;align-items:center;gap:17px;padding:16px;border:1px dashed #cfc7f8;border-radius:16px;background:linear-gradient(135deg,#faf9ff,#f5f8ff)}.profile-photo-preview{width:78px;height:78px;flex:0 0 auto;display:grid;place-items:center;overflow:hidden;border:4px solid #fff;border-radius:22px;color:#fff;background:linear-gradient(135deg,#7550ed,#2972eb);box-shadow:0 10px 24px rgba(72,52,174,.16);font-size:18px;font-weight:850}.profile-photo-preview img{width:100%;height:100%;object-fit:cover}.profile-photo-copy strong{display:block;color:#1d2c4a;font-size:12px}.profile-photo-copy span{display:block;margin-top:4px;color:#7d8aa0;font-size:9px;line-height:1.5}.profile-upload-button{display:inline-flex;margin-top:9px;padding:8px 11px;border:1px solid #d8d0f8;border-radius:9px;color:#6040da;background:#fff;font-size:9px;font-weight:850;cursor:pointer}.profile-upload-button:hover{background:#f4f1ff}.profile-security-list{display:grid;gap:9px;margin:0 0 20px}.profile-security-item{display:flex;align-items:center;gap:10px;padding:11px 12px;border:1px solid #e8ecf3;border-radius:12px;color:#586781;background:#fbfcfe;font-size:9px;font-weight:700}.profile-security-item svg{width:17px;height:17px;color:#11a875}.profile-danger{border:1px solid #f4d1d7;border-radius:18px;background:#fff}.profile-danger summary{padding:17px 20px;display:flex;align-items:center;justify-content:space-between;gap:14px;color:#a9273e;font-size:11px;font-weight:850;cursor:pointer;list-style:none}.profile-danger summary::-webkit-details-marker{display:none}.profile-danger-summary{display:flex;align-items:center;gap:10px}.profile-danger-summary svg{width:18px;height:18px}.profile-danger-body{padding:0 20px 20px;border-top:1px solid #f8e1e5}.profile-password-wrap{position:relative}.profile-password-toggle{position:absolute;right:7px;top:50%;width:34px;height:34px;transform:translateY(-50%);display:grid;place-items:center;border:0;border-radius:9px;color:#8290a7;background:transparent;cursor:pointer}.profile-password-toggle:hover{color:#6240e4;background:#f2efff}.profile-password-toggle svg{width:16px;height:16px}.profile-success{margin-bottom:18px;padding:13px 15px;border:1px solid #c9eddf;border-radius:12px;color:#087953;background:#effaf6;font-size:10px;font-weight:750}.profile-error{margin-bottom:18px;padding:13px 15px;border:1px solid #ffd1d9;border-radius:12px;color:#b4233d;background:#fff3f5;font-size:10px;font-weight:750}
    .profile-shell,.profile-grid,.profile-card,.profile-card-body,.profile-form-grid,.profile-field,.profile-hero-inner,.profile-identity,.profile-photo-copy{min-width:0}.profile-hero p,.profile-photo-copy span{overflow-wrap:anywhere}.profile-save{white-space:nowrap}.profile-save svg{width:16px;height:16px;flex:none}
    @media(max-width:900px){.profile-grid{grid-template-columns:1fr}.profile-completion{display:none}}@media(max-width:640px){.profile-shell{width:100%;gap:12px}.profile-hero{width:100%;min-height:235px;border-radius:18px}.profile-hero-inner{width:100%;min-height:235px;padding:24px 18px;align-items:center}.profile-identity{width:100%;display:block}.profile-hero-avatar{width:82px;height:82px;margin-bottom:16px;border-radius:23px;font-size:20px}.profile-hero h2{font-size:23px}.profile-card{width:100%;border-radius:16px}.profile-card-head,.profile-card-body{padding:18px}.profile-form-grid{width:100%;grid-template-columns:minmax(0,1fr)}.profile-photo-box{width:100%;align-items:center;gap:12px;padding:13px}.profile-photo-preview{width:70px;height:70px;border-radius:19px}.profile-phone{width:100%;grid-template-columns:108px minmax(0,1fr)}.profile-label span{font-size:8px}.profile-danger summary{align-items:flex-start}.profile-danger summary>span:last-child{text-align:right}}
</style>

@php
    $completion = $user->avatar_path && $user->phone ? 100 : ($user->avatar_path || $user->phone ? 75 : 50);
@endphp

<div class="profile-shell">
    <section class="profile-hero">
        <div class="profile-hero-inner">
            <div class="profile-identity">
                <div class="profile-hero-avatar">
                    @if($user->avatarUrl())
                        <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }} profile photo">
                    @else
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    @endif
                </div>
                <div>
                    <span class="profile-eyebrow"><i></i>{{ ucfirst($user->getRoleNames()->first() ?? 'member') }} workspace</span>
                    <h2>{{ $user->name }}</h2>
                    <p>{{ $user->email }} @if($user->phone) · {{ $user->phone }} @endif</p>
                </div>
            </div>
            <div class="profile-completion">
                <div class="profile-completion-head"><span>Profile strength</span><strong>{{ $completion }}%</strong></div>
                <div class="profile-completion-track"><span style="width:{{ $completion }}%"></span></div>
            </div>
        </div>
    </section>

    <div class="profile-grid">
        <section class="profile-card">
            <div class="profile-card-head"><span class="profile-card-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg></span><div><h3>Personal profile</h3><p>Identity, contact details, and profile photo</p></div></div>
            <div class="profile-card-body">@include('profile.partials.update-profile-information-form')</div>
        </section>

        <section class="profile-card">
            <div class="profile-card-head"><span class="profile-card-icon blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3 20 6v6c0 5-3.4 8-8 9-4.6-1-8-4-8-9V6l8-3Z"/><path d="m8 12 2.5 2.5L16 9"/></svg></span><div><h3>Account security</h3><p>Password controls and protected access</p></div></div>
            <div class="profile-card-body">@include('profile.partials.update-password-form')</div>
        </section>
    </div>

    <details class="profile-danger">
        <summary><span class="profile-danger-summary"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M9 7V4h6v3M7 7l1 14h8l1-14M10 11v6M14 11v6"/></svg>Danger zone</span><span>Account deletion options</span></summary>
        <div class="profile-danger-body">@include('profile.partials.delete-user-form')</div>
    </details>
</div>
@endsection
