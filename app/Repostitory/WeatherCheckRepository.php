<?php

namespace App\Repository;

use App\Community;
use App\CommunityUser;
use App\MacAddress;
use App\TalkMessage;

/**
 *
 */
class WeatherCheckRepository
{
    private $community;
    private $communityUser;
    private $macAddress;
    private $talkMessage;

    public function __construct(
        Community       $community,
        CommunityUser   $communityUser,
        MacAddress      $macAddress,
        TalkMessage     $talkMessage
        )
    {
        $this->community     = $community;
        $this->communityUser = $communityUser;
        $this->macAddress    = $macAddress;
        $this->talkMessage   = $talkMessage;
    }

    /**
     * 天気APIが有効で滞在者のいるコミュニティの緯度経度とIDを配列で取得する
     */
    public function stayUserCommunitiesLocations()
    {
        return $this->communityUser
        ->select('community_id', 'latitude', 'longitude')
        ->Join(
            'mac_addresses',
            'mac_addresses.community_user_id', '=', 'community_user.id')
        ->Join(
            'communities',
            'communities.id', '=', 'community_user.community_id')
        ->where('current_stay', 1)
        ->where('google_home_enable', 1)
        ->where('google_home_weather_enable', 1)
        ->groupBy('community_id')
        ->get();
    }

    /**
     * 天気情報の発話をDBに収める
     */
    public function talkMessageSave(string $message, int $communityId)
    {
        $talkMessage = $this->talkMessage;
        // ひままずcommunityの最初のrouterに紐づいたGoogleHomeを対象にする
        $router = $this->community::find($communityId)
                    ->router()->orderBy('id')->first();
        $talkMessage->router_id       = $router->id;
        $talkMessage->talking_message = $message;
        $talkMessage->save();
    }
}
