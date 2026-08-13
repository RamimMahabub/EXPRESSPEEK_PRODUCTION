import os

def replace_in_file(filepath, replacements):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    original_content = content
    for old, new in replacements:
        content = content.replace(old, new)
        
    if content != original_content:
        with open(filepath, 'w', encoding='utf-8', newline='\n') as f:
            f.write(content)
        print('Updated ' + filepath)

# 1. invoice-pdf.blade.php
replace_in_file('resources/views/shipments/invoice-pdf.blade.php', [
    ('<title>Commercial Invoice {{ $shipmentInvoice->invoice_number }}</title>', '<title>{{ $shipment->shipment_purpose === \'Commercial\' ? \'Commercial\' : \'Proforma\' }} Invoice {{ $shipmentInvoice->invoice_number }}</title>'),
    ('<h1 class="document-title">Commercial Invoice</h1>', '<h1 class="document-title">{{ $shipment->shipment_purpose === \'Commercial\' ? \'Commercial\' : \'Proforma\' }} Invoice</h1>')
])

# 2. agent/shipments/invoice.blade.php
replace_in_file('resources/views/agent/shipments/invoice.blade.php', [
    ('<title>Commercial Invoice {{ $shipment->invoice_number }}</title>', '<title>{{ $shipment->shipment_purpose === \'Commercial\' ? \'Commercial\' : \'Proforma\' }} Invoice {{ $shipment->invoice_number }}</title>'),
    ('<h1 class="document-title">Commercial Invoice</h1>', '<h1 class="document-title">{{ $shipment->shipment_purpose === \'Commercial\' ? \'Commercial\' : \'Proforma\' }} Invoice</h1>')
])

# 3. customer/shipments/show.blade.php
replace_in_file('resources/views/customer/shipments/show.blade.php', [
    ('class="ap-button light">Commercial Invoice</a>', 'class="ap-button light">{{ $shipment->shipment_purpose === \'Commercial\' ? \'Commercial\' : \'Proforma\' }} Invoice</a>')
])

# 4. admin/shipments/edit.blade.php
replace_in_file('resources/views/admin/shipments/edit.blade.php', [
    ('transition-colors">Commercial Invoice</a>', 'transition-colors">{{ $shipment->shipment_purpose === \'Commercial\' ? \'Commercial\' : \'Proforma\' }} Invoice</a>')
])
