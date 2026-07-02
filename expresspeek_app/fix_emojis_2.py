import re

def process(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    content = content.replace('dY"', '??')
    
    # Just to be 100% sure we fix the exact string
    # Let's replace the whole div just in case
    content = re.sub(r'<div class="text-4xl">.*?</div>', lambda m: '<div class="text-4xl">??</div>' if 'document' in m.string[m.start()-50:m.end()] else m.group(0), content)

    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)

process(r'resources/views/agent/shipments/create.blade.php')
process(r'resources/views/customer/shipments/create.blade.php')
