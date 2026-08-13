import sys

def add_purpose_validation(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    target_str = "'document_description'   => 'nullable|string|max:500',"
    replacement = "'document_description'   => 'nullable|string|max:500',\n            'shipment_purpose'       => 'nullable|string|max:191',"
    
    if target_str in content and "'shipment_purpose'" not in content:
        content = content.replace(target_str, replacement)
        with open(filepath, 'w', encoding='utf-8', newline='\n') as f:
            f.write(content)
        print('Updated ' + filepath)
    else:
        print('Skipped or not found in ' + filepath)

files = [
    'app/Http/Controllers/Customer/ShipmentController.php',
    'app/Http/Controllers/Agent/ShipmentController.php',
    'app/Http/Controllers/Admin/ShipmentController.php',
    'app/Http/Controllers/PublicShipmentController.php'
]

for f in files:
    add_purpose_validation(f)
