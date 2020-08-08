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

class TestFlight extends Apapi{
    public function __construct($acc_id){
        $this->acc = $acc_id;
    }
    /*
     TestFlight
获取APP列表
GET https://api.appstoreconnect.apple.com/v1/apps
可选参数：
fields[apps]：返回的app信息有限，就是后面这些，builds, bundleId, name, preReleaseVersions, primaryLocale, sku, betaAppLocalizations, betaAppReviewDetail, betaGroups, betaLicenseAgreement, betaTesters
include：betaAppLocalizations, betaAppReviewDetail, betaGroups, betaLicenseAgreement, builds, preReleaseVersions
其它可选参数，请自行查看文档
获取APP列表 返回数据(去除了一些无用字段)
{
  "data": [{
    "type": "apps",
    "id": "1456000000",
    "attributes": {
      "name": "微信",
      "bundleId": "com.tencent.xin",
      "sku": "com.tencent.xin",
      "primaryLocale": "zh-Hans"
    }
}
复制代码获取某个app的preReleaseVersions信息（对应App Store Connect后台的version）
GET https://api.appstoreconnect.apple.com/v1/apps/1456000000/preReleaseVersions
获取某个app preReleaseVersions信息 返回数据(去除了一些无用数据)
{
  "data": [{
    "type": "preReleaseVersions",
    "id": "316bxxx-5394-46d7-8e49-4axxxxx",
    "attributes": {
      "version": "1.0.2",
      "platform": "IOS"
    }
  }, {
    "type": "preReleaseVersions",
    "id": "8a9xxxx-3620-4bbd-a540-9axxxxx",
    "attributes": {
      "version": "1.0.0",
      "platform": "IOS"
    },
  }, {
    "type": "preReleaseVersions",
    "id": "c649xxxx-3229-4e20-8349-2efaxxxxx",
    "attributes": {
      "version": "1.0.1",
      "platform": "IOS"
    }
  }],
  "meta": {
    "paging": {
      "total": 3,
      "limit": 50
    }
  }
}
复制代码获取某个app的builds信息（对应App Store Connect后台的二进制包）
GET https://api.appstoreconnect.apple.com/v1/apps/1456000000/builds
获取某个app build信息 返回数据（我去掉了一些无用数据）
{
    "data": [
        {
            "type": "builds",
            "id": "977xxx-c0d1-47a5-b439-7485xxxx",
            "attributes": {
                "version": "5",
                "uploadedDate": "2019-07-05T01:49:16-07:00",
                "expirationDate": "2019-10-03T01:49:16-07:00",
                "expired": true,
                "minOsVersion": "9.0",
                "iconAssetToken": {
                    "templateUrl": "https://is2-ssl.mzstatic.com/image/thumb/Purple113/v4/41/24/46/41xxx-8fa3-142b-b3da-e1xxxxx/Icon-60@2x.png.png/{w}x{h}bb.{f}",
                    "width": 120,
                    "height": 120
                },
                "processingState": "VALID",
                "usesNonExemptEncryption": false
            },
        },
        {
            "type": "builds",
            "id": "de3d9xxx-d87b-449c-b025-a510xxxxx",
            "attributes": {
                "version": "6",
                "uploadedDate": "2019-11-04T00:13:03-08:00",
                "expirationDate": "2020-02-02T00:13:03-08:00",
                "expired": false,
                "minOsVersion": "9.0",
                "iconAssetToken": {
                    "templateUrl": "https://is4-ssl.mzstatic.com/image/thumb/Purple123/v4/4b/f2/41/4bxxx-f059-fe0a-c220-39cxxx/Icon-60@2x.png.png/{w}x{h}bb.{f}",
                    "width": 120,
                    "height": 120
                },
                "processingState": "VALID",
                "usesNonExemptEncryption": false
            }
        }
        ...
    ],
    "meta": {
        "paging": {
            "total": 6,
            "limit": 50
        }
    }
}

作者：CocoaKier
链接：https://juejin.im/post/5df75e395188251227530e87
来源：掘金
著作权归作者所有。商业转载请联系作者获得授权，非商业转载请注明出处。
     */
}
