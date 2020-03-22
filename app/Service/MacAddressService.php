<?php

namespace App\Service;

use DB;
use App\CommunityUser;
use App\MacAddress;
use App\Repository\MacAddressRepository;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class MacAddressService
{
    private $repository;

    public function __construct(
        MacAddressRepository $repository
    ) {
        $this->repository = $repository;
    }

    // IndexController サブクエリ用の滞在中端末を取得
    public function GetStayMacAddressForSubQuery()
    {
        return $this->repository->GetStayMacAddressForSubQuery();
    }

    public function PersonHavingGet(int $communityUserId, int $communityId)
    {
        return $this->repository->PersonHavingGet($communityUserId, $communityId);
    }

    public function CommunityHavingMac(int $communityId, int $readerId, string $order, string $key, string $case)
    {
        return 'App\CommunityUser'::CommunityHavingMac($communityId, $readerId, $order, $key, $case)->get();
    }

    public function MacIDtoGetCommunityID(int $macAddressId)
    {
        return 'App\CommunityUser'::MacIDtoGetCommunityID($macAddressId);
    }

    public function Update(int $macId, $vendor, $deviceName, bool $hide, string $now)
    {
        return $this->repository->Update($macId, $vendor, $deviceName, $hide, $now);
    }

    public function UpdateChangeOwner(int $macId, $vendor, $deviceName, bool $hide, string $now, int $communityUserId)
    {
        return $this->repository->UpdateChangeOwner($macId, $vendor, $deviceName, $hide, $now, $communityUserId);
    }

    public function UpdateProvisionalOwner(int $macId, int $oldCommunityUserId, int $newCommunityUserId, string $now)
    {
        return $this->repository->UpdateProvisionalOwner($macId, $oldCommunityUserId, $newCommunityUserId, $now);
    }

    // InportPostController MacAddress
    public function Arraival_at_Update(
        int $communityUserId,
        string $postMacHash,
        int $routerId,
        string $now
    ) {
        return $this->repository->ArraivalAtUpdate($communityUserId, $postMacHash, $routerId, $now);
    }

    // InportPostController MacAddress
    public function ThisUserExists(int $communityUserId)
    {
        return $this->repository->IsUserExists($communityUserId);
    }

    // InportPostController MacAddress
    public function MacAddressStatusUpdate(
        int $communityUserId,
        string $postMacHash,
        int $routerId,
        string $now
    ) {
        return $this->repository->MacAddressStatusUpdate($communityUserId, $postMacHash, $routerId, $now);
    }

    // mac_address.posted_at がnowからn分以内の curret stay 1 のcommunity_user_idとposted_atを取得する
    // UserStayLogController->stayCheck
    public function getRecentStayIdsAndMaxPostedAt(string $lastCheckDatetime)
    {
        return $this->repository->getRecentStayIdsAndMaxPostedAt($lastCheckDatetime);
    }

    public function nearArraivalExists(int $communityUserId, string $pastLimit)
    {
        return $this->repository->nearArraivalExists($communityUserId, $pastLimit);
    }

    /**
     * 端末が仮ユーザーか判定する
     * @param int $id  mac_addresses.id
     * @return object  int community_user_id
     * @return object  bool provisional
     */
    public function isDeviceProvisionUser(int $id)
    {
        return $this->repository->isDeviceProvisionUser($id);
    }
}
