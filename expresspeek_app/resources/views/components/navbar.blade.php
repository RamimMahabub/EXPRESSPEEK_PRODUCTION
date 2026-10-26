<header class="panel-topbar flex items-center justify-between gap-3 px-4 py-3.5 sm:px-6 bg-white/90 backdrop-blur-xl border-b border-slate-200 flex-shrink-0">

    <div class="flex items-center gap-4">
        {{-- Mobile Sidebar Toggle --}}
        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 -ml-2 rounded-lg text-slate-500 hover:text-violet-700 hover:bg-violet-50 transition-colors" aria-label="Open navigation">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- Page Title --}}
        <div>
            <h1 class="text-base sm:text-lg font-semibold text-slate-900">@yield('page-title', 'Dashboard')</h1>
            <p class="hidden sm:block text-xs text-slate-500">@yield('page-subtitle', config('app.name'))</p>
        </div>
    </div>

    {{-- Right side --}}
    <div class="flex items-center gap-2 sm:gap-3">

        {{-- Notifications --}}
        <div x-data="notificationsComponent()" class="relative">
            <button @click="toggle()" class="relative p-2 rounded-xl text-slate-500 hover:text-violet-700 hover:bg-violet-50 transition-colors" aria-label="Notifications">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span x-show="unreadCount > 0" x-text="unreadCount" x-cloak style="display: none;" class="absolute -top-1 -right-1 min-w-[20px] h-5 px-1 bg-violet-600 text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-white shadow-sm"></span>
            </button>

            {{-- Dropdown --}}
            <div x-show="open" x-cloak style="display: none;" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden z-50">
                <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                    <h3 class="text-sm font-bold text-slate-900">Notifications</h3>
                    <button @click="markAllAsRead()" x-show="unreadCount > 0" class="text-xs font-semibold text-violet-600 hover:text-violet-700">Mark all read</button>
                </div>
                <div class="max-h-96 overflow-y-auto">
                    <template x-if="notifications.length === 0">
                        <div class="px-4 py-8 text-center text-sm text-slate-500">
                            No notifications yet.
                        </div>
                    </template>
                    <template x-for="notification in notifications" :key="notification.id">
                        <div :class="{'bg-violet-50/50': !notification.read_at}" class="px-4 py-3 border-b border-slate-100 last:border-b-0 hover:bg-slate-50 transition-colors relative group">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-violet-100 flex items-center justify-center flex-shrink-0 text-violet-600 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-slate-800" x-text="notification.data.message || 'New notification'"></p>
                                    <p class="text-[10px] text-slate-400 mt-1 uppercase font-semibold tracking-wider" x-text="new Date(notification.created_at).toLocaleDateString()"></p>
                                </div>
                                <button x-show="!notification.read_at" @click="markAsRead(notification.id)" class="opacity-0 group-hover:opacity-100 transition-opacity p-1 text-slate-400 hover:text-violet-600" title="Mark as read">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('notificationsComponent', () => ({
                    open: false,
                    notifications: [],
                    get unreadCount() {
                        return this.notifications.filter(n => !n.read_at).length;
                    },
                    init() {
                        this.fetchNotifications();
                        setInterval(() => this.fetchNotifications(), 30000);
                    },
                    toggle() {
                        this.open = !this.open;
                        if (this.open && this.unreadCount > 0) {
                            this.markAllAsRead();
                        }
                    },
                    fetchNotifications() {
                        fetch('/notifications')
                            .then(res => res.json())
                            .then(data => {
                                this.notifications = data.notifications || [];
                            });
                    },
                    markAsRead(id) {
                        fetch(`/notifications/${id}/read`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        }).then(() => {
                            let notif = this.notifications.find(n => n.id === id);
                            if (notif) notif.read_at = new Date().toISOString();
                        });
                    },
                    markAllAsRead() {
                        fetch('/notifications/read-all', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        }).then(() => {
                            this.notifications.forEach(n => n.read_at = new Date().toISOString());
                        });
                    }
                }));
            });
        </script>

        {{-- Action Buttons --}}
        <div class="hidden lg:flex items-center gap-1 pl-3 border-l border-slate-200">
            <a href="{{ route('home') }}" class="text-sm font-medium text-slate-600 hover:text-violet-700 px-3 py-2 rounded-lg hover:bg-violet-50 transition-colors">
                Back to Home
            </a>
            <form method="POST" action="{{ route('logout') }}" class="inline m-0 p-0">
                @csrf
                <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700 px-3 py-2 rounded-lg hover:bg-red-50 transition-colors">
                    Log Out
                </button>
            </form>
        </div>

        {{-- User Avatar & Info --}}
        <div class="flex items-center gap-3 pl-3 border-l border-slate-200">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-600 to-indigo-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-sm shadow-violet-200">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="hidden md:block text-left max-w-40">
                <p class="text-sm font-medium text-slate-900 leading-none truncate">{{ auth()->user()->name }}</p>
                <p class="hidden xl:block text-xs text-slate-500 mt-1 truncate">{{ auth()->user()->email }}</p>
            </div>
        </div>
    </div>
</header>
