<?php
/**
 * Logout - ProjectHub
 * Projeto SSI/IP - InovaSoft
 */

require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/helpers.php';

logout_user();
redirect('index.php');

