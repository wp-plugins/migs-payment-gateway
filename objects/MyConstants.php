<?php

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
    header('Location: /');
    die();
}

class MyConstants {
    const PREFIX = "migs_payment_gateway";
    
}
