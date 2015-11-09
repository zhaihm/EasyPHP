<?php

class CHttp
{
    public static function GetRequest($url, $protocol = 'http', $timeout = 3, $inheader=array(), $charset ='utf-8', $num = 1)
    {
        $tBegin = Log::getLogTime();
        $ch = curl_init();
        if(false == $ch)
        {
            echo "1";
            Log::writeLog('CHttp::GetRequest curl_init failed ,  protocol[' . $protocol . ']  $url[' . $url . ']', 'error',0,'interface');
            return false;
        }

        if ($protocol === 'https')
        {
            $isHttps = true;
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        }
        $defaultheader = array(
            "MIME-Version: 1.0",
            "Content-type: text/html; charset=" . $charset,
            "Content-transfer-encoding: text",
            "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.104 Safari/537.36",
        );
        $header = array_merge($defaultheader, $inheader);

        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_RETURNTRANSFER => 1,
        );
        curl_setopt_array ($ch, $options);

        $content = curl_exec($ch);
        $tEnd = Log::getLogTime();
        Log::writeLog('httpcode[' . curl_getinfo($ch, CURLINFO_HTTP_CODE) . ']   url[' . $url . ']   ret['.$content.']','info', $tEnd-$tBegin,'interface');

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if(false === $content) {
            echo "2";
            Log::writeLog('CHttp::GetRequest failed ,  httpcode[' . $httpcode . ']  protocol[' . $protocol . ']  url[' . $url . ']', 'warning', $tEnd-$tBegin,'interface');
            curl_close($ch);
            return false;
        }
        curl_close($ch);
        if ($httpcode == 302) {
            if ($num < 3) {
                return self::GetRequest($url, $protocol, $timeout, $inheader, $charset, $num+1);
            }
        }
        if ($httpcode != 200) {
            echo "3";
            Log::writeLog('CHttp::GetRequest failed ,  httpcode[' . $httpcode . ']  protocol[' . $protocol . ']  url[' . $url . ']', 'warning', $tEnd-$tBegin,'interface');
            return false;
        }

        return $content;
    }

    public static function PostContent($url, $data, $protocol = 'http', $timeout = 30, $header=array(), $charset ='utf-8') {
        $tBegin = Log::getLogTime();
        $ch = curl_init();
        if(false == $ch)
        {
            Log::writeLog('CHttp::PostContent curl_init failed ,  protocol[' . $protocol . ']  url[' . $url . ']  data['.var_export($data,true).']', 'error',0,'interface');
            return false;
        }
        if ($protocol === 'https')
        {
            $isHttps = true;
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        }
        $defaultheader = array(
            //"MIME-Version: 1.0",
            //"Content-type: text/html; charset=" . $charset,
            //"Content-transfer-encoding: text",
            "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.104 Safari/537.36",
        );
        $header = array_merge($defaultheader, $header);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        $content = curl_exec($ch);
        $tEnd = Log::getLogTime();
        Log::writeLog('httpcode[' . curl_getinfo($ch, CURLINFO_HTTP_CODE) . ']   url[' . $url . ']  data['.var_export($data,true).']   ret['.$content.']','info', $tEnd-$tBegin,'interface');

        if(false === $content || curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200)
        {
            Log::writeLog('CHttp::PostContent failed ,  httpcode[' . curl_getinfo($ch, CURLINFO_HTTP_CODE) . ']  protocol[' . $protocol . ']  url[' . $url . ']  data['.var_export($data,true).'] ', 'warning', $tEnd-$tBegin,'interface');
            curl_close($ch);
            return false;
        }

        curl_close($ch);
        return $content;
    }

    public static function redirect ($url, $code = 302)
    {
        if ($code == 301) {
            Header('HTTP/1.1 301 Moved Permanently');
        }

        header('Location: ' . $url);
        exit();
    }
    public static function RedirectInTop($url)
    {
        $html = '<html><head></head><script type="text/javascript">'.
                'top.location.href="'.$url.'"'.
                '</script><body></body></html>';

        echo $html;
        die();
    }

    public static function constructGetUrl($baseurl, $arrkeyvalue=array())
    {
        $strUrl = $baseurl . '?';
        $isFirst = true;
        $strUri = self::constructGetUriAttrributes($arrkeyvalue);
        return ($strUrl . $strUri);
    }

    public static function constructGetUriAttrributes($arrkeyvalue=array())
    {
        $strUri = '';
        foreach($arrkeyvalue as $key => $value)
        {
            if(is_array($value))
            {
                foreach($value as &$temp)
                {
                    if($strUri == '')
                        $strUri = $strUri . $key . '=' . urlencode($temp);
                    else
                        $strUri = $strUri . '&' . $key . '=' . urlencode($temp);
                }
            }
            else
            {
                if($strUri == '')
                        $strUri = $strUri . $key . '=' . urlencode($value);
                else
                        $strUri = $strUri . '&' . $key . '=' . urlencode($value);
            }
        }

        return $strUri;
    }


    public static function RedirectInPost($url, $params)
    {
        $html = '<form action="'.$url.'" method="POST" id="id_hidden_form">';

        foreach($params as $key => $value)
        {
            $html .= '<input type="hidden" name="'.$key.'" value=\''.$value.'\'/>';
        }


        $html .= '</form>'.
        $html .= '<script>'.
                'document.getElementById("id_hidden_form").submit();'.
                '</script>';

        echo $html;
    }
}
?>