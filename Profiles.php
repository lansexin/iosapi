<?php
/**
 * Created by PhpStorm.
 * User: lansy
 * Date: 2020/6/15
 * Time: 21:49
 *
获取Provisioning列表 普通版(不包含bundleId、certificates、devices信息)
GET https://api.appstoreconnect.apple.com/v1/profiles
获取Provisioning列表 加强版(包含bundleId、certificates、devices信息)
GET https://api.appstoreconnect.apple.com/v1/profiles?include=bundleId,certificates,devices
获取某个Provisioning devices详情(列表API+profiles id+devices)
GET https://api.appstoreconnect.apple.com/v1/profiles/295XXXXX/devices
获取某个Provisioning certificates详情
GET https://api.appstoreconnect.apple.com/v1/profiles/295XXXXX/certificates
获取某个Provisioning bundleId详情
GET https://api.appstoreconnect.apple.com/v1/profiles/295XXXXX/bundleId
 *
 */

namespace App\Http\Controllers\Iosapi;

class Profiles extends Apapi{
    public function __construct($acc_id){
        $this->acc = $acc_id;
    }
    /**
     * 创建Profile
    可选参数：
    fields[profiles]：bundleId, certificates, createdDate, devices, expirationDate, name, platform, profileContent（provisioning文件）, profileState, profileType, uuid
    fields[certificates]：certificateContent, certificateType, csrContent, displayName, expirationDate, name, platform, serialNumber
    fields[devices]：addedDate, deviceClass, model, name, platform, status, udid
    fields[bundleIds]：bundleIdCapabilities, identifier, name, platform, profiles, seedId
    filter[id]：
    filter[name]：
    filter[profileState]：ACTIVE, INVALID
    filter[profileType]：IOS_APP_DEVELOPMENT, IOS_APP_STORE, IOS_APP_ADHOC, IOS_APP_INHOUSE, MAC_APP_DEVELOPMENT, MAC_APP_STORE, MAC_APP_DIRECT, TVOS_APP_DEVELOPMENT, TVOS_APP_STORE, TVOS_APP_ADHOC, TVOS_APP_INHOUSE
    include：是否返回具体信息（默认不返回），bundleId, certificates, devices
    limit：最大200
    limit[certificates]：最大50
    limit[devices]：最大50
    sort：id, -id, name, -name, profileState, -profileState, profileType, -profileType
     *
     * @param $name
     * @param $profileType Possible values: IOS_APP_DEVELOPMENT, IOS_APP_STORE, IOS_APP_ADHOC, IOS_APP_INHOUSE,
     * MAC_APP_DEVELOPMENT, MAC_APP_STORE, MAC_APP_DIRECT, TVOS_APP_DEVELOPMENT, TVOS_APP_STORE, TVOS_APP_ADHOC,
     * TVOS_APP_INHOUSE
     * @param $bundleID
     * @param String $certificateID
     * @param String $deviceID
     * @return mixed|string
     */
    public function createProfile($name, $profileType, $bundleID, $certificateID, $deviceID){
        $certificates [] = [
            'id' => $certificateID,
            'type' => 'certificates'
        ];
        $devices [] = [
            'id' => $deviceID,
            'type' => 'devices'
        ];
        $params = [
            'data' => [
                'attributes' => [
                    'name' => $name,
                    'profileType' => $profileType
                ],
                'relationships' =>[
                    'bundleId' => [
                        'data' => [
                            'id' => $bundleID,
                            'type' => 'bundleIds'
                        ]
                    ],
                    'certificates' => [
                        'data' => $certificates
                    ],
                    'devices' => [
                        'data' => $devices
                    ],
                ],
                'type' => 'profiles',
            ]
        ];
        return $this->curlPost('/profiles',$params);
    }

    /**
     * 列出所有的Profile
     * @param array $query
     * @return array|mixed
     */
    public function listProfile(array $query = ['filter[profileType]'=>'IOS_APP_ADHOC','filter[profileState]'=>'ACTIVE']){
        return $this->curlGet('/profiles',$query);
    }


    /**
     * 删除某一个Profile
     * @param $id
     * @return array|mixed
     */
    public function deleteProfile($id){
        return $this->curlDel('/profiles/' . $id);
    }

    /**
     * 读取某一个profile
     * @param $id
     * @param array $query
     * @return mixed
     */
    public function readProfileInformation($id, array $query = []){
        return $this->curlGet('/profiles/' . $id);
    }


    /**
     * 获取某个Provisioning bundleId详情
     * @param $id
     * @param array $query
     * @return mixed
     */
    public function readBundleIDInProfile($id, array $query = []){
        return $this->curlGet('/profiles/' . $id . '/bundleId');
    }

    /**
     * 获取某个Provisioning Resource详情
     * @param $id
     * @param array $query
     * @return array|mixed
     */
    public function getBundleIDResourceInProfile($id, array $query = []){
        return $this->curlGet('/profiles/' . $id . '/relationships/bundleId');
    }

    /**
     * 获取某个Provisioning certificates详情
     * @param $id
     * @param array $query
     * @return mixed
     */
    public function listAllCertificatesInProfile($id, array $query = []){
        return $this->curlGet('/profiles/' . $id . '/certificates');
    }

    /**
     * 获取某个Provisioning  相关 certificates详情
     * @param $id
     * @param array $query
     * @return mixed
     */
    public function getAllCertificateIDsInProfile($id, array $query = []){
        return $this->curlGet('/profiles/' . $id . '/relationships/certificates');
    }

    /**
     * 获取某个Provisioning devices详情
     * @param $id
     * @param array $query
     * @return mixed
     */
    public function listAllDevicesInProfile($id, array $query = []){
        return $this->curlGet('/profiles/' . $id . '/devices');
    }

    /**
     * 获取某个Provisioning 相关的 devices详情
     * @param $id
     * @param array $query
     * @return array|mixed
     */
    public function getAllDeviceResourceIDsInProfile($id, array $query = []){
        return $this->curlGet('/profiles/' . $id . '/relationships/devices');
    }
}
