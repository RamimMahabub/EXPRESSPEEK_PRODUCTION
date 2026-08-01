<div class="profile-security-list">
    <div class="profile-security-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3 20 6v6c0 5-3.4 8-8 9-4.6-1-8-4-8-9V6l8-3Z"/><path d="m8 12 2.5 2.5L16 9"/></svg>Rate-limited account protection</div>
    <div class="profile-security-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>Strong password policy enabled</div>
</div>

<form method="post" action="{{ route('password.update') }}" class="space-y-4">
    @csrf
    @method('put')

    @foreach([
        ['update_password_current_password','current_password','Current password','current-password'],
        ['update_password_password','password','New password','new-password'],
        ['update_password_password_confirmation','password_confirmation','Confirm new password','new-password']
    ] as [$id,$name,$label,$autocomplete])
        <div x-data="{visible:false}">
            <label class="profile-label" for="{{ $id }}">{{ $label }}</label>
            <div class="profile-password-wrap">
                <input class="profile-input no-icon" style="padding-right:46px" id="{{ $id }}" name="{{ $name }}" :type="visible ? 'text' : 'password'" autocomplete="{{ $autocomplete }}">
                <button class="profile-password-toggle" type="button" @click="visible=!visible" :aria-label="visible ? 'Hide password' : 'Show password'">
                    <svg x-show="!visible" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                    <svg x-cloak x-show="visible" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m3 3 18 18M10.6 6.2A11.6 11.6 0 0 1 12 6c6.5 0 10 6 10 6M2 12s3.5 6 10 6a11 11 0 0 0 4.2-.8"/></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get($name)" class="mt-2" />
        </div>
    @endforeach

    <div class="flex items-center gap-3 pt-1">
        <button class="profile-save" type="submit">Update password</button>
        @if(session('status') === 'password-updated')<span class="text-xs font-semibold text-emerald-600" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,3000)" x-transition>Password updated</span>@endif
    </div>
</form>
