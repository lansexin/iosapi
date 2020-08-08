<?php
namespace App\Http\Controllers\Iosapi;
use App\Http\Controllers\Center\BController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
/**
 * 请求ios接口。
 * Created by PhpStorm.
 * User: lansy
 * Date: 2020/6/15
 * Time: 11:52
 * 1.获取token。
 */
class Apapi extends BController {
    var $apiURL = 'https://api.appstoreconnect.apple.com/v1';
    var $acc;
    var $timeout = 15;//token有效时间，15分钟
    var $key_id,$issuser,$authKey;

    public function __construct(){}

    public function setCfg($acc_id,$key_id,$issuser,$authKey){
        $this->acc = $acc_id;
        $this->key_id = $key_id;
        $this->issuser = $issuser;
        $this->authKey = $authKey;
        $this->getAuthToken();
    }

    /**
     * 获取token
     * @throws Exception
     */
    private function getAuthToken() {
        $key = "api_token_".$this->acc;
        if($token = Redis::get($key)) {
            return $token;
        }
        $header = ["alg"=>"ES256","kid"=>$this->key_id,"typ"=>"JWT"];
        $payload = ['iss'=>$this->issuser,'exp'=>time()+($this->timeout+2)*60,"aud"=>"appstoreconnect-v1"];
        $token =  JWT::sign($payload, $header, $this->authKey);
        Redis::setex($key,$this->timeout*60,$token);
        return $token;
    }

    protected function curlGet($uri,$params = []){
        if(substr($uri,0,1)!='/') {
            $uri = '/'.$uri;
        }
        $curl = curl_init();
        $token = $this->getAuthToken();
        $header =   ['Authorization: Bearer '.$token,'Content-Type:application/json'];
        if(!empty($header)){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
            curl_setopt($curl, CURLOPT_HEADER, 0);//返回response头部信息
        }
        $str = '';
        if($params){
            $str = '?'.http_build_query($params);
        }
        curl_setopt($curl, CURLOPT_URL, $this->apiURL.$uri.$str);
        curl_setopt($curl, CURLOPT_HTTPGET, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//绕过ssl验证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $output = curl_exec($curl);
        $tmp = [];
        $output && $tmp = json_decode($output,true);
        curl_close($curl);
        return $tmp;
    }

    function curlDel($uri,$params=[]) {
        if(substr($uri,0,1)!='/') {
            $uri = '/'.$uri;
        }
        $token = $this->getAuthToken();
        $ch = curl_init();
        $header =   ['Authorization: Bearer '.$token,'Content-Type:application/json'];
        if(!empty($header)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_HEADER, 0);//返回response头部信息
        }
        if($params){
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }
        curl_setopt($ch, CURLOPT_URL, $this->apiURL.$uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//绕过ssl验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $output = curl_exec($ch);
        $tmp = [];
        $output && $tmp = json_decode($output,true);
        curl_close($ch);
        return $tmp;
    }

    function curlPatch($uri,$params=[]) {
        if(substr($uri,0,1)!='/') {
            $uri = '/'.$uri;
        }
        $ch = curl_init();
        $token = $this->getAuthToken();
        $header =   ['Authorization: Bearer '.$token,'Content-Type:application/json'];
        if(!empty($header)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_HEADER, 0);//返回response头部信息
        }
        curl_setopt ($ch,CURLOPT_URL,$this->apiURL.$uri);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
        curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($params));     //20170611修改接口，用/id的方式传递，直接写在url中了
        $output = curl_exec($ch);
        $tmp = [];
        $output && $tmp = json_decode($output,true);
        curl_close($ch);
        return $tmp;
    }

    protected function curlPost($uri , $data=array()){
        if(substr($uri,0,1)!='/') {
            $uri = '/'.$uri;
        }
        $ch = curl_init();
        $header =   ['Authorization: Bearer '.$this->getAuthToken(),'Content-Type: application/json'];
        if(!empty($header)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_HEADER, 0);//返回response头部信息
        }
        curl_setopt($ch, CURLOPT_URL, $this->apiURL.$uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        // POST数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // 把post的变量加上
        curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($data));
        $output = curl_exec($ch);
        $tmp = [];
        curl_close($ch);
        $output && $tmp = json_decode($output,true);
        return $tmp;
    }

    /**
     * 列出所有app
     */
    public function listAllApps(){
        return $this->curlGet('/apps');
    }

    /**
     * 验证token
     */
    private function TestToken(){
        $ret = $this->curlGet('/apps');
        var_dump($ret);
        //$cmd = "curl -v -H 'Authorization: Bearer eyJhbGciOiJFUzI1NiIsImtpZCI6Ijk2RENaR0JSOTQiLCJ0eXAiOiJKV1QifQ.eyJpc3MiOiJjZTY0M2UzMS0xOWI0LTQxNjMtYjlmMC1jOTJmYmFiZWFjN2QiLCJleHAiOjE1OTIyMDc1NjQsImF1ZCI6ImFwcHN0b3JlY29ubmVjdC12MSJ9.Ounk5l3JC85rRkhJ5ApWh7ctkSkFPumbBRQgMTTx7AAXCdWaK1e41Q2lS-cumR6sK9XFE8B9CypZvZvhgA__HQ' \"https://api.appstoreconnect.apple.com/v1/apps\"";
    }

    /**
     * 获取开发团队的成员列表。
     */
    public function getDevUsers(){
        return $this->curlGet('/users');
    }

    //分析.
    private function explainCert($con,$acc){
        $str = base64_decode($con);
        $path = base_path('config').'/p12/'.$acc;
        if(!is_dir($path)) {
            mkdir($path);
            chmod($path, 0777);
        }
        $tf = $path.'/'.$acc.'.cer';
        file_put_contents($tf,$str);
        return $tf;
    }
    private function getAppleWWDRCA(){
        $f = '/tmp/AppleWWDRCA.pem';
        if(!file_exists($f)) {  //不存在则下载，并导出.
            $con = file_get_contents('http://developer.apple.com/certificationauthority/AppleWWDRCA.cer');
            if($con) {
                $t = '/tmp/apple.cer';
                file_put_contents($t,$con);
                exec("openssl x509 -in {$t} -inform DER -out {$f} -outform PEM",$ar,$ret);
                if($ret == 0){
                    unlink($t);
                    return $f;
                }
                return false;
            }
        }
        return $f;
    }
    private function delFiles($files){
        foreach ($files as $f) {
            @unlink($f);
        }
    }

    //********************************* 具体的逻辑 ************************************

    /**
     * 1.获取CSR文件。
     */
    public function getCSR($email){
        $cert = new Certificate('');
        $name = substr($email,0,stripos($email,'@'));
        return $cert->getCertificateSigningRequest($name,$email);
    }

    /**
     * 2.获取distribution_key.pem文件。
     */
    public function get_distribution_pem($acc,$csr){
        //判断当前账户有多少个IOS_DISTRIBUTION,如果超过2个，则需要删除一个，不然无法创建新的。
        $cert = new Certificate($acc);
        $all = $cert->showTypeCertificates();
        $flag = true;
        if(count($all)>=2) {
            $tmp = [];
            //删除最新的一个.
            foreach ($all as $k=>$row){
                if($tmp) { //如果有数据，则比较时间
                    if(strtotime($tmp['attributes']['expirationDate'])<strtotime($row['attributes']['expirationDate'])) {
                        $tmp = $row;
                    }
                }else{
                    $tmp = $row;
                }
            }
            //得到最新的。请求删除。
            $flag = $cert->revokeCertificate($tmp['id']);
        }
        if($flag) {
            //创建新的。
            $data = $cert->createCertificate($csr);
            if(isset($data['errors'])) {    //记录错误日志。
                DB::table('iosacc')->where('id',$acc)->update(['note'=>$data['errors'][0]['detail'],'stat'=>1,'status'=>4]);
            } else if(isset($data['data']['attributes']['certificateContent'])) {
                return ['cert_id'=> $data['data']['id'], 'file'=>$this->explainCert($data['data']['attributes']['certificateContent'],$acc)];
            }
        }
        return false;
    }


    /**
     *
     * https://www.jason-z.com/post/160
     * @param $cer  为下载生成后的证书cer
     * @param $pri_key 为生成CSR时的创建的私钥。
     */
    public function exportP12($cer,$pri_key,$acc_id,$path){
        //wget http://developer.apple.com/certificationauthority/AppleWWDRCA.cer
        //openssl x509 -in AppleWWDRCA.cer -inform DER -out AppleWWDRCA.pem -outform PEM
        $apple = $this->getAppleWWDRCA();

        if(is_bool($apple)) {
            exit('export AppleWWDRCA error');
        }
        //导出cer为pem格式.
        $pri_file = '/tmp/pri_file';
        file_put_contents($pri_file,$pri_key);
        $dis_pem = '/tmp/ios_distribution.pem';
        //openssl x509 -inform der -in ios_distribution.cer -outform PEM -out ios_distribution.pem
        exec("openssl x509 -inform der -in {$cer} -outform PEM -out {$dis_pem}",$ar,$ret);
        if($ret){ //失败.
            exit('export error -1 ');
        }

        $p12 = $path.'/'.$acc_id.'.p12';
        $pwd = $this->getRandomStr(10);
        exec("openssl pkcs12 -export -out {$p12} -inkey {$pri_file} -in {$dis_pem} -certfile {$apple} -passout pass:'{$pwd}' ",$ar,$ret);
        $files = [$pri_file,$dis_pem];
        if($ret == 0) {
            $this->delFiles($files);
            return ['pwd'=>$pwd,'p12'=>$p12];
        }
        $this->delFiles($files);
        exit('export error -2');
    }

    /**
     * 获取设备剩余可使用的数量.
     */
    public function getDeviceNums($acc_id){
        $dev = new Devices($acc_id);
        $info = $dev->listDevices();
        $num = 100;
        if(isset($info['data'])) {
            $num -= count($info['data']);
        }else{
            $num = 0;
        }
        return $num;
    }


    /**
     * 生成profile，并下载mobileprovision文件。
     * @param $acc_id       苹果账号的数据库id
     * @param $app_id       应用的id
     * @param $udid         用户的设备id
     * @param $identifier   包名
     * @param $cert_id      证书id
     * @param $team_id      团队id
     * @param $regfc String    注册信息，回调方法.
     * @return string
     */
    public function createProvision($acc_id,$app_id,$udid,$identifier,$cert_id,$team_id,$regfc){
        $profile = new Profiles($acc_id);
        $name = $this->getRandomStr(10);
        //注册device
        $dev = new Devices($acc_id);
        $info = $dev->registerDevice($name,$udid);
        $dev_id = '';
        //判断是否已经注册了。
        if(isset($info['errors']) && stripos($info['errors'][0]['detail'],'exists')!==false) {
            $info = $dev->getOneDevID($udid);
            if($info && isset($info['data'][0]['id'])) {
                $dev_id = $info['data'][0]['id'];
            }
        }else{
            if($info && isset($info['data']['id'])) {
                $dev_id = $info['data']['id'];
            }
        }
        //注册成功，记录下来udid信息。
        $regid = $regfc();
        if(stripos($regid,'err_')!==false) {
            return $regid;
        }
        if(!$dev_id) {
            if(stripos($info['errors'][0]['detail'],'exists')===false){}
            DB::table('iosacc')->where('id',$acc_id)->update(['note'=>$info['errors'][0]['detail'],'status'=>4]);
            //碰到问题。记录下来.
            return 'err_01';
        }
        //注册bundle.
        $bun = new BundleID($acc_id);
        $bun_id = '';
        $info = $bun->listOneBundleID($identifier);
        if($info && isset($info['data'][0]['id'])) {
            $bun_id = $info['data'][0]['id'];
        } else {
            $info = $bun->registerBundleID($identifier,$name,$team_id);
            if($info && isset($info['data']['id'])) {
                $bun_id = $info['data']['id'];
                //启用推送消息功能。
                $ret = $bun->enableCapability($bun_id,'PUSH_NOTIFICATIONS');
            }
        }
        if(!$bun_id) {
            return 'err_02';
        }
        //注册bundle，下载ipa时生成plist文件需要用到。
        $this->registerBundle($acc_id,$app_id,$bun_id,$identifier);

//        $data = $profile->listProfile();
//        var_dump($data);

        $ret = $profile->createProfile($name,'IOS_APP_ADHOC',$bun_id,$cert_id,$dev_id);
        if($ret && isset($ret['data']['attributes'])) {
            $profileContent = $ret['data']['attributes']['profileContent'];
            $path = base_path('storage').'/app/provisions/';
            $f = $path.$name.'.mobileprovision';
            if(!file_exists($f)) {
                touch($f);
            }
            file_put_contents($f,base64_decode($profileContent));
            return $f;
        }else{
            return 'err_03';
        }
    }

    private function registerBundle($acc,$app,$bun_id,$indentify){
        DB::table('bundles')->insertOrIgnore([
            'acc_id'    =>  $acc,
            'app_id'    =>  $app,
            'bun_id'    =>  $bun_id,
            'identifier'=>  $indentify,
            'ctime'     =>  time()
        ]);
    }




}
