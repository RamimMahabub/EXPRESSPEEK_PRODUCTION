import sys

def process(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # 1. Document badge HTML replacement
    t1 = """<div class="absolute top-3 right-3 items-center justify-center w-6 h-6 rounded-full bg-violet-500 text-white shadow-sm hidden" data-shipment-type-badge="document">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="text-4xl mb-3">??</div>""".replace('\r\n', '\n')
    
    r1 = """<div class="flex justify-between items-start mb-3">
                                <div class="text-4xl">??</div>
                                <div class="w-6 h-6 rounded-full border-2 border-gray-600 flex items-center justify-center transition-all duration-200" data-shipment-type-badge="document">
                                    <svg class="h-3.5 w-3.5 text-white opacity-0 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            </div>""".replace('\r\n', '\n')
    content = content.replace(t1, r1)

    # 2. Non-document badge HTML replacement
    t2 = """<div class="absolute top-3 right-3 items-center justify-center w-6 h-6 rounded-full bg-violet-500 text-white shadow-sm hidden" data-shipment-type-badge="non_document">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="text-4xl mb-3">??</div>""".replace('\r\n', '\n')
    
    r2 = """<div class="flex justify-between items-start mb-3">
                                <div class="text-4xl">??</div>
                                <div class="w-6 h-6 rounded-full border-2 border-gray-600 flex items-center justify-center transition-all duration-200" data-shipment-type-badge="non_document">
                                    <svg class="h-3.5 w-3.5 text-white opacity-0 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            </div>""".replace('\r\n', '\n')
    content = content.replace(t2, r2)

    # 3. Shipment Type JS update
    t_js_ship = """        if (badge) {
            if (isSelected) {
                badge.classList.remove('hidden');
                badge.classList.add('flex');
            } else {
                badge.classList.add('hidden');
                badge.classList.remove('flex');
            }
        }""".replace('\r\n', '\n')
        
    r_js_ship = """        if (badge) {
            if (isSelected) {
                badge.className = 'w-6 h-6 rounded-full border-2 border-violet-500 bg-violet-500 flex items-center justify-center transition-all duration-200';
                badge.querySelector('svg').classList.remove('opacity-0');
                badge.querySelector('svg').classList.add('opacity-100');
            } else {
                badge.className = 'w-6 h-6 rounded-full border-2 border-gray-600 flex items-center justify-center transition-all duration-200';
                badge.querySelector('svg').classList.add('opacity-0');
                badge.querySelector('svg').classList.remove('opacity-100');
            }
        }""".replace('\r\n', '\n')
    content = content.replace(t_js_ship, r_js_ship)

    # 4. Carrier Card HTML replacement
    t4 = """<div class="absolute top-3 right-3 items-center justify-center w-6 h-6 rounded-full bg-violet-500 text-white shadow-sm ${String(selectedCarrierId) === String(c.id) ? '' : 'hidden'}" data-carrier-selected-badge="${c.id}">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="flex items-center justify-between gap-3 mb-3 pr-8">
                        <div class="min-w-0">""".replace('\r\n', '\n')
                        
    r4 = """<div class="flex items-center justify-between gap-3 mb-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-5 h-5 shrink-0 rounded-full border-2 flex items-center justify-center transition-all duration-200 ${String(selectedCarrierId) === String(c.id) ? 'border-violet-500 bg-violet-500' : 'border-gray-600'}" data-carrier-selected-badge="${c.id}">
                                <svg class="h-3 w-3 text-white transition-opacity ${String(selectedCarrierId) === String(c.id) ? 'opacity-100' : 'opacity-0'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="min-w-0">""".replace('\r\n', '\n')
    content = content.replace(t4, r4)
    
    # 5. Carrier Card JS update
    t_js_car = """          if (badge) {
              if (isSelected) {
                  badge.classList.remove('hidden');
                  badge.classList.add('flex');
              } else {
                  badge.classList.add('hidden');
                  badge.classList.remove('flex');
              }
          }""".replace('\r\n', '\n')
          
    r_js_car = """          if (badge) {
              if (isSelected) {
                  badge.className = 'w-5 h-5 shrink-0 rounded-full border-2 border-violet-500 bg-violet-500 flex items-center justify-center transition-all duration-200';
                  badge.querySelector('svg').classList.remove('opacity-0');
                  badge.querySelector('svg').classList.add('opacity-100');
              } else {
                  badge.className = 'w-5 h-5 shrink-0 rounded-full border-2 border-gray-600 flex items-center justify-center transition-all duration-200';
                  badge.querySelector('svg').classList.remove('opacity-100');
                  badge.querySelector('svg').classList.add('opacity-0');
              }
          }""".replace('\r\n', '\n')
    content = content.replace(t_js_car, r_js_car)

    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)

process(r'resources/views/agent/shipments/create.blade.php')
process(r'resources/views/customer/shipments/create.blade.php')
