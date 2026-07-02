import sys

def process(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # 3. Carrier badge
    t3 = '''                    <div class="absolute top-4 right-4 items-center gap-1 rounded-full bg-violet-500 px-2 py-1 text-[10px] font-semibold text-white shadow-sm ${String(selectedCarrierId) === String(c.id) ? '' : 'hidden'}" data-carrier-selected-badge="${c.id}">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Selected
                    </div>'''
    r3 = '''                    <div class="absolute top-3 right-3 items-center justify-center w-6 h-6 rounded-full bg-violet-500 text-white shadow-sm ${String(selectedCarrierId) === String(c.id) ? '' : 'hidden'}" data-carrier-selected-badge="${c.id}">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>'''
    content = content.replace(t3, r3)

    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)

process(r'resources/views/agent/shipments/create.blade.php')
process(r'resources/views/customer/shipments/create.blade.php')
