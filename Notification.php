<?php

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
    header('Location: /');
    die();
}

class Notification {
    /*
     * This is the a predefined notification utility
     */

    const ERROR = 0;
    const NOTIFY = 1;

    static function NotifyUtil() {
        $bs = new MigsPaymentGatewaySettings;
        $bs = $bs->getBankSettings();

        if ($bs->isEmpty()) {
            self::Notify("Please fill in the settings of Migs. <a href='admin.php?page=bank-migs-settings'>Settings</a>", self::ERROR, true);
        }
        if (strpos($bs->merchant, 'TEST') !== false) {
            self::Notify("Be Aware! your using a test account for Migs.", self::NOTIFY, true);
        }
    }

    static function Notify($value = '', $type = self::ERROR, $isecho = true) {
        self::showMessage($value, $type, $isecho);
    }

    static function showMessage($message, $type = self::ERROR, $isecho = true) {
        $t = time();
        switch ($type) {
            case self::NOTIFY:
                $cssClass = "updated";
                break;
            default:
                $cssClass = "error";
                break;
        }
        $errortxt = '<div id="message-' . $t . '" class="' . $cssClass . '"><p><strong>' . $message . '</strong></p></div>';
        if ($isecho == true) {
            echo $errortxt;
        } else {
            return $errortxt;
        }
    }
}
