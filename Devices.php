<?php
/**
 * Created by PhpStorm.
 * User: lansy
 * Date: 2020/6/15
 * Time: 21:49
 */

namespace App\Http\Controllers\Iosapi;

class Devices extends Apapi{
    public function __construct($acc_id){
        $this->acc = $acc_id;
    }
    /**
     * 获取所有的设备信息。
    可选参数：
    fields[devices]：筛选显示的数据，addedDate, deviceClass, model, name, platform, status, udid
    filter[id]
    filter[name]
    filter[platform]：IOS, MAC_OS
    filter[status]：ENABLED, DISABLED    设备只能启用、禁用，无法删除。
    filter[udid]：
    limit：integer，最大200
    sort：id, -id, name, -name, platform, -platform, status, -status, udid, -udid

    作者：CocoaKier
    链接：https://juejin.im/post/5df75e395188251227530e87
    来源：掘金
    著作权归作者所有。商业转载请联系作者获得授权，非商业转载请注明出处。
     * @param array $query
     * @return array|mixed
     */
    public function listDevices(array $query = ['fields[devices]'=>'addedDate, deviceClass, model, name, platform, status, udid']){
        return $this->curlGet('/devices',$query);
    }

    /**
     * 获取对应的udid的注册id.
     */
    public function getOneDevID($udid){
        $query = ['fields[devices]'=>'addedDate, deviceClass, model, name, platform, status, udid','filter[udid]'=>$udid];
        return $this->curlGet('/devices',$query);
    }


    /**
     * 获取某一个设备的信息。
     * @param $id
     * @param array $query
     * @return mixed
     */
    public function readDeviceInformation($id, array $query = []){
        return $this->curlGet('/devices/'.$id,$query);
    }

    /**
     * 注册某一个设备.
     * @param $name
     * @param $udid
     * @param $platform Possible values: IOS, MAC_OS
     * @return mixed|string
     */
    public function registerDevice($name, $udid, $platform='IOS'){
        $params = [
            'data' => [
                'type' => 'devices',
                'attributes' => [
                    'name' => $name,
                    'platform' => $platform,
                    'udid' => $udid
                ]
            ]
        ];
        return $this->curlPost("/devices",$params);
    }

    /**
     * 修改设备的状态。
     * @param $id
     * @param $name
     * @param $status Possible values: ENABLED, DISABLED
     * @return mixed|string
     */
    public function modifyDevice($id, $name, $status){
        $json = [
            'data' => [
                'id' => $id,
                'type' => 'devices',
                'attributes' => [
                    'name' => $name,
                    'status' => $status
                ]
            ]
        ];
        return $this->curlPatch('/devices/' . $id,$json);
    }
}
