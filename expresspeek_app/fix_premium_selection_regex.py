import re

def process(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # 1. Document badge
    content = re.sub(
        r'<div class="absolute top-3 right-3 items-center justify-center w-6 h-6 rounded-full bg-violet-500 text-white shadow-sm hidden" data-shipment-type-badge="document">.*?</div>\s*<div class="text-4xl mb-3">??</div>',
        '<div class="flex justify-between items-start mb-3">\n                                <div class="text-4xl">??</div>\n                                <div class="w-6 h-6 rounded-full border-2 border-gray-600 flex items-center justify-center transition-all duration-200" data-shipment-type-badge="document">\n                                    <svg class="h-3.5 w-3.5 text-white opacity-0 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>\n                                </div>\n                            </div>',
        content,
        flags=re.DOTALL
    )

    # 2. Non-document badge
    content = re.sub(
        r'<div class="absolute top-3 right-3 items-center justify-center w-6 h-6 rounded-full bg-violet-500 text-white shadow-sm hidden" data-shipment-type-badge="non_document">.*?</div>\s*<div class="text-4xl mb-3">??</div>',
        '<div class="flex justify-between items-start mb-3">\n                                <div class="text-4xl">??</div>\n                                <div class="w-6 h-6 rounded-full border-2 border-gray-600 flex items-center justify-center transition-all duration-200" data-shipment-type-badge="non_document">\n                                    <svg class="h-3.5 w-3.5 text-white opacity-0 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>\n                                </div>\n                            </div>',
        content,
        flags=re.DOTALL
    )

    # 3. Shipment Type JS
    content = re.sub(
        r'if \(isSelected\) \{\s*badge\.classList\.remove\(\'hidden\'\);\s*badge\.classList\.add\(\'flex\'\);\s*\} else \{\s*badge\.classList\.add\(\'hidden\'\);\s*badge\.classList\.remove\(\'flex\'\);\s*\}',
        "if (isSelected) {\n                badge.className = 'w-6 h-6 rounded-full border-2 border-violet-500 bg-violet-500 flex items-center justify-center transition-all duration-200';\n                badge.querySelector('svg').classList.remove('opacity-0');\n            } else {\n                badge.className = 'w-6 h-6 rounded-full border-2 border-gray-600 flex items-center justify-center transition-all duration-200';\n                badge.querySelector('svg').classList.add('opacity-0');\n            }",
        content
    )

    # 4. Carrier badge
    content = re.sub(
        r'<div class="absolute top-3 right-3 items-center justify-center w-6 h-6 rounded-full bg-violet-500 text-white shadow-sm \$\{String\(selectedCarrierId\) === String\(c\.id\) \? \'\' : \'hidden\'\}" data-carrier-selected-badge="\$\{c\.id\}">.*?</div>\s*<div class="flex items-center justify-between gap-3 mb-3 pr-8">\s*<div class="min-w-0">',
        '<div class="flex items-center justify-between gap-3 mb-3">\n                        <div class="flex items-center gap-3 min-w-0">\n                            <div class="w-5 h-5 shrink-0 rounded-full border-2 flex items-center justify-center transition-all duration-200 ${String(selectedCarrierId) === String(c.id) ? \'border-violet-500 bg-violet-500\' : \'border-gray-600\'}" data-carrier-selected-badge="${c.id}">\n                                <svg class="h-3 w-3 text-white transition-opacity ${String(selectedCarrierId) === String(c.id) ? \'opacity-100\' : \'opacity-0\'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>\n                            </div>\n                            <div class="min-w-0">',
        content,
        flags=re.DOTALL
    )

    # 5. Carrier Card JS
    content = re.sub(
        r'if \(isSelected\) \{\s*badge\.classList\.remove\(\'hidden\'\);\s*badge\.classList\.add\(\'flex\'\);\s*\} else \{\s*badge\.classList\.add\(\'hidden\'\);\s*badge\.classList\.remove\(\'flex\'\);\s*\}',
        "if (isSelected) {\n                badge.className = 'w-5 h-5 shrink-0 rounded-full border-2 border-violet-500 bg-violet-500 flex items-center justify-center transition-all duration-200';\n                badge.querySelector('svg').classList.remove('opacity-0');\n                badge.querySelector('svg').classList.add('opacity-100');\n            } else {\n                badge.className = 'w-5 h-5 shrink-0 rounded-full border-2 border-gray-600 flex items-center justify-center transition-all duration-200';\n                badge.querySelector('svg').classList.remove('opacity-100');\n                badge.querySelector('svg').classList.add('opacity-0');\n            }",
        content
    )

    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)

process(r'resources/views/agent/shipments/create.blade.php')
process(r'resources/views/customer/shipments/create.blade.php')
