<?php
ini_set('session.cookie_path', '/');
session_start();

$_SESSION['test'] = 'Hello from page 1';

header('Location: page2.php');
exit;