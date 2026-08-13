import sys

def add_purpose_dropdown(filepath, target_str):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    replacement = r'''                {{-- Non-document items --}}
                <div id="itemsSection" class="">
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-400 mb-2">
                            What is the purpose of your shipment? 
                            <span class="inline-block text-violet-400 ml-1 cursor-help" title="Select the main purpose of your shipment for customs declarations">
                                <svg class="w-4 h-4 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                            </span>
                        </label>
                        <select name="shipment_purpose" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-violet-500 transition-colors appearance-none" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394A3B8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                            <option value="Gift">Gift</option>
                            <option value="Commercial">Commercial</option>
                            <option value="Personal, Not for Resale">Personal, Not for Resale</option>
                            <option value="Sample">Sample</option>
                            <option value="Return for Repair">Return for Repair</option>
                            <option value="Return after Repair">Return after Repair</option>
                            <option value="Return to Seller">Return to Seller</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-semibold text-white">Item Details</h4>
                    </div>'''
    
    if target_str in content:
        content = content.replace(target_str, replacement)
        with open(filepath, 'w', encoding='utf-8', newline='\n') as f:
            f.write(content)
        print('Updated ' + filepath)
    else:
        print('Target string not found in ' + filepath)

target = '''                {{-- Non-document items --}}
                <div id="itemsSection" class="">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-semibold text-white">Item Details</h4>
                    </div>'''

# Adjust for potential Windows CRLF
target_crlf = target.replace('\n', '\r\n')

for filepath in ['resources/views/admin/shipments/create.blade.php', 'resources/views/agent/shipments/create.blade.php']:
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    if target in content:
        add_purpose_dropdown(filepath, target)
    elif target_crlf in content:
        add_purpose_dropdown(filepath, target_crlf)
    else:
        print(f"Not found in {filepath}")
