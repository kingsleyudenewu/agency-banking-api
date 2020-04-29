<?php

namespace App\Koloo\Transformers;

use App\Koloo\Message;

/**
 * Class MessageTransformer
 *
 * @package \App\Koloo\Transformers
 */
class MessageTransformer
{

    public static function transform(Message $message, array $scopes = []): array
    {
        return [
            'id' => $message->getId(),
            'subject' => $message->getSubject(),
            'message' => $message->getMessage(),
            'message_type' => $message->getMessageType(),
            'status' => $message->getStatus(),
            'user_id' => $message->getUserId(),
            'created_at' => $message->getCreatedAt(),
        ];
    }
}
