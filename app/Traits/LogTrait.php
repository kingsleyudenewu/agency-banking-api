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


    public function log(string $message, array $context = []): self
    {
        Log::channel('koloo')->info($message, $context);

        return $this;
    }
}
