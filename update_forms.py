import re

def update_file(filepath, is_admin=False):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    with open('full_codes.txt', 'r', encoding='utf-8') as f:
        full_codes = f.read()

    with open('phone_codes_obj.txt', 'r', encoding='utf-8') as f:
        phone_codes_obj = f.read()

    # Replace allCodes array
    content = re.sub(
        r'get allCodes\(\) \{\s*return \[(.*?)\];\s*\},',
        f'get allCodes() {{\n                    return {full_codes};\n                }},',
        content,
        flags=re.DOTALL
    )

    if is_admin:
        # Replace phoneCodes object inside selectCountry
        content = re.sub(
            r'const phoneCodes = \{(.*?)\};',
            f'const phoneCodes = {phone_codes_obj};',
            content,
            flags=re.DOTALL
        )
    
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)

update_file('expresspeek_app/resources/views/admin/partials/contact-form.blade.php', is_admin=True)
update_file('expresspeek_app/resources/views/agent/partials/contact-form.blade.php', is_admin=False)

print("Updated forms successfully.")
