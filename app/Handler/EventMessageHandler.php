<?php 

namespace App\Handler;

use EasyWeChat\Kernel\Contracts\EventHandlerInterface;

class EventMessageHandler implements EventHandlerInterface
{
    public function handle($payload) 
    {
        // Log::info($payload);
    }
}