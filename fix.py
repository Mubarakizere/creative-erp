import codecs

path = 'app/Http/Controllers/Admin/Inventory/InventoryReportController.php'

# Read file handling utf-16 or utf-8 fallback
try:
    with codecs.open(path, 'r', 'utf-16') as f:
        content = f.read()
except:
    with codecs.open(path, 'r', 'utf-8') as f:
        content = f.read()

# Replace currency
content = content.replace("'$' .", "'RWF ' .")
content = content.replace("['$', ',']", "['RWF', ' ', ',']")

# Write back strictly as utf-8
with codecs.open(path, 'w', 'utf-8') as f:
    f.write(content)

print("Encoding and currency fixed!")
