<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;

class CustomizeFormatter
{
    /**
     * This keeps the daily logs from having 
     * write issues based on the owner of the file
     *
     * @param  \Illuminate\Log\Logger  $logger
     * @return void
     */
    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            if ($handler instanceof RotatingFileHandler) {
                $user = get_current_user();
                $sapi = php_sapi_name(); // Get the php handler (cli/apache2 handler)
                $handler->setFilenameFormat("{filename}-$user-$sapi-{date}", 'Y-m-d'); // And change the logging filename to include it
            }
        }
    }
}