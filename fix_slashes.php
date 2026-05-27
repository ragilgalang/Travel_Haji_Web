$file = 'resources/views/admin/settings.blade.php';
$content = file_get_contents($file);
$content = str_replace("'/\\''", "'/'", $content);
$content = str_replace("'/\\\\'", "'/'", $content);
file_put_contents($file, $content);
echo "Fixed slashes";
