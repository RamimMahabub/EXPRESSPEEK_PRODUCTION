import zipfile
import os

files_to_zip = [
    "app/Http/Controllers/Agent/ShipmentController.php",
    "app/Http/Controllers/Customer/ShipmentController.php",
    "app/Models/User.php",
    "app/Models/AddressBook.php",
    "database/migrations/2026_06_29_103253_rename_agent_id_to_user_id_in_address_book_table.php",
    "routes/customer.php",
    "routes/web.php",
    "resources/views/layouts/customer_panel.blade.php",
    "resources/views/components/customer_sidebar.blade.php",
    "resources/views/components/customer_navbar.blade.php",
    "resources/views/customer/dashboard.blade.php",
    "resources/views/customer/address-book.blade.php",
    "resources/views/customer/partials/contact-form.blade.php",
    "resources/views/customer/shipments/create.blade.php",
    "resources/views/customer/shipments/index.blade.php",
    "resources/views/customer/shipments/show.blade.php"
]

zip_path = "customer_panel_update_fixed.zip"

with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zipf:
    for file in files_to_zip:
        if os.path.exists(file):
            zipf.write(file, arcname=file) # arcname=file ensures it keeps the path structure
            print(f"Added {file}")
        else:
            print(f"Warning: {file} not found")

print(f"Successfully created {zip_path} with correct folder structure.")
