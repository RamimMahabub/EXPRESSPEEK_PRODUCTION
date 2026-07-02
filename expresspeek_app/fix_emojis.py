import re

def process(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    content = content.replace('dY",', '??')
    content = content.replace('??', '??') # just in case

    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)

process(r'resources/views/agent/shipments/create.blade.php')
process(r'resources/views/customer/shipments/create.blade.php')
