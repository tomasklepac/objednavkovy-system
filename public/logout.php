<?php
// ----------------------------------------------------
// LOGOUT uživatele
// ----------------------------------------------------

// 1) Spustíme session, abychom ji mohli zrušit
session_start();

// 2) Vymažeme všechny proměnné v $_SESSION
session_unset();

// 3) Ukončíme samotnou session na serveru
session_destroy();

// 4) Přesměrujeme uživatele zpět na login stránku
// (zde konkrétně na public/index.php)
header("Location: /objednavkovy-system/public/");
exit;
