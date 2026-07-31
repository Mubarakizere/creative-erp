import re

with open('resources/views/components/sidebar.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

content = content.split('{{-- Mobile Sidebar --}}')[0]

# Extract blocks separated by dividers
dividers = re.split(r'<div x-show="sidebarOpen"[^>]*>.*?<p[^>]*>(.*?)</p>.*?</div>', content, flags=re.DOTALL)

print("Main Group:")
main_content = dividers[0]
links = re.findall(r'(?:@(canany?)\(\[?([^\]]+)\]?\)\s*)?<a href=\"(.*?)\".*?<span[^>]*>(.*?)</span>', main_content, re.DOTALL)
for link in links:
    print(f"  {link}")

for i in range(1, len(dividers), 2):
    group_name = dividers[i].strip()
    group_content = dividers[i+1]
    print(f"\nGroup: {group_name}")
    links = re.findall(r'(?:@(canany?)\(\[?([^\]]+)\]?\)\s*)?<a href=\"(.*?)\".*?(?:<span[^>]*>|<span>)(.*?)</span>', group_content, re.DOTALL)
    for link in links:
        print(f"  {link}")

