<?php

namespace Alerts;

class AlertService
{
    const SESSION_KEY = 'flash_alerts';

    public static function add(string $message, string $level = 'info'): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();

        $_SESSION[self::SESSION_KEY][] = [
            'message' => htmlspecialchars($message, ENT_QUOTES, 'UTF-8'),
            'level'   => in_array($level, ['success', 'info', 'warning', 'danger'])
                ? $level
                : 'info',
        ];
    }

    public static function display(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $alerts = $_SESSION[self::SESSION_KEY] ?? [];
        unset($_SESSION[self::SESSION_KEY]);

        foreach ($alerts as $alert) {
            echo '<div class="position-fixed bottom-0 start-50 translate-middle-x p-3" style="z-index:1050; pointer-events: none;">';
            foreach ($alerts as $i => $alert) {
                printf(
                    '<div id="toast-%1$d" class="toast align-items-center text-bg-%2$s border-0" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                        <div class="toast-body">%3$s</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>',
                    $i,
                    $alert['level'],
                    $alert['message']
                );
            }
            echo '</div>';
        }
    }
}
