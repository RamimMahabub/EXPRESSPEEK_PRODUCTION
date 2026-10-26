import os
import re

directories_to_scan = [
    r"d:\projects\expresspeek_app\expresspeek_app\resources\views",
    r"d:\projects\expresspeek_app\expresspeek_app\resources\js"
]

extensions = ('.blade.php', '.js', '.vue', '.jsx')

replacements = [
    # Match Sourcing Requests, ignoring case, but avoid inside quotes or paths if possible.
    # Actually, a negative lookbehind/lookahead can prevent replacing inside URLs or single quotes.
    (re.compile(r"(?<!['\"/\-\.])\b(sourcing\s+requests?)\b(?!!?['\"/\-\.])", re.IGNORECASE), "Shop From Bangladesh"),
    (re.compile(r"(?<!['\"/\-\.])\b(quotes?)\b(?!!?['\"/\-\.])", re.IGNORECASE), "Check Delivery Cost"),
]

def process_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    original_content = content
    for pattern, replacement in replacements:
        def rep_func(match):
            # Special case for 'Quotes' vs 'quotes'
            original = match.group(1)
            
            if "request" in original.lower():
                return "Shop From Bangladesh"
            else:
                if original.lower().endswith('s'):
                    return "Check Delivery Costs" if original[0].isupper() else "check delivery costs"
                else:
                    return "Check Delivery Cost" if original[0].isupper() else "check delivery cost"
                
        content = pattern.sub(rep_func, content)

    if content != original_content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Updated: {filepath}")

for d in directories_to_scan:
    for root, dirs, files in os.walk(d):
        for file in files:
            if file.endswith(extensions):
                process_file(os.path.join(root, file))
