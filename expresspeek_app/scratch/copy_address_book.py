import os

source_file = r'c:\Users\Admin\Downloads\expresspeek_app\expresspeek_app\resources\views\agent\address-book.blade.php'
dest_file = r'c:\Users\Admin\Downloads\expresspeek_app\expresspeek_app\resources\views\customer\address-book.blade.php'

with open(source_file, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace("route('agent.shipments.create')", "route('customer.shipments.create')")
content = content.replace('route("agent.address-book.index"', 'route("customer.address-book.api.index"')
content = content.replace("`/agent/address-book/${id}`", "`/customer/api/address-book/${id}`")
content = content.replace('route("agent.address-book.store"', 'route("customer.address-book.api.store"')

os.makedirs(os.path.dirname(dest_file), exist_ok=True)
with open(dest_file, 'w', encoding='utf-8') as f:
    f.write(content)

print("Created customer address-book.blade.php")
