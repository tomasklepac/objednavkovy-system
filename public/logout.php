<?php
session_start();        // načteme session
session_unset();        // vymaže všechny proměnné v $_SESSION
session_destroy();      // zruší session úplně

// přesměrování zpět na login stránku
header("Location: /objednavkovy-system/public/");
exit;
