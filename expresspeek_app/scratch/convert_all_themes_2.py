import os
import glob

paths = [
    "resources/views/admin/**/*.blade.php",
    "resources/views/agent/**/*.blade.php"
]

base_dir = r"c:\Users\Admin\Downloads\expresspeek_app\expresspeek_app"

files_to_process = []
for p in paths:
    full_pattern = os.path.join(base_dir, p)
    files = glob.glob(full_pattern, recursive=True)
    files_to_process.extend(files)

replacements = {
    "bg-gray-900 border border-gray-100": "bg-white border border-gray-200 shadow-sm",
    
    # Badges (Dark to Light)
    "bg-emerald-900/40 text-emerald-400": "bg-emerald-100 text-emerald-700",
    "bg-blue-900/40 text-blue-400": "bg-blue-100 text-blue-700",
    "bg-purple-900/40 text-purple-400": "bg-purple-100 text-purple-700",
    "bg-yellow-900/40 text-yellow-400": "bg-amber-100 text-amber-700",
    "bg-gray-800 text-gray-500": "bg-gray-100 text-gray-600",
    "bg-amber-900/40 text-amber-400": "bg-amber-100 text-amber-700",

    # Buttons
    "text-emerald-300 border border-emerald-600/40 hover:border-emerald-500 hover:text-emerald-200": "text-emerald-700 border border-emerald-200 hover:border-emerald-300 hover:bg-emerald-50 transition-colors",
    "text-blue-300 border border-blue-600/40 hover:border-blue-500 hover:text-blue-200": "text-blue-700 border border-blue-200 hover:border-blue-300 hover:bg-blue-50 transition-colors",
    
    # Generic missing ones
    "bg-gray-950": "bg-white",
    "text-gray-100": "text-gray-900 font-bold",
    "bg-gray-800": "bg-gray-100",
    "text-violet-400": "text-violet-600",
    "text-violet-300": "text-violet-700",
}

for file_path in files_to_process:
    if "invoice.blade.php" in file_path or "waybill.blade.php" in file_path:
        continue
    
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
        
    for old, new in replacements.items():
        content = content.replace(old, new)
        
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)
        
print("Second pass complete.")
