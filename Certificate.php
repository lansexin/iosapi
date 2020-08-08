<?php
/**
 * Created by PhpStorm.
 * User: lansy
 * Date: 2020/6/15
 * Time: 21:49
 */

namespace App\Http\Controllers\Iosapi;

class Certificate extends Apapi
{
    public function __construct($acc_id){
        $this->acc = $acc_id;
    }

    /**
     * 创建一个CSR文件。
     * @param $commonName
     * @param $emailAddress
     * @return array
     */
    public function getCertificateSigningRequest($commonName, $emailAddress)
    {
        $privateKeyParam = [
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ];
        $privateKey = openssl_pkey_new($privateKeyParam);
        $subject = [
            'commonName' => $commonName,
            'emailAddress' => $emailAddress
        ];
        $certificateSigningRequest = openssl_csr_new($subject, $privateKey);
        openssl_pkey_export($privateKey, $pkey);
        openssl_csr_export($certificateSigningRequest, $csr);
        return [
            'private_key' => $pkey,
            'csr' => $csr
        ];
    }

    /**
     * 查找最新adhoc的证书。
     * @param string $type
     * @return array
     */
    public function listLatestCertificates($type = 'IOS_DISTRIBUTION'){
        $rets = $this->curlGet('/certificates');
        $info = [];
        if(isset($rets['data'])) foreach ($rets['data'] as $k=>$row){
            if($type == $row['attributes']['certificateType']){
                if($info) { //如果有数据，则比较时间
                    if(strtotime($info['attributes']['expirationDate'])<strtotime($row['attributes']['expirationDate'])) {
                        $info = $row;
                    }
                }else{
                    $info = $row;
                }
            }
        }
        return $info;
    }

    /**
     *
    array(4) {
        ["type"]=>
        string(12) "certificates"
        ["id"]=>
        string(10) "MYT2V6955Y"
        ["attributes"]=>
            array(8) {
            ["serialNumber"]=>
            string(16) "60A00CA774CD5807"
            ["certificateContent"]=>
            string(1908) "MIIFkTCCBHmgAwIBAgIIYKAMp3TNWAcwDQYJKoZIhvcNAQELBQAwgZYxCzAJBgNVBAYTAlVTMRMwEQYDVQQKDApBcHBsZSBJbmMuMSwwKgYDVQQLDCNBcHBsZSBXb3JsZHdpZGUgRGV2ZWxvcGVyIFJlbGF0aW9uczFEMEIGA1UEAww7QXBwbGUgV29ybGR3aWRlIERldmVsb3BlciBSZWxhdGlvbnMgQ2VydGlmaWNhdGlvbiBBdXRob3JpdHkwHhcNMjAwNjE1MDMzODU5WhcNMjEwNjE1MDMzODU5WjCBhDEaMBgGCgmSJomT8ixkAQEMClA4NjlNVDVKWEYxMjAwBgNVBAMMKWlQaG9uZSBEaXN0cmlidXRpb246IEZlbmcgWWUgKFA4NjlNVDVKWEYpMRMwEQYDVQQLDApQODY5TVQ1SlhGMRAwDgYDVQQKDAdGZW5nIFllMQswCQYDVQQGEwJDTjCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBAOxtKJOZktRshHbdrBTlsVvFsTGX/TqvTW8fk9MiXK0iMMwpkG5J8mCcPj1qY9opZi5RMkmutdcVxdfRKlZ4MZ2nw0KIs2ZyI5PeBOdQU+rI7jSbbd/f83VevWSfkGKAtX8sHjnoJ4oIJg0oqb963YHOsrAyO7F4hebyY9NXheD7DzepJtJtWiFTF8iH12dag3IA2QNxTEzJxLtzWkhdm0oP2VZBEqas5Yhe0Iq+kDKyppgQniZx3nvDv6Dk5x7HtLe6mrnW9b725k+WaZMt6IRswPEnNiB5ok0k3ZDyxs0zSo1XhKk6f8W/cnTGk9JYzZsKRBVgTSQf+OEI7Twd6E8CAwEAAaOCAfEwggHtMAwGA1UdEwEB/wQCMAAwHwYDVR0jBBgwFoAUiCcXCam2GGCL7Ou69kdZxVJUo7cwPwYIKwYBBQUHAQEEMzAxMC8GCCsGAQUFBzABhiNodHRwOi8vb2NzcC5hcHBsZS5jb20vb2NzcDAzLXd3ZHIxMTCCAR0GA1UdIASCARQwggEQMIIBDAYJKoZIhvdjZAUBMIH+MIHDBggrBgEFBQcCAjCBtgyBs1JlbGlhbmNlIG9uIHRoaXMgY2VydGlmaWNhdGUgYnkgYW55IHBhcnR5IGFzc3VtZXMgYWNjZXB0YW5jZSBvZiB0aGUgdGhlbiBhcHBsaWNhYmxlIHN0YW5kYXJkIHRlcm1zIGFuZCBjb25kaXRpb25zIG9mIHVzZSwgY2VydGlmaWNhdGUgcG9saWN5IGFuZCBjZXJ0aWZpY2F0aW9uIHByYWN0aWNlIHN0YXRlbWVudHMuMDYGCCsGAQUFBwIBFipodHRwOi8vd3d3LmFwcGxlLmNvbS9jZXJ0aWZpY2F0ZWF1dGhvcml0eS8wFgYDVR0lAQH/BAwwCgYIKwYBBQUHAwMwHQYDVR0OBBYEFN0lN/ejqETv8PJqebKSYHzLkRZAMA4GA1UdDwEB/wQEAwIHgDATBgoqhkiG92NkBgEEAQH/BAIFADANBgkqhkiG9w0BAQsFAAOCAQEApOZtAVdIaF7lmrEt+ZXGxrrH1NVoZRjRUC8pWb9ih1WWI5etgcbwq+EuZefNNBuMKv+MChHy6XBpMiM9t0AkAwC1iWAqeqi7ze/5T96j0alrjEFw7ytLQ5BRxdGLFbeuoX+IznuIw+sxJr8pDC68vVzhh/kkIE+CeCciAR8pVBdw5lb5zHY504AaXIVTY5pDV5IaNHzzjqOdmQGE3ELHaqyRrnuWOa9FauhGnvs1lqJVbflOuQwKxfQBLO+dOy8X/nJ0GVboE6ueCpynFWVoVSZj/TuPP2lVODsImT9dYgVxnXwTYQrEQ5/gaEeBb7Xw8tsy/R5nJMW6WTUCnkBrfA=="
            ["displayName"]=>
            string(7) "Feng Ye"
            ["name"]=>
            string(25) "iOS Distribution: Feng Ye"
            ["csrContent"]=>
            NULL
            ["platform"]=>
            string(3) "IOS"
            ["expirationDate"]=>
            string(28) "2021-06-15T03:38:59.000+0000"
            ["certificateType"]=>
            string(16) "IOS_DISTRIBUTION"
        }
        ["links"]=>
        array(1) {
        ["self"]=>
        string(64) "https://api.appstoreconnect.apple.com/v1/certificates/MYT2V6955Y"
        }
    }
     * 列出所有的证书
     * @return array|mixed
     */
    public function listAllCertificates(){
        return  $this->curlGet('/certificates');
    }

    /**
     * 获取指定类型的证书,用于统计数量，超出可以删除。
     * @return array|mixed
     */
    public function showTypeCertificates($type = 'IOS_DISTRIBUTION'){
        $rets = $this->curlGet('/certificates');
        $info = [];
        if (isset($rets['data'])) foreach ($rets['data'] as $k => $row) {
            if ($type == $row['attributes']['certificateType']) {
                $info[] = $row;
            }
        }
        return $info;
    }

    /**
     * 销毁某一个证书
     * @param $id
     * @return mixed
     */
    public function revokeCertificate($id){
        $ret = $this->curlDel('/certificates/' . $id);
        return isset($ret['errors']) ? false : true;
    }

    /**
     * 创建某一个证书。
     *
     *
    array(2) {
        ["data"]=>
        array(4) {
        ["type"]=>
        string(12) "certificates"
        ["id"]=>
        string(10) "FPFFT7ZAFW"
        ["attributes"]=>
            array(8) {
            ["serialNumber"]=>
            string(16) "772838EC71E04854"
            ["certificateContent"]=>
            string(1908) "MIIFkTCCBHmgAwIBAgIIdyg47HHgSFQwDQYJKoZIhvcNAQELBQAwgZYxCzAJBgNVBAYTAlVTMRMwEQYDVQQKDApBcHBsZSBJbmMuMSwwKgYDVQQLDCNBcHBsZSBXb3JsZHdpZGUgRGV2ZWxvcGVyIFJlbGF0aW9uczFEMEIGA1UEAww7QXBwbGUgV29ybGR3aWRlIERldmVsb3BlciBSZWxhdGlvbnMgQ2VydGlmaWNhdGlvbiBBdXRob3JpdHkwHhcNMjAwNjE2MTM0NjQ1WhcNMjEwNjE2MTM0NjQ1WjCBhDEaMBgGCgmSJomT8ixkAQEMClA4NjlNVDVKWEYxMjAwBgNVBAMMKWlQaG9uZSBEaXN0cmlidXRpb246IEZlbmcgWWUgKFA4NjlNVDVKWEYpMRMwEQYDVQQLDApQODY5TVQ1SlhGMRAwDgYDVQQKDAdGZW5nIFllMQswCQYDVQQGEwJDTjCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBAM8ak8hfmO4a+/WRzofg6IrrogXpHraDIoNQ+NhDBJNNfRUB7EyhqjhaCrLn6KCJMeR6a3T5GODPOoPXRxamQ3udqLkNsxSjDsyPuFPjyOhjYI6OakeBRRq2SLUAjBRwqMbTuEOlgMcgkE7ci86326Xj+lr7MUowOlWECIFgoFMNp1RZK4sFEpmnCme9HPd0Bg8INZ7QcFjF/M7jzMfUCnLtj1tbcyXkEq0v/gtztZqndgJ1LQjXSiqPSuduxJiNqjihXZKk/+ROcGm5bT0FFYeHIn3qhWzxD857i0R/Z4jmWlCXGwp+cD9fo+G3iSHtTPwebtvwehjMrnDLvhmEA48CAwEAAaOCAfEwggHtMAwGA1UdEwEB/wQCMAAwHwYDVR0jBBgwFoAUiCcXCam2GGCL7Ou69kdZxVJUo7cwPwYIKwYBBQUHAQEEMzAxMC8GCCsGAQUFBzABhiNodHRwOi8vb2NzcC5hcHBsZS5jb20vb2NzcDAzLXd3ZHIxMTCCAR0GA1UdIASCARQwggEQMIIBDAYJKoZIhvdjZAUBMIH+MIHDBggrBgEFBQcCAjCBtgyBs1JlbGlhbmNlIG9uIHRoaXMgY2VydGlmaWNhdGUgYnkgYW55IHBhcnR5IGFzc3VtZXMgYWNjZXB0YW5jZSBvZiB0aGUgdGhlbiBhcHBsaWNhYmxlIHN0YW5kYXJkIHRlcm1zIGFuZCBjb25kaXRpb25zIG9mIHVzZSwgY2VydGlmaWNhdGUgcG9saWN5IGFuZCBjZXJ0aWZpY2F0aW9uIHByYWN0aWNlIHN0YXRlbWVudHMuMDYGCCsGAQUFBwIBFipodHRwOi8vd3d3LmFwcGxlLmNvbS9jZXJ0aWZpY2F0ZWF1dGhvcml0eS8wFgYDVR0lAQH/BAwwCgYIKwYBBQUHAwMwHQYDVR0OBBYEFOq5m9iuslOeU6R9i9v4DL0mI2zNMA4GA1UdDwEB/wQEAwIHgDATBgoqhkiG92NkBgEEAQH/BAIFADANBgkqhkiG9w0BAQsFAAOCAQEAM2eENMaaiUJsNqMRJyJH1ziC7ZYr53DnbNzQggoWTDEQ99UuFBt0ebnhAa8ADjDe54w20oJRm0IRB6RVQhQyjhTBaEASBTnrujBXD1MhongF3WI0P7AJDZBk7SGycX1hKV+tzDtB2CgghMTl0T0qKihVF9ClSLZhrEkLpLloQJn8fWASCM6bUgZGU7lUtQxFRrubDoBPXTVnLnTUZ5nRarj40K0P0KH9ublLi6sCQrq2issARE1dzRTIf9gYnnCUp75gTEAULmQCg1/Z1lUVRiuhPnJHd7EqRxs9SLfV77brpf/I3KtzHpStL785LoibzILxXsmi1Lci+GkA4RTNRg=="
            ["displayName"]=>
            string(7) "Feng Ye"
            ["name"]=>
            string(25) "iOS Distribution: Feng Ye"
            ["csrContent"]=>
            NULL
            ["platform"]=>
            string(3) "IOS"
            ["expirationDate"]=>
            string(28) "2021-06-16T13:46:45.000+0000"
            ["certificateType"]=>
            string(16) "IOS_DISTRIBUTION"
            }
            ["links"]=>
            array(1) {
            ["self"]=>
            string(64) "https://api.appstoreconnect.apple.com/v1/certificates/FPFFT7ZAFW"
            }
        }
        ["links"]=>
        array(1) {
        ["self"]=>
        string(53) "https://api.appstoreconnect.apple.com/v1/certificates"
        }
    }

     *
     * 创建指定类型证书。
     * @param $certificateType Possible values: IOS_DEVELOPMENT, IOS_DISTRIBUTION, MAC_APP_DISTRIBUTION,
     * MAC_INSTALLER_DISTRIBUTION, MAC_APP_DEVELOPMENT, DEVELOPER_ID_KEXT, DEVELOPER_ID_APPLICATION
     * @param $csrContent
     * @return mixed|string
     *
     *
     */
    public function createCertificate($csrContent,$certificateType = 'IOS_DISTRIBUTION')
    {
        $params = [
            'data' => [
                'type' => 'certificates',
                'attributes' => [
                    'csrContent' => $csrContent,
                    'certificateType' => $certificateType
                ]
            ]
        ];
        return $this->curlPost('/certificates',$params);
    }

    /**
     * 读取某一个证书的信息。
     * @param $id
     * @param array $query
     * @return mixed
     */
    public function readCertificateInformation($id, array $query = []){
        return $this->curlGet('/certificates/'.$id,$query);
    }
}
