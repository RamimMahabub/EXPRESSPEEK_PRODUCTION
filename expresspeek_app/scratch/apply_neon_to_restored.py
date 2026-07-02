import os
import glob

paths = [
    "resources/views/**/*.blade.php"
]

base_dir = r"c:\Users\Admin\Downloads\expresspeek_app\expresspeek_app"

files_to_process = []
for p in paths:
    full_pattern = os.path.join(base_dir, p)
    files = glob.glob(full_pattern, recursive=True)
    files_to_process.extend(files)

replacements = {
    # Replace plain dark cards with neon-cards
    "bg-gray-900 border border-gray-800 shadow-sm": "neon-card",
    "bg-gray-900 border border-gray-800": "neon-card",
    
    # Text colors
    "text-gray-900": "text-slate-100",
    "text-gray-500": "text-slate-400",
    "text-gray-400": "text-slate-400",
    "text-gray-300": "text-slate-300",
    "text-gray-600": "text-slate-500",
    
    # Borders
    "border-gray-800": "border-white/10",
    "border-gray-100": "border-white/10",
    
    # Buttons
    "bg-violet-600 hover:bg-violet-500 text-white": "neon-button text-white",
    
    # Status badges
    "bg-emerald-900/40 text-emerald-400": "bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-[0_0_10px_rgba(16,185,129,0.2)]",
    "bg-blue-900/40 text-blue-400": "bg-blue-500/10 text-blue-400 border border-blue-500/20 shadow-[0_0_10px_rgba(59,130,246,0.2)]",
    "bg-purple-900/40 text-purple-400": "bg-purple-500/10 text-purple-400 border border-purple-500/20 shadow-[0_0_10px_rgba(168,85,247,0.2)]",
    "bg-yellow-900/40 text-yellow-400": "bg-amber-500/10 text-amber-400 border border-amber-500/20 shadow-[0_0_10px_rgba(245,158,11,0.2)]",
}

for file_path in set(files_to_process):
    if "invoice.blade.php" in file_path or "waybill.blade.php" in file_path:
        continue
    
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
            
        original_content = content
        
        # Inject glowing orbs background into layouts
        if "layouts\\dashboard.blade.php" in file_path or "layouts\\customer.blade.php" in file_path or "layouts\\customer_panel.blade.php" in file_path:
            orb_html = """
<div class="fixed inset-0 overflow-hidden pointer-events-none z-[-1]">
    <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] bg-violet-600/20 blur-[120px] rounded-full mix-blend-screen animate-pulse"></div>
    <div class="absolute top-[20%] -right-[10%] w-[40%] h-[60%] bg-fuchsia-600/20 blur-[120px] rounded-full mix-blend-screen animate-pulse" style="animation-delay: 2s;"></div>
    <div class="absolute -bottom-[20%] left-[20%] w-[60%] h-[50%] bg-blue-600/20 blur-[120px] rounded-full mix-blend-screen animate-pulse" style="animation-delay: 4s;"></div>
</div>
"""
            if "<body" in content and "animate-pulse" not in content:
                content = content.replace("<body class=\"h-full bg-gray-950 text-white font-sans\">", f"<body class=\"h-full bg-slate-950 text-slate-100 relative overflow-x-hidden\">\n{orb_html}")
                content = content.replace("<body class=\"h-full bg-gray-900 text-white font-sans antialiased\">", f"<body class=\"h-full bg-slate-950 text-slate-100 relative overflow-x-hidden antialiased\">\n{orb_html}")
            
        for old, new in replacements.items():
            content = content.replace(old, new)
            
        if content != original_content:
            with open(file_path, 'w', encoding='utf-8') as f:
                f.write(content)
                
    except Exception as e:
        print(f"Error processing {file_path}: {e}")

print("Neon UI applied to restored files.")
