import re

def process(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # 1. Shipment Type HTML
    content = re.sub(
        r'relative border-2 border-gray-700 rounded-2xl p-6 transition-all duration-200 peer-checked:border-violet-400 peer-checked:bg-violet-600/15 peer-checked:shadow-lg peer-checked:shadow-violet-900/20 hover:border-gray-600',
        'relative border-2 border-slate-200 rounded-2xl p-6 transition-all duration-300 peer-checked:border-violet-600 peer-checked:ring-1 peer-checked:ring-violet-600 peer-checked:bg-violet-50/40 peer-checked:shadow-lg peer-checked:shadow-violet-500/10 hover:border-violet-300',
        content
    )

    # 2. Carrier Card HTML Generation
    # Find the carrier card div inside loadCarriers
    target_carrier_div = r'<div class="relative border-2 rounded-2xl p-5 transition-all duration-200 peer-checked:border-violet-400 peer-checked:bg-violet-600/15 peer-checked:shadow-lg peer-checked:shadow-violet-900/20 hover:border-gray-500 hover:-translate-y-0\.5"\s*style="border-color: \$\{String\(selectedCarrierId\) === String\(c\.id\) \? \'#8b5cf6\' : \'#374151\'\}; background: \$\{String\(selectedCarrierId\) === String\(c\.id\) \? \'rgba\(124, 58, 237, 0\.14\)\' : \'transparent\'\};">'
    replacement_carrier_div = '<div class="relative border-2 rounded-2xl p-5 transition-all duration-300 shadow-sm ${String(selectedCarrierId) === String(c.id) ? \'border-violet-600 ring-1 ring-violet-600 bg-violet-50/50 shadow-lg shadow-violet-500/10\' : \'border-slate-200 bg-white hover:border-violet-300 hover:-translate-y-0.5\'}">'
    content = re.sub(target_carrier_div, replacement_carrier_div, content, flags=re.DOTALL)

    # 3. Carrier Card JS update
    target_js = r'if \(panel\) \{\s*panel\.style\.borderColor = isSelected \? \'#8b5cf6\' : \'#374151\';\s*panel\.style\.background = isSelected \? \'rgba\(124, 58, 237, 0\.14\)\' : \'transparent\';\s*\}'
    replacement_js = '''if (panel) {
              panel.className = isSelected 
                  ? 'relative border-2 rounded-2xl p-5 transition-all duration-300 border-violet-600 ring-1 ring-violet-600 bg-violet-50/50 shadow-lg shadow-violet-500/10'
                  : 'relative border-2 rounded-2xl p-5 transition-all duration-300 border-slate-200 bg-white hover:border-violet-300 hover:-translate-y-0.5 shadow-sm';
              panel.style.borderColor = '';
              panel.style.background = '';
          }'''
    content = re.sub(target_js, replacement_js, content)

    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)

process(r'resources/views/agent/shipments/create.blade.php')
process(r'resources/views/customer/shipments/create.blade.php')
