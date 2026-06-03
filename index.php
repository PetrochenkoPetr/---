<?php
require_once __DIR__ . '/includes/config.php';

if (isAuthenticated()) {
    header('Location: profile.php');
} else {
    header('Location: login.php');
}
exit;
