import zipfile
import os

folders_to_zip = [
    "resources/views/admin",
    "resources/views/agent",
    "resources/views/components",
    "resources/views/layouts"
]

zip_path = "full_panel_update.zip"

with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zipf:
    for folder in folders_to_zip:
        for root, dirs, files in os.walk(folder):
            for file in files:
                file_path = os.path.join(root, file)
                zipf.write(file_path, arcname=file_path)
                print(f"Added {file_path}")

print(f"Successfully created {zip_path}")
