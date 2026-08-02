import os
import re
from pathlib import Path

def clean_alerts(root_dir):
    # Regex to match blocks like:
    # @if(session('success'))
    #   <something>{{ session('success') }}</something>
    # @endif
    # Also for error, info, warning
    
    pattern = re.compile(
        r'([ \t]*)@if\s*\(\s*session\([\'"](success|error|info|warning|status)[\'"]\)\s*\).*?@endif[ \t]*\n?',
        re.DOTALL
    )
    
    count = 0
    for path in Path(root_dir).rglob('*.blade.php'):
        with open(path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # We only want to remove it if it looks like an inline banner, not if it's our new global script
        # So we can exclude the layout file itself or any file that uses it inside <script>
        if "components/layouts/app.blade.php" in str(path).replace('\\', '/'):
            continue
            
        new_content, num_subs = pattern.subn('', content)
        
        if num_subs > 0:
            with open(path, 'w', encoding='utf-8') as f:
                f.write(new_content)
            print(f"Cleaned {num_subs} alerts in {path}")
            count += num_subs
            
    print(f"Total alerts removed: {count}")

if __name__ == '__main__':
    clean_alerts('resources/views')
