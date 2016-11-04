<?php
namespace App\Helper;

/**
 * 
 * @filesource 简单验证
 * @author luoqingbo
 * @date 2016-05
 */
class Verify
{

    /**
     * 检测是否为正确的邮件格式
     * 返回值:是正确的邮件格式返回邮件,不是返回false
     */
    public static function isEmail($email)
    {
        if (strlen($email) >= 50 || strlen($email) <4) {
            return false;
        }
        $suffix = end(explode('.', $email));
        if(!in_array($suffix, array('com','net','org','cn'))){
            return false;
        }
        $RegExp = '/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/';
        return preg_match($RegExp, $email) ? true : false;
    }

    /**
     * 检测参数的值是否符合QQ号码的格式
     * 返回值:是正确的QQ号码返回QQ号码,不是返回false
     */
    public static function isQQ($qq)
    {
        $RegExp = '/^[1-9][0-9]{5,11}$/';
        return preg_match($RegExp, $qq) ? true : false;
    }

    /**
     * 检测参数的值是否为正确的中国手机号码格式
     * 返回值:是正确的手机号码返回手机号码,不是返回false
     */
    public static function isMobile($mobile)
    {
        $RegExp = '/^(?:13|15|18|14)[0-9]\d{8}$/';
        return preg_match($RegExp, $mobile) ? true : false;
    }

    /**
     * 是否为正确的中国座机号码
     *@example xxx-xxxxxxxx-xxx 或 xxxx-xxxxxxx-xxx ...
     */
    public static function isPhone($tel)
    {
        $RegExp = '/^((0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$/';
        return preg_match($RegExp, $tel) ? true : false;
    }

    /**
     * 是否为邮编
     */
    public static function isPostCode($postcode)
    {
        $RegExp = '/^[0-9]{4,6}$/';
        return preg_match($RegExp, $postcode) ? true : false;
    }

    //验证银行卡号
    public static function isBankCard($card)
    {
        $RegExp = "/^\d{12,}$/";
        return preg_match($RegExp, $card) ? true : false;
    }

    //验证身份证
    public static function isIdentityCard($card)
    {
        if (strlen($card) == 18) {
            return self::idcard_checksum18($card);
        } elseif ((strlen($card) == 15)) {
            $card = self::idcard_15to18($card);
            return self::idcard_checksum18($card);
        } elseif ((strlen($card)) == 10) {
            return self::hkidcard($card);
        } else {
            return false;
        }
    }

    // 计算身份证校验码，根据国家标准GB 11643-1999
    public static function idcard_verify_number($idcard_base)
    {
        if (strlen($idcard_base) != 17) {
            return false;
        }
        //加权因子
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        //校验码对应值
        $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $checksum = 0;
        for ($i = 0; $i < strlen($idcard_base); $i++) {
            $checksum += substr($idcard_base, $i, 1) * $factor[$i];
        }
        $mod = $checksum % 11;
        $verify_number = $verify_number_list[$mod];
        return $verify_number;
    }

    // 将15位身份证升级到18位
    public static function idcard_15to18($idcard)
    {
        if (strlen($idcard) != 15) {
            return false;
        } else {
            // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
            if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false) {
                $idcard = substr($idcard, 0, 6) . '18' . substr($idcard, 6, 9);
            } else {
                $idcard = substr($idcard, 0, 6) . '19' . substr($idcard, 6, 9);
            }
        }
        $idcard = $idcard . self::idcard_verify_number($idcard);
        return $idcard;
    }

    // 18位身份证校验码有效性检查
    public static function idcard_checksum18($idcard)
    {
        if (strlen($idcard) != 18) {
            return false;
        }
        $idcard_base = substr($idcard, 0, 17);
        if (self::idcard_verify_number($idcard_base) != strtoupper(substr($idcard, 17, 1))) {
            return false;
        } else {
            return true;
        }
    }

    //香港身份证号
    public static function hkidcard($idcard)
    {
        $firstStr = substr($idcard, 0, 1);
        $middleStr = substr($idcard, 1, -3);
        $length = strlen($middleStr);

        $rightSecondStr = substr($idcard, -2, 1);
        $left = substr($idcard, -3, 1);
        $right = substr($idcard, -1, 1);
        $ord_firstStr = ord($firstStr);
        $ord_rightSecondStr = ord($rightSecondStr);
        $ord_left = ord($left);
        $ord_right = ord($right);
        if (($ord_firstStr > 90) || ($ord_firstStr < 65)) {
            return false;
        } else if (($ord_left != 40) or ($ord_right != 41)) {
            return false;
        } else if ($ord_rightSecondStr < 48 || $ord_rightSecondStr > 57) {
            return false;
        } else if (!is_numeric($middleStr)) {
            return false;
        } else if ($length != 6) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 是否为英文
     */
    public static function isEnglish($str)
    {
        $RegExp = '/^[a-zA-Z]{1,}$/';
        return preg_match($RegExp, $str) ? true : false;
    }

    /**
     * 是否为中文
     */
    public static function isChinese($str)
    {
        $RegExp = '/^[\x{4e00}-\x{9fa5}]+$/u';
        return preg_match($RegExp, $str) ? true : false;
    }

    /**
     * 检查输入的是否为数字
     * @param $number
     */
    public static function isNumber($number)
    {
        $RegExp = '/^[0-9]+$/';
        return preg_match($RegExp, $number) ? true : false;
    }

    /**
     * 是否为数字-常用id
     */
    public static function isID($val)
    {
        if (is_numeric($val) && preg_match("/^[1-9]\d{0,}$/", $val)) {
            return true;
        }
        return false;
    }

    /**
     * 是否为一个合法的url
     * @param string $url
     * @return boolean
     */
    public static function is_url($url){
        if(!preg_match('/http(s)?:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is',$url)){
            return false;
        }
        return true;
    }
}

