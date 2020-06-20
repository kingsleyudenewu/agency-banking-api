<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

/**
 * Class LogTrait
 *
 * @package \App\Traits
 */
trait LogTrait
{


    protected $logChannel = 'koloo';


    protected function logInfo($message, array $context = []): self
    {
        return $this->log('info', $message, $context);
    }

    protected function logError(string $message = '', array $context = []): self
    {
        return $this->log('error', $message, $context);
    }

    protected function log(string $type, string $message, array $context = []): self
    {
        Log::channel('koloo')->{$type}($message, $context);

        return $this;
    }
}
