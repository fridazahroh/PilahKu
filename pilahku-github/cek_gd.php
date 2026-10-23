
<?php
echo "File PHP aktif: " . php_ini_loaded_file() . "<br>";
echo "Ekstensi GD aktif: ";

if (extension_loaded('gd')) {
    echo "YA";
} else {
    echo "TIDAK";
}
?>
