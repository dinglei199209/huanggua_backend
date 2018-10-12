<?php
/**
 * Created by PhpStorm.
 * User: yhouse-pai
 * Date: 2015/2/9
 * Time: 11:02
 */

namespace app\models;


class Helper {

    //Elisa's Merchant
    public static function disposeImgAction($tmp_path, $dst_w, $file_path, $file_name)
    {
        $arr=getimagesize($tmp_path);
        $src_w=$arr[0];
        $src_h=$arr[1];
        $type=$arr[2];
        switch($type){
            case 1:$src_im = imagecreatefromgif($tmp_path);break;
            case 2:$src_im = imagecreatefromjpeg($tmp_path);break;
            case 3:$src_im = imagecreatefrompng($tmp_path);break;
            // default:UtlsSvc::showMsg('不支持该图片类型','/coinproduct/index/');
        }

        if ($dst_w == 200) {
            $dst_h = 50;
        } elseif ($dst_w == 150) {
            $dst_h = 44;
        } elseif ($dst_w == 120) {
            $dst_h = 30;
        }elseif ($dst_w == 294) {
            $dst_h = 82;
        }

        $dst_im=imagecreatetruecolor($dst_w,$dst_h);
        $white = imagecolorallocate($dst_im, 255, 255, 255);
        imagefill($dst_im, 0, 0, $white);
        imagecopyresized($dst_im,$src_im,0,0,0,0,$dst_w,$dst_h,$src_w,$src_h);
        imagejpeg($dst_im, $file_path.'/'.$file_name);

        ImageDestroy ($src_im);
        ImageDestroy ($dst_im);
    }

    public static function send_post($url, $post_data) {
        $postdata = http_build_query($post_data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postdata,
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        return $result;
    }
    
    public static function myTrim($str) {
        $str = trim($str);
        $str = str_replace("\r", "", $str);
        $str = str_replace("\n", "", $str);
        $str = str_replace("\t", "", $str);
        $str = preg_replace("/(\s+)/", " ", $str);
        return $str;
    }
    
    public static function array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if ( ! isset($value[$columnKey])) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            }
            else {
                if ( ! isset($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if ( ! is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }
}