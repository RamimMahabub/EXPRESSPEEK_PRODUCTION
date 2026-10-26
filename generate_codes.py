import json
import re

agent_file = 'expresspeek_app/resources/views/agent/partials/contact-form.blade.php'
with open(agent_file, 'r', encoding='utf-8') as f:
    content = f.read()

countries_match = re.search(r'window\.COUNTRIES = \[(.*?)\];', content, re.DOTALL)
phone_codes_match = re.search(r'window\.PHONE_CODES = \[(.*?)\];', content, re.DOTALL)

countries = []
for m in re.finditer(r"{name:'(.*?)',code:'(.*?)'}", countries_match.group(1)):
    countries.append({'name': m.group(1), 'code': m.group(2)})

phone_codes = {}
for m in re.finditer(r"{code:'(.*?)',dial:'(.*?)'}", phone_codes_match.group(1)):
    phone_codes[m.group(1)] = m.group(2)

def get_flag(code):
    return chr(ord(code[0]) + 127397) + chr(ord(code[1]) + 127397)

all_codes = []
for c in countries:
    code = c['code']
    if code in phone_codes:
        dial = phone_codes[code]
        flag = get_flag(code)
        name = c['name'].replace("'", "\\'")
        all_codes.append(f"{{c:'{dial}',n:'{name}',f:'{flag}'}}")

with open('full_codes.txt', 'w', encoding='utf-8') as out:
    out.write('[\n    ' + ',\n    '.join([', '.join(all_codes[i:i+4]) for i in range(0, len(all_codes), 4)]) + '\n]')

phone_codes_obj = "{" + ",".join([f"{k}:'{v}'" for k, v in phone_codes.items()]) + "}"
with open('phone_codes_obj.txt', 'w', encoding='utf-8') as out2:
    out2.write(phone_codes_obj)
