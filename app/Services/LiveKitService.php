<?php

namespace App\Services;

use Agence104\LiveKit\AccessToken;
use Agence104\LiveKit\AccessTokenOptions;
use Agence104\LiveKit\VideoGrant;

class LiveKitService
{
    public function generateToken(string $roomName, int $userId, string $userName): string
    {
        $tokenOptions = (new AccessTokenOptions())
            ->setIdentity('user-'.$userId)
            ->setName($userName)
            ->setTtl(3600);

        $videoGrant = (new VideoGrant())
            ->setRoomJoin()
            ->setRoomName($roomName)
            ->setCanPublish()
            ->setCanSubscribe();

        return (new AccessToken(
            config('services.livekit.api_key'),
            config('services.livekit.api_secret'),
            $tokenOptions
        ))
            ->setGrant($videoGrant)
            ->toJwt();
    }

    public function getRoomName(int $conversationId): string
    {
        return 'conv-'.$conversationId;
    }
}
