<?php
namespace Img;

class Utils
{
    function trimPx($data) {
        $data['left'] = intval(str_replace('px', '', $data['left'])) * 2;
        $data['top'] = intval(str_replace('px', '', $data['top'])) * 2;
        $data['width'] = intval(str_replace('px', '', $data['width'])) * 2;
        $data['height'] = intval(str_replace('px', '', $data['height'])) * 2;
        $data['size'] = intval(str_replace('px', '', $data['size'])) * 2;
        $data['src'] = tomedia($data['src']);
        return $data;
    }

    function mergeImage($target, $imgurl , $data) {
        $img = $this->imagecreates($imgurl);
        $w = imagesx($img);
        $h = imagesy($img);
        imagecopyresized($target, $img, $data['left'], $data['top'], 0, 0, $data['width'], $data['height'], $w, $h);
        imagedestroy($img);
        return $target;
    }


    /**合并图片
     * @param  $bg 背景图
     * @param  $qr 其他图
     * @param  $out 存放路径
     * @param $param 大小参数
     */
    function mergeImage1($bgImg, $qr, $out, $param) {
        list($qrWidth, $qrHeight) = getimagesize($qr);
        $qrImg = imagecreates($qr);
        imagecopyresized($bgImg, $qrImg, $param['left'], $param['top'], 0, 0, $param['width'], $param['height'], $qrWidth, $qrHeight);
        ob_start();
        imagejpeg($bgImg, NULL, 100);
        $contents = ob_get_contents();
        ob_end_clean();
        imagedestroy($bgImg);
        imagedestroy($qrImg);
        $fh = fopen($out, "w+");
        fwrite($fh, $contents);
        fclose($fh);
    }


    /**创建图片
     * @param $bg 图片路径
     * @return
     */
    function imagecreates($bg) {
        $bgImg = @imagecreatefromjpeg($bg);
        if (FALSE == $bgImg) {
            $bgImg = @imagecreatefrompng($bg);
        }
        if (FALSE == $bgImg) {
            $bgImg = @imagecreatefromgif($bg);
        }
        return $bgImg;
    }

    function saveImage($url) {
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt ( $ch, CURLOPT_URL, $url );
        ob_start ();
        curl_exec ( $ch );
        $return_content = ob_get_contents ();
        ob_end_clean ();
        $return_code = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
        $filename = './temp.jpg';
        $fp= @fopen($filename,"a"); //将文件绑定到流 
        fwrite($fp,$return_content); //写入文件
        return $filename;
    }

}

