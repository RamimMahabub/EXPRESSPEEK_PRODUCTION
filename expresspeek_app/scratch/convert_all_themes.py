import os
import glob

# Paths to process
paths = [
    "resources/views/admin/**/*.blade.php",
    "resources/views/agent/**/*.blade.php",
    "resources/views/components/sidebar.blade.php",
    "resources/views/components/navbar.blade.php"
]

base_dir = r"c:\Users\Admin\Downloads\expresspeek_app\expresspeek_app"

files_to_process = []
for p in paths:
    full_pattern = os.path.join(base_dir, p)
    files = glob.glob(full_pattern, recursive=True)
    files_to_process.extend(files)

replacements = {
    # Layout structural
    "bg-gray-950 text-gray-100": "bg-gray-50 text-gray-900",
    "bg-gray-950/80": "bg-gray-900/50",
    "bg-gray-950": "bg-white",
    
    # Sidebars / Navbars
    "bg-gray-900 border-r border-gray-800": "bg-white border-r border-gray-200",
    "bg-gray-900/80": "bg-gray-50",
    "border-b border-gray-800": "border-b border-gray-100",
    "bg-gray-800/40": "bg-gray-50/50",
    "bg-gray-800/50": "bg-gray-50",
    "border-gray-800": "border-gray-100",
    "hover:bg-red-900/20 hover:text-red-400": "hover:bg-red-50 hover:text-red-600",
    
    # Panels & Cards
    "bg-gray-900 border border-gray-800": "bg-white border border-gray-200 shadow-sm",
    "bg-gray-900 border border-gray-700": "bg-white border border-gray-200 shadow-sm",
    "bg-gray-800 border border-gray-700": "bg-white border border-gray-200",
    "bg-gray-800/50 border border-gray-700/50": "bg-gray-50 border border-gray-200",
    "bg-gray-900 border-gray-700 text-gray-500": "bg-white border-gray-300 text-gray-400",
    
    # Typography
    "text-white": "text-gray-900 font-bold",
    "text-gray-100": "text-gray-900 font-bold",
    "text-gray-300": "text-gray-700",
    "text-gray-400": "text-gray-500",
    "text-gray-600": "text-gray-500",
    
    # Specific UI elements
    "bg-gray-700 rounded-2xl": "bg-gray-50 rounded-2xl",
    "border-gray-700": "border-gray-200",
    "bg-gray-800 z-0": "bg-gray-200 z-0",
    "bg-gray-800/60": "bg-gray-100",
    "divide-gray-800/50": "divide-gray-100",
    "divide-gray-800": "divide-gray-100",
    "hover:bg-gray-800": "hover:bg-gray-50",
    "hover:bg-gray-800/50": "hover:bg-gray-50",
    "border-t border-gray-800": "border-t border-gray-100",
}

for file_path in files_to_process:
    # Skip print views since they are already light/printable
    if "invoice.blade.php" in file_path or "waybill.blade.php" in file_path:
        continue
        
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
        
    for old, new in replacements.items():
        content = content.replace(old, new)
        
    # Extra fix for inputs which might need text-gray-900 explicitly if they had text-white
    content = content.replace("text-gray-900 font-bold", "text-gray-900")
    
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)
    
print(f"Processed {len(files_to_process)} files.")
