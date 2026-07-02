import re

def process(filepath):
    with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()

    # The emojis got mangled. We know exactly what they should be.
    # We can find <div class="text-4xl">...</div> and replace it based on the badge name.
    
    # Document card
    content = re.sub(
        r'<div class="text-4xl">.*?</div>(\s*<div class="w-6 h-6 rounded-full border-2 border-gray-600 flex items-center justify-center transition-all duration-200" data-shipment-type-badge="document">)',
        r'<div class="text-4xl">??</div>\1',
        content,
        flags=re.DOTALL
    )

    # Non-Document card
    content = re.sub(
        r'<div class="text-4xl">.*?</div>(\s*<div class="w-6 h-6 rounded-full border-2 border-gray-600 flex items-center justify-center transition-all duration-200" data-shipment-type-badge="non_document">)',
        r'<div class="text-4xl">??</div>\1',
        content,
        flags=re.DOTALL
    )

    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)

process(r'resources/views/agent/shipments/create.blade.php')
process(r'resources/views/customer/shipments/create.blade.php')
