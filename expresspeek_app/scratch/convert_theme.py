import re

path = r"c:\Users\Admin\Downloads\expresspeek_app\expresspeek_app\resources\views\customer\shipments\create.blade.php"
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Routes and includes
content = content.replace("@extends('layouts.dashboard')", "@extends('layouts.customer_panel')")
content = content.replace("agent.partials.contact-form", "customer.partials.contact-form")
content = content.replace("route('agent.dashboard')", "route('customer.dashboard')")
content = content.replace('route("agent.address-book.index"', 'route("customer.address-book.api.index"')
content = content.replace('route("agent.address-book.store"', 'route("customer.address-book.api.store"')
content = content.replace("route('agent.carriers.available'", "route('customer.carriers.api.index'")
content = content.replace('route("agent.shipments.store"', 'route("customer.shipments.store"')

# Light theme transformations
replacements = {
    "bg-gray-900 border border-gray-800": "bg-white border border-gray-200 shadow-sm",
    "text-white": "text-gray-900 font-bold",
    "bg-gray-800 z-0": "bg-gray-200 z-0",
    "bg-gray-900 border-gray-700 text-gray-500": "bg-white border-gray-300 text-gray-400",
    "text-gray-400": "text-gray-500 font-medium",
    "bg-gray-700 rounded-2xl": "bg-gray-50 rounded-2xl",
    "border-gray-700": "border-gray-200",
    "bg-gray-800 border border-gray-700": "bg-white border border-gray-200",
    "bg-gray-800/50 border border-gray-700/50": "bg-gray-50 border border-gray-200",
    "bg-gray-900 border border-gray-700": "bg-white border border-gray-200",
    "text-gray-300": "text-gray-700",
    "bg-gray-800/50": "bg-gray-50",
    "bg-gray-800/60": "bg-gray-100",
    "border-gray-800": "border-gray-100",
    "divide-gray-800/50": "divide-gray-100",
    "border-b border-gray-800": "border-b border-gray-100",
    "bg-gray-950": "bg-white",
    "bg-gray-900/80": "bg-gray-50",
}

for old, new in replacements.items():
    content = content.replace(old, new)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Transformations applied.")
