import os
import glob
import re

paths = [
    "resources/views/admin/**/*.blade.php",
    "resources/views/agent/**/*.blade.php",
    "resources/views/customer/**/*.blade.php",
    "resources/views/components/*.blade.php"
]

base_dir = r"c:\Users\Admin\Downloads\expresspeek_app\expresspeek_app"

files_to_process = []
for p in paths:
    full_pattern = os.path.join(base_dir, p)
    files = glob.glob(full_pattern, recursive=True)
    files_to_process.extend(files)

replacements = {
    # Fix the broken cards that were left as bg-white
    "bg-white border border-white/10 shadow-sm": "neon-card",
    "bg-white border border-white/10": "neon-card",
    "bg-white border border-gray-200": "neon-card",
    "bg-white rounded-2xl": "neon-card rounded-2xl",
    "bg-white": "bg-slate-900/40", # Catch any stray bg-whites, unless it's a specific button
    
    # Badges - they were mapped to light theme (bg-emerald-100), need them dark and glowing
    "bg-emerald-100 text-emerald-700": "bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-[0_0_10px_rgba(16,185,129,0.2)]",
    "bg-blue-100 text-blue-700": "bg-blue-500/10 text-blue-400 border border-blue-500/20 shadow-[0_0_10px_rgba(59,130,246,0.2)]",
    "bg-purple-100 text-purple-700": "bg-purple-500/10 text-purple-400 border border-purple-500/20 shadow-[0_0_10px_rgba(168,85,247,0.2)]",
    "bg-amber-100 text-amber-700": "bg-amber-500/10 text-amber-400 border border-amber-500/20 shadow-[0_0_10px_rgba(245,158,11,0.2)]",
    "bg-gray-100 text-gray-600": "bg-slate-800 text-slate-300 border border-slate-700 shadow-[0_0_10px_rgba(148,163,184,0.1)]",
    "bg-gray-100": "bg-white/5",
    
    # Fix buttons that got messed up in agent edit
    "text-emerald-700 border border-emerald-200 hover:border-emerald-300 hover:bg-emerald-50": "text-emerald-400 border border-emerald-500/30 hover:border-emerald-400 hover:bg-emerald-500/10 shadow-[0_0_10px_rgba(16,185,129,0.1)]",
    "text-blue-700 border border-blue-200 hover:border-blue-300 hover:bg-blue-50": "text-blue-400 border border-blue-500/30 hover:border-blue-400 hover:bg-blue-500/10 shadow-[0_0_10px_rgba(59,130,246,0.1)]",
    
    # Hover states for rows
    "hover:bg-transparent": "hover:bg-white/5",
    
    # Inputs
    "bg-black/20 px-4 py-3 text-sm text-slate-100": "bg-black/40 px-4 py-3 text-sm text-slate-100", # Better contrast for inputs
}

for file_path in set(files_to_process):
    if "invoice.blade.php" in file_path or "waybill.blade.php" in file_path:
        continue
    
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
            
        original_content = content
        
        for old, new in replacements.items():
            content = content.replace(old, new)
            
        if content != original_content:
            with open(file_path, 'w', encoding='utf-8') as f:
                f.write(content)
                
    except Exception as e:
        print(f"Error processing {file_path}: {e}")

print("Neon UI fixes complete.")
