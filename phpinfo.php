<?php
echo "<!DOCTYPE html>\n";
echo "<html><head><title>PHP Test</title></head><body>";
echo "<h1>PHP is Working!</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Current Time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p><a href='/hms/test.html'>Back to Test Page</a></p>";
echo "<hr>";
echo "<h2>PHP Info:</h2>";
phpinfo();
echo "</body></html>";
?>
