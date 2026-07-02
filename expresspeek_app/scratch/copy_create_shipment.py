import os

source_file = r'c:\Users\Admin\Downloads\expresspeek_app\expresspeek_app\resources\views\agent\shipments\create.blade.php'
dest_file = r'c:\Users\Admin\Downloads\expresspeek_app\expresspeek_app\resources\views\customer\shipments\create.blade.php'

with open(source_file, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace("route('agent.dashboard')", "route('customer.dashboard')")
content = content.replace('route("agent.address-book.index"', 'route("customer.address-book.api.index"')
content = content.replace("route('agent.carriers.available'", "route('customer.carriers.api.index'")
content = content.replace('route("agent.shipments.store"', 'route("customer.shipments.store"')
content = content.replace('route("agent.address-book.store"', 'route("customer.address-book.api.store"')

os.makedirs(os.path.dirname(dest_file), exist_ok=True)
with open(dest_file, 'w', encoding='utf-8') as f:
    f.write(content)

print("Created customer create.blade.php")
