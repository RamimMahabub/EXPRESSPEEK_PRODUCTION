<div x-data="notificationDropdown()" x-init="init()" class="relative">
    <button @click="toggle()" @click.away="close()" class="ops-icon-btn relative" aria-label="Notifications">
        <svg class="ops-icon" viewBox="0 0 24 24"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg>
        <span x-show="unreadCount > 0" x-text="unreadCount" style="display: none;" class="ops-notice bg-red-500 text-white rounded-full text-[8px] font-bold absolute -top-1 -right-1 min-w-[16px] h-4 flex items-center justify-center px-1"></span>
        
        <!-- Ping animation for lively feel -->
        <span x-show="unreadCount > 0" class="absolute top-0 right-0 w-3 h-3 bg-red-500 rounded-full animate-ping opacity-75"></span>
    </button>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         style="display: none;"
         class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden text-left origin-top-right">
        
        <div class="px-4 py-3 bg-gray-50/80 border-b border-gray-100 flex items-center justify-between backdrop-blur-sm">
            <h3 class="text-sm font-bold text-gray-800">Notifications</h3>
            <button @click="markAllAsRead()" x-show="unreadCount > 0" class="text-[10px] font-bold text-violet-600 hover:text-violet-700 bg-violet-50 hover:bg-violet-100 px-2 py-1 rounded-md transition-colors">Mark all read</button>
        </div>

        <div class="max-h-80 overflow-y-auto">
            <template x-if="loading">
                <div class="p-8 text-center flex flex-col items-center justify-center">
                    <svg class="animate-spin h-6 w-6 text-violet-500 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span class="text-xs text-gray-500">Loading...</span>
                </div>
            </template>
            
            <template x-if="!loading && notifications.length === 0">
                <div class="p-8 text-center flex flex-col items-center justify-center">
                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <p class="text-xs text-gray-500 font-medium">No new notifications</p>
                </div>
            </template>

            <template x-for="notification in notifications" :key="notification.id">
                <a :href="notification.data.action_url || '#'" @click="markAsRead(notification.id)" class="block p-4 border-b border-gray-50 hover:bg-violet-50/50 transition-colors relative group" :class="{'bg-violet-50/20': notification.read_at === null}">
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 mt-0.5">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full" :class="getIconBgClass(notification.data.color)">
                                <i :class="getIconClass(notification.data.icon)" class="w-4 h-4" :style="'color: ' + getIconColor(notification.data.color)"></i>
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[13px] font-bold text-gray-900 mb-0.5" x-text="notification.data.title"></p>
                            <p class="text-[11px] text-gray-600 leading-snug line-clamp-2" x-text="notification.data.message"></p>
                            <p class="text-[9px] font-semibold text-gray-400 mt-1.5 uppercase tracking-wider" x-text="formatDate(notification.created_at)"></p>
                        </div>
                        <div x-show="notification.read_at === null" class="flex-shrink-0 flex items-center">
                            <div class="w-2 h-2 bg-violet-600 rounded-full"></div>
                        </div>
                    </div>
                </a>
            </template>
        </div>
        
        <div class="px-4 py-2 bg-gray-50/50 border-t border-gray-100 text-center">
            <a href="{{ route('notifications.all') }}" class="text-[10px] font-bold text-gray-500 hover:text-gray-700 transition-colors">View all</a>
        </div>
    </div>
</div>

<script>
function notificationDropdown() {
    return {
        open: false,
        loading: false,
        notifications: [],
        unreadCount: 0,
        
        init() {
            this.fetchNotifications();
            // Polling for lively feel
            setInterval(() => {
                if (!this.open) {
                    this.fetchNotifications();
                }
            }, 60000); // Check every minute
        },
        
        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.unreadCount = 0;
                this.fetchNotifications();
                this.markAllAsRead();
            }
        },
        
        close() {
            this.open = false;
        },
        
        fetchNotifications() {
            this.loading = this.notifications.length === 0;
            fetch('/notifications', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.notifications) {
                    this.notifications = data.notifications;
                    this.unreadCount = this.open ? 0 : this.notifications.filter(n => n.read_at === null).length;
                }
                this.loading = false;
            })
            .catch(error => {
                console.error('Error fetching notifications:', error);
                this.loading = false;
            });
        },
        
        markAsRead(id) {
            const notification = this.notifications.find(n => n.id === id);
            if (notification && notification.read_at === null) {
                fetch(`/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }).then(() => {
                    notification.read_at = new Date().toISOString();
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                });
            }
        },
        
        markAllAsRead() {
            fetch('/notifications/read-all', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).then(() => {
                this.notifications.forEach(n => { n.read_at = new Date().toISOString(); });
                this.unreadCount = 0;
            });
        },
        
        formatDate(dateStr) {
            const date = new Date(dateStr);
            const now = new Date();
            const diff = Math.floor((now - date) / 1000);
            
            if (diff < 60) return 'Just now';
            if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
            if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
            return Math.floor(diff / 86400) + 'd ago';
        },
        
        getIconClass(icon) {
            // Simplified lucide/feather fallback icons using feather CDN
            const iconMap = {
                'x-circle': 'lucide-x-circle',
                'truck': 'lucide-truck',
                'user-check': 'lucide-user-check',
                'check-circle': 'lucide-check-circle',
                'alert-circle': 'lucide-alert-circle',
                'package': 'lucide-package',
                'credit-card': 'lucide-credit-card',
                'bell': 'lucide-bell'
            };
            return iconMap[icon] || 'lucide-bell';
        },
        
        getIconBgClass(color) {
            const map = {
                'red': 'bg-red-100',
                'violet': 'bg-violet-100',
                'blue': 'bg-blue-100',
                'green': 'bg-green-100',
                'orange': 'bg-orange-100'
            };
            return map[color] || 'bg-gray-100';
        },
        
        getIconColor(color) {
            const map = {
                'red': '#ef4444',
                'violet': '#8b5cf6',
                'blue': '#3b82f6',
                'green': '#10b981',
                'orange': '#f97316'
            };
            return map[color] || '#6b7280';
        }
    }
}
</script>
