<?php


namespace app\api\controller;


use app\common\model\FangyongPrice;
use think\Controller;
use think\Db;

class Pay extends Controller
{

    const  priviate_secrect='MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDhBM9jEI+aCDzlWfpu9Pwk5/5ueILh5s8WSzIRIKzJNmRAXORYhRBL0Htj75Xr4caJiyLG2t8pAupmQTRffC1bCqN1s9sRmKDSQH4NqruvHj0G2bJgrqjy4ljMekUqxjT5UWqjYUWJiRNBBPBYE8OJwkC96eTRdFMYnOInExhF3hbUiNZ9T4SHAmtMZ2zYldZvZrNNDGZD2VJv6xmmTSnsOIrxYz9FzOCsAC/FFmpG2ueMLzSI2Zo7nwWQF2bb1GOek5O96W28e+StXxgyiIZjn/Q0BEOpjoHHnO/12x32TtKURL6iOO2Ydl4WhZm9JXD1d1996Dj28MTH1/WB9/X9AgMBAAECggEAGfiAJyLmMBT7Uk1MRjooT+omx4FJOeF1zVDoemmXB7IrZ/JQuJbDBr0NQL+KQitQytcwMFtanlUj0KF99fdTFMrpitAzXJiNFzcyVUl7V/7ZdEzz9YyzGzVcol0KVqGBw7TN5gV4DSOxATQcuU2up9uvtTulmTcf0BtrTq85gfakopgUXaFFSYVhkNb4NaSqw6z5uFFU3LrnqP2gLx6RvStLY7c0dUzRgqGX6wGybAUCuB5dA/e0s4KBr7r1RBMTibiPUprR5hS2mk4n8F/KwCmtZu37z3RaDEZhWbgupz9u4uo0/OFVgFbHVqMic7NBgLsVnWBXirWsDIYbI1BY0QKBgQD5TTWAxAIKvXeXfr7oUDvn46pt9f2aZqPPRHGcVNxd74qowI4yIsBnO7SfrSe9dMm/tJkO1K0e6c8fhzabt8e6fPuMUwjjiN92YQCh/JN1KDSFk4TgVv1CkgQPU/at1zMLCTrIDDKpzGfA1adaBrr+FUz0aNNf2pfSeyzJEkMY0wKBgQDnEJMiFkCEHe2MhPEuJAGmyKoVPLDwe1aqtCm2SX7Y21dbqI3WKAO3DefWmux17zFIbhcfzbxdssUHIGl932azQfInzRbPNsWQCmoHQB6oc3OjIhNDdcKb7BKHAjrL7VqodkoAF+ir+0to8xPSXdIbjqXwaghcuvgxcl2/rf9z7wKBgEtLyIcVrFA2HXLCM0LTSOSm3IjNrUT/DybA01eWQeCVQtnpHomB+X60h79aexA7v9uI3lmeXpNhps8s9Y9emJJ4wTkGsz6Vznhgn+JM+PR/qi1V+uIPI/sBQvymLXEf+CizJu/Yz0x9kjCp1xvXwI6RPGbtajofOY+26DXqsB1FAoGBAKBGg/iJ/NhntgXZlT3PJgIdgLTYc1dsBlLEQ4a49i7P/omKok/hU6pfD+hxcAHiF4pJKusZl0ZbIYXu+p2gH6nJ3YG0JdU4BdQDFUbQODPaWWTX1wrdHU9wce2KI2Se0Fq4Kl5kNzVMSyOSMLaj84C/8uSPeBtyaYRK8zJpLB+rAoGBANfKxa1OWcj1jQEPtqc2oVIiJO1NK369CJCNGwxMpY7EOwHoxhuq1PtPvyyickuaDZNlP+q0coScpCMgsT95SXUg3X0Cmh2/MZgpewzh3vvPqbHKwdZj1TsL9dNsipEdAhkAv0ciGaOjS87gs7SSyGHSYayQ1sRxt/6PChGu3L9G';
    const pubulic_secrect='MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA4QTPYxCPmgg85Vn6bvT8JOf+bniC4ebPFksyESCsyTZkQFzkWIUQS9B7Y++V6+HGiYsixtrfKQLqZkE0X3wtWwqjdbPbEZig0kB+Daq7rx49BtmyYK6o8uJYzHpFKsY0+VFqo2FFiYkTQQTwWBPDicJAvenk0XRTGJziJxMYRd4W1IjWfU+EhwJrTGds2JXWb2azTQxmQ9lSb+sZpk0p7DiK8WM/RczgrAAvxRZqRtrnjC80iNmaO58FkBdm29RjnpOTveltvHvkrV8YMoiGY5/0NARDqY6Bx5zv9dsd9k7SlES+ojjtmHZeFoWZvSVw9Xdffeg49vDEx9f1gff1/QIDAQAB';

    public function pay(){
        $timestamp = date('Y-m-d h:i:s',time());
        $pay = array();
        $pay['mch_id'] = '宜信惠民';
        $pay['pay_orderid'] = $this->createOrderSn();
        $pay['pay_applydate'] = $timestamp;
        $pay['pay_bankcode'] = '868';
        $pay['pay_notifyurl'] = 'xxxx.test.com';
        $pay['pay_callbackurl'] = 'xxxx.test.com';
        //排序
        ksort($pay);
        $msg = $this->signMsg($pay, 'vqxkaqdVMLz2i9v7Bgyg8gLLBtmEBAN7');
        return hash("sha256", $str);

    }

    //生成订单号
    public function createOrderSn(){
        return date('YmdHis') . str_pad(mt_rand(1, 99999), 6, '0', STR_PAD_LEFT);
    }


    public function signMsg($array, $md5Key){
        $msg = "";
        // 转换为字符串 &key=value&key.... 加签
        foreach ($array as $key => $val) {
            // 不参与签名
            if($key != "sign" && $val != ""){
                $msg = $msg."&$key=$val";
            }
        }

        $msg = substr($msg,1).$md5Key;
        return  $msg;
    }




}