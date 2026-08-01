import glob
from pathlib import Path

files = glob.glob('expresspeek_app/resources/views/**/*.blade.php', recursive=True)
count = 0
for f in files:
    content = Path(f).read_text(encoding='utf-8')
    if '01XXXXXXXXX' in content:
        Path(f).write_text(content.replace('01XXXXXXXXX', '1968524222'), encoding='utf-8')
        count += 1
print(f'Replaced in {count} files')
