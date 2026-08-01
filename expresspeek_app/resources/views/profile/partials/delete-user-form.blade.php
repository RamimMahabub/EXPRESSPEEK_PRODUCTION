<section class="pt-5">
    <h3 class="text-sm font-extrabold text-red-900">Permanently delete this account</h3>
    <p class="mt-2 max-w-2xl text-xs leading-6 text-red-700">Your profile, saved photo, and account data will be permanently removed. This action cannot be undone.</p>

    <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" class="mt-4 inline-flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-xs font-extrabold text-red-700 transition hover:bg-red-100">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M9 7V4h6v3M7 7l1 14h8l1-14"/></svg>
        Delete my account
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 sm:p-8">
            @csrf
            @method('delete')
            <div class="mb-5 grid h-12 w-12 place-items-center rounded-2xl bg-red-50 text-red-600"><svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.6 2.6 17a2 2 0 0 0 1.7 3h15.4a2 2 0 0 0 1.7-3L13.7 3.6a2 2 0 0 0-3.4 0Z"/></svg></div>
            <h2 class="text-xl font-extrabold text-slate-900">Delete your account?</h2>
            <p class="mt-2 text-sm leading-6 text-slate-500">Enter your current password to confirm permanent deletion of your account and profile photo.</p>
            <div class="mt-5">
                <label class="profile-label" for="delete_account_password">Current password</label>
                <input id="delete_account_password" name="password" type="password" class="profile-input no-icon" autocomplete="current-password" placeholder="Enter your password">
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>
            <div class="mt-6 flex flex-wrap justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-600">Keep account</button>
                <button type="submit" class="rounded-xl bg-red-600 px-4 py-2.5 text-xs font-extrabold text-white shadow-lg shadow-red-200">Delete permanently</button>
            </div>
        </form>
    </x-modal>
</section>
