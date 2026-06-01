<?php
$a = null;
$b = $a[0]['latitude'] ?? 'default';
echo "Success: $b\n";
