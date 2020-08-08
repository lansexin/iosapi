<?php
/**
 * Created by PhpStorm.
 * User: lansy
 * Date: 2020/6/15
 * Time: 21:49
 */

namespace App\Http\Controllers\Iosapi;

class BundleID extends Apapi{
    public function __construct($acc_id){
        $this->acc = $acc_id;
    }
    /**
     * 设置包名
     * 列出所有的bandleid
     *
    fields[bundleIds]：attributes字段筛选显示结果，bundleIdCapabilities, identifier, name, platform, profiles, seedId
    fields[profiles]：profiles字段筛选显示结果，bundleId, certificates, createdDate, devices, expirationDate, name, platform, profileContent, profileState, profileType, uuid
    filter[id]：
    filter[identifier]：
    filter[name]：
    filter[platform]：IOS, MAC_OS
    filter[seedId]：
    include：bundleIdCapabilities, profiles
    limit：integer，最大200
    limit[profiles]：integer，最大50
    sort：id, -id, name, -name, platform, -platform, seedId, -seedId
    fields[bundleIdCapabilities]：bundleId, capabilityType, settings
     *
     * @param array $query
     * @return array|mixed
     */
    public function listBundleID(array $query = ['fields[bundleIds]'=>'name,identifier,platform']){
        return $this->curlGet('/bundleIds',$query);
    }

    /**
     * 获取已经存在的包名信息。
     * @param $indent
     * @return array|mixed
     */
    public function listOneBundleID($indent){
        $query = ['fields[bundleIds]'=>'name,identifier,platform','filter[identifier]'=>$indent];
        return $this->curlGet('/bundleIds',$query);
    }

    /**
     * 获取某一个信息。
     * @param $id
     * @param array $query
     * @return array|mixed
     */
    public function readBundleIdInformation($id, array $query = []){
        return $this->curlGet('/bundleIds/'.$id,$query);
    }

    /**
     * 创建一个BundleID
    必传参数：（其它没列出来的参数表示写法固定，参考请求示例）
    name：取个名字
    identifier：bundle identifier
    seedId：Team ID
    platform：IOS，MAC_OS
    Status Code: 201 Created，表示成功
     * @param $identifier
     * @param $name
     * @param $seedId
     * @param $platform Possible values: IOS, MAC_OS
     * @return mixed|string
     */
    public function registerBundleID($identifier, $name,  $seedId = null,$platform='IOS'){
        $params = [
            'data' => [
                'type' => 'bundleIds',
                'attributes' => [
                    'identifier' => $identifier,
                    'name' => $name,
                    'platform' => $platform,
                    'seedId' => $seedId ?: ''
                ]
            ]
        ];
        return $this->curlPost("/bundleIds", $params);
    }

    /**
     * 删除bundleID
     * @param $id
     * @return array|mixed
     */
    public function deleteBundleID($id){
        $ret = $this->curlDel('/bundleIds/' . $id);
        return $ret == [] ? true : false;
    }


    /**
     * 获取bundle设置的ProfileID
     * @param $id
     * @param array $query
     * @return array|mixed
     */
    public function listAllProfilesForABundleID($id, array $query = []){
        return $this->curlGet('/bundleIds/' . $id . '/profiles');
    }

    /**
     * 获取bundle设置的关联ProfileID
     * @param $id
     * @param array $query
     * @return array|mixed
     */
    public function getAllProfileIDsForABundleID($id, array $query = []){
        return $this->curlGet('/bundleIds/' . $id . '/relationships/profiles');
    }


    /**
     * 获取Bundle的所有启用了的特权。
     * @param $id
     * @param array $query
     * @return array|mixed
     */
    public function listAllCapabilitiesForABundleID($id, array $query = []){
        return $this->curlGet('/bundleIds/' . $id . '/bundleIdCapabilities');
    }

    /**
     * 获取Bundle的所有启用了的特权。
     * @param $id
     * @param array $query
     * @return array|mixed
     */
    public function getAllCapabililityIDsForABundleID($id, array $query = []){
        return $this->curlGet('/bundleIds/' . $id . '/relationships/bundleIdCapabilities');
    }


    //********************* Capabilitty *********************

    /**
     * 启用某一个功能。
     * @param $bundleID
     * @param $capabilityType  Possible values: ICLOUD, IN_APP_PURCHASE, GAME_CENTER, PUSH_NOTIFICATIONS, WALLET,
     * INTER_APP_AUDIO, MAPS, ASSOCIATED_DOMAINS, PERSONAL_VPN, APP_GROUPS, HEALTHKIT, HOMEKIT,
     * WIRELESS_ACCESSORY_CONFIGURATION, APPLE_PAY, DATA_PROTECTION, SIRIKIT, NETWORK_EXTENSIONS, MULTIPATH, HOT_SPOT,
     * NFC_TAG_READING, CLASSKIT, AUTOFILL_CREDENTIAL_PROVIDER, ACCESS_WIFI_INFORMATION
     * @param array $settings
     * @return array|mixed
     */
    public function enableCapability($bundleID, $capabilityType, array $settings = []){
        $params = [
            'data' => [
                'type' => 'bundleIdCapabilities',
                'attributes' => [
                    'capabilityType' => $capabilityType,
                    'settings' => $settings
                ],
                'relationships' => [
                    'bundleId' => [
                        'data' => [
                            'id' => $bundleID,
                            'type' => 'bundleIds'
                        ]
                    ]
                ]
            ]
        ];
        return $this->curlPost("/bundleIdCapabilities", $params);
    }

    /**
     * 删除某一个功能
     * @param $bundleID
     * @return mixed
     */
    public function disableCapability($bundleID){
        return $this->curlDel('/bundleIdCapabilities/'. $bundleID);
    }

    /**
     * 修改某一个功能
     * @param $id
     * @param $capabilityType
     * @param array $settings
     * @return mixed
     */
    public function modifyCapability($bundleID, $capabilityType, array $settings = []){
        $data = [
            'data' => [
                'id' => $bundleID,
                'type' => 'bundleIdCapabilities',
                'attributes' => [
                    'capabilityType' => $capabilityType,
                    'settings' => $settings
                ]
            ]
        ];
        return $this->curlPatch('/bundleIdCapabilities/'. $bundleID,$data);
    }
}
