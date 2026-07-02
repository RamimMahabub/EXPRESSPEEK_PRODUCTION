import re

with open("resources/views/agent/shipments/create.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

t1 = """<div class="absolute top-3 right-3 items-center justify-center w-6 h-6 rounded-full bg-violet-500 text-white shadow-sm hidden" data-shipment-type-badge="document">"""
print("Found T1:", t1 in content)
