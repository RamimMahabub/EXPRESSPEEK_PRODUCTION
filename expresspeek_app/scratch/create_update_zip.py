import zipfile
import os

zip_path = r'c:\Users\Admin\Downloads\expresspeek_app\expresspeek_app\customer_panel_update.zip'

with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zipf:
    # Add resources/views
    for root, dirs, files in os.walk('resources/views'):
        for file in files:
            file_path = os.path.join(root, file)
            zipf.write(file_path, arcname=file_path)
            
    # Add routes/web.php
    if os.path.exists('routes/web.php'):
        zipf.write('routes/web.php', arcname='routes/web.php')
        
print("Zip created successfully.")
