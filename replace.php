$file = 'resources/views/admin/settings.blade.php';
$content = file_get_contents($file);
$content = preg_replace('/src="\{\{\s*\\$settings\[\'([^\']+)\'\]\s*\}\}"/', 'src="{{ asset(ltrim($settings['.'\'\''.'], \'/\\')) }}"', $content);
$content = preg_replace('/src="\{\{\s*\\$settings\[\'hero_bg_\'\s*\.\s*\\$i\]\s*\}\}"/', 'src="{{ asset(ltrim($settings[\'hero_bg_\' . $i], \'/\\')) }}"', $content);
file_put_contents($file, $content);
echo "Replaced properly";
