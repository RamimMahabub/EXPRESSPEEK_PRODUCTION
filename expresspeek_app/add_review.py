import sys

def add_purpose_to_review(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    target_str = "<p>Type: <span class=\"text-white\">${(g('shipment_type')||'').replace('_',' ')}</span></p>"
    replacement = "<p>Type: <span class=\"text-white\">${(g('shipment_type')||'').replace('_',' ')}</span></p>\n        ${g('shipment_purpose') && g('shipment_type') !== 'document' ? `<p>Purpose: <span class=\"text-white\">${g('shipment_purpose')}</span></p>` : ''}"
    
    if target_str in content and "Purpose: <span" not in content:
        content = content.replace(target_str, replacement)
        with open(filepath, 'w', encoding='utf-8', newline='\n') as f:
            f.write(content)
        print('Updated ' + filepath)
    else:
        print('Skipped or not found in ' + filepath)

files = [
    'resources/views/customer/shipments/create.blade.php',
    'resources/views/agent/shipments/create.blade.php',
    'resources/views/admin/shipments/create.blade.php'
]

for f in files:
    add_purpose_to_review(f)
