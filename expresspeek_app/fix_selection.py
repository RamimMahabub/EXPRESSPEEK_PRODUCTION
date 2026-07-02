import sys

def process(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # 1. Document badge
    t1 = '''                            <div class="absolute top-4 right-4 items-center gap-1 rounded-full bg-violet-500 px-2 py-1 text-[10px] font-semibold text-white shadow-sm hidden" data-shipment-type-badge="document">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                Selected
                            </div>'''
    r1 = '''                            <div class="absolute top-3 right-3 items-center justify-center w-6 h-6 rounded-full bg-violet-500 text-white shadow-sm hidden" data-shipment-type-badge="document">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>'''
    content = content.replace(t1, r1)

    # 2. Non-document badge
    t2 = '''                            <div class="absolute top-4 right-4 items-center gap-1 rounded-full bg-violet-500 px-2 py-1 text-[10px] font-semibold text-white shadow-sm hidden" data-shipment-type-badge="non_document">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                Selected
                            </div>'''
    r2 = '''                            <div class="absolute top-3 right-3 items-center justify-center w-6 h-6 rounded-full bg-violet-500 text-white shadow-sm hidden" data-shipment-type-badge="non_document">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>'''
    content = content.replace(t2, r2)

    # 3. Carrier badge
    t3 = '''                    <div class="absolute top-4 right-4 items-center gap-1 rounded-full bg-violet-500 px-2 py-1 text-[10px] font-semibold text-white shadow-sm " data-carrier-selected-badge="">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Selected
                    </div>'''
    r3 = '''                    <div class="absolute top-3 right-3 items-center justify-center w-6 h-6 rounded-full bg-violet-500 text-white shadow-sm " data-carrier-selected-badge="">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>'''
    content = content.replace(t3, r3)

    # 4. Flex classes
    content = content.replace("badge.classList.add('inline-flex');", "badge.classList.add('flex');")
    content = content.replace("badge.classList.remove('inline-flex');", "badge.classList.remove('flex');")

    # 5. Spacing
    content = content.replace("gap-3 mb-3 pr-14", "gap-3 mb-3 pr-8")

    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)

process(r'resources/views/agent/shipments/create.blade.php')
process(r'resources/views/customer/shipments/create.blade.php')
