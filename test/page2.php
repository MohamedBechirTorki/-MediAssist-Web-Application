<?php
ini_set('session.cookie_path', '/');
session_start();

if (isset($_SESSION['test'])) {
    echo "✅ Session OK: " . $_SESSION['test'];
} else {
    echo "❌ Session perdue !";
}
