import os
import glob

# Modify app.css
app_css_path = r"c:\Users\Admin\Downloads\expresspeek_app\expresspeek_app\resources\css\app.css"
with open(app_css_path, "a", encoding="utf-8") as f:
    f.write("""
@theme {
  --color-slate-950: #0a0f1c;
}

@layer utilities {
  .neon-card {
    @apply bg-slate-900/40 backdrop-blur-xl border border-white/10 shadow-2xl transition-all duration-300;
  }
  .neon-card:hover {
    @apply border-violet-500/50 shadow-[0_0_25px_rgba(139,92,246,0.25)] -translate-y-1;
  }
  .neon-button {
    @apply bg-gradient-to-r from-violet-600 to-fuchsia-600 border border-white/20 text-white font-semibold transition-all duration-300 shadow-[0_0_15px_rgba(139,92,246,0.3)];
  }
  .neon-button:hover {
    @apply shadow-[0_0_25px_rgba(192,38,211,0.5)] scale-[1.02];
  }
  .neon-text {
    @apply text-fuchsia-400 drop-shadow-[0_0_8px_rgba(192,38,211,0.8)];
  }
  .glass-panel {
    @apply bg-slate-900/60 backdrop-blur-2xl border border-white/10;
  }
}
""")

paths = [
    "resources/views/admin/**/*.blade.php",
    "resources/views/agent/**/*.blade.php",
    "resources/views/customer/**/*.blade.php",
    "resources/views/components/*.blade.php",
    "resources/views/layouts/*.blade.php"
]

base_dir = r"c:\Users\Admin\Downloads\expresspeek_app\expresspeek_app"
files_to_process = []
for p in paths:
    full_pattern = os.path.join(base_dir, p)
    files = glob.glob(full_pattern, recursive=True)
    files_to_process.extend(files)

replacements = {
    # Layouts & Backgrounds
    "bg-gray-50 text-gray-900": "bg-slate-950 text-slate-100 relative overflow-x-hidden",
    "bg-gray-50": "bg-transparent", # Because we want the body background to show
    "bg-white/80": "bg-slate-900/40",
    "bg-gray-900/50 backdrop-blur-sm": "bg-slate-950/80 backdrop-blur-md", # Overlays
    
    # Sidebars & Navbars
    "bg-white border-r border-gray-200": "glass-panel border-r-white/10 shadow-[5px_0_25px_rgba(0,0,0,0.5)]",
    "bg-gray-50/50": "bg-white/5",
    "border-b border-gray-100": "border-b border-white/10",
    "border-b border-gray-200": "border-b border-white/10",
    "border-t border-gray-100": "border-t border-white/10",
    "border-gray-200": "border-white/10",
    
    # Cards
    "bg-white border border-gray-200 shadow-sm transition-colors hover:border-violet-600/40": "neon-card",
    "bg-white border border-gray-200 shadow-sm transition-colors hover:border-yellow-600/40": "neon-card",
    "bg-white border border-gray-200 shadow-sm transition-colors hover:border-blue-600/40": "neon-card",
    "bg-white border border-gray-200 shadow-sm transition-colors hover:border-emerald-600/40": "neon-card",
    "bg-white border border-gray-200 shadow-sm transition-colors hover:border-amber-600/40": "neon-card",
    "bg-white border border-gray-200 shadow-sm": "neon-card",
    "bg-white border border-gray-200": "neon-card",
    
    # Typography
    "text-gray-900 font-bold": "text-white font-bold",
    "text-gray-900": "text-slate-100",
    "text-gray-700": "text-slate-300",
    "text-gray-600": "text-slate-400",
    "text-gray-500": "text-slate-400",
    
    # Buttons
    "bg-violet-600 hover:bg-violet-500 text-white": "neon-button",
    "bg-violet-600 hover:bg-violet-500": "neon-button",
    "bg-gray-100 hover:bg-gray-200 text-sm text-gray-700": "bg-white/10 hover:bg-white/20 text-sm text-slate-200 border border-white/10 shadow-lg backdrop-blur-md",
    "bg-gray-100 hover:bg-gray-700": "bg-white/10 hover:bg-white/20 border border-white/10",
    
    # Inputs
    "bg-white border-gray-300 focus:border-violet-500": "bg-black/20 border-white/10 focus:border-fuchsia-500 focus:ring-fuchsia-500/50 text-white placeholder-slate-500 shadow-inner",
    "bg-white border-gray-300": "bg-black/20 border-white/10 text-white placeholder-slate-500",
    "bg-gray-100": "bg-white/5",
    "border-gray-300": "border-white/10",
    
    # Modals
    "bg-white rounded-2xl": "glass-panel rounded-2xl shadow-[0_0_40px_rgba(0,0,0,0.5)]",
    
    # Tables / Dividers
    "divide-gray-100": "divide-white/5",
    "divide-gray-200": "divide-white/5",
    "hover:bg-gray-50": "hover:bg-white/5",
    
    # Accents & Text
    "text-violet-600": "neon-text",
    
    # Fix black text in CTA buttons
    "text-gray-900 font-semibold": "text-white font-bold",
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
            if "<body" in content and orb_html not in content:
                content = content.replace("<body class=\"h-full bg-gray-50 text-gray-900\">", f"<body class=\"h-full bg-slate-950 text-slate-100 relative overflow-x-hidden\">\n{orb_html}")
                content = content.replace("<body class=\"h-full bg-slate-950 text-slate-100 relative overflow-x-hidden\">", f"<body class=\"h-full bg-slate-950 text-slate-100 relative overflow-x-hidden\">\n{orb_html}") # in case it was already replaced
            
        for old, new in replacements.items():
            content = content.replace(old, new)
            
        if content != original_content:
            with open(file_path, 'w', encoding='utf-8') as f:
                f.write(content)
                
    except Exception as e:
        print(f"Error processing {file_path}: {e}")

print("Neon UI replacement complete.")
