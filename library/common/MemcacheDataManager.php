<?php

class MemcacheDataManager
{
    private static $instance = NULL;
    private $objMemcache;
    private $session_values;
    private $cache_values;

    public static function getInstance()
    {
        if(!(self::$instance instanceof self))
        {
            self::$instance = new MemcacheDataManager();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->session_values = array();
        $this->cache_values = array();
        $this->objMemcache = new CMemcache(ConfigManager::$config['params']['memcache_addrs']);
    }

    public function __destruct()
    {
        $this->updateSessionDataExpire(Session::getSessionId());
    }

    public function setValue($sessionId, $key, $value, $expire = 1800)
    {
        if (empty($sessionId)) {
            $this->cache_values[$key] = array('value'=>$value,'expire'=>$expire);
        } else {
            $memcacheKey = $sessionId .'_'. $key;
            $this->session_values[$memcacheKey] = $value;
        }

        return true;
    }

    public function getValue($sessionId, $key)
    {
        if (empty($sessionId)) {
            if(array_key_exists($key, $this->cache_values)) {
                return $this->cache_values[$key]['value'];
            }
            $value = $this->objMemcache->GetValue($key);
            if(isset($value) && $value!="" && $value!=false) {
                $this->cache_values[$key] = array('value'=>$value);
                return $value;
            }
        } else {
            $memcacheKey = $sessionId .'_'. $key;
            if(array_key_exists($memcacheKey, $this->session_values)) {
                return $this->session_values[$memcacheKey];
            }
            $value = $this->objMemcache->GetValue($memcacheKey);
            if(isset($value) && $value!="" && $value!=false) {
                $this->session_values[$memcacheKey] = $value;
                return $value;
            }
        }

        return false;
    }

    public function delete($sessionId, $key)
    {
        if (empty($sessionId)) {
            if(array_key_exists($key,$this->cache_values)) {
                unset($this->cache_values[$key]);
            }
            $this->objMemcache->delete($key);
        } else {
            $memcacheKey = $sessionId .'_'. $key;
            if(array_key_exists($memcacheKey,$this->session_values))
            {
                unset($this->session_values[$memcacheKey]);
            }
            $this->objMemcache->delete($memcacheKey);
        }
    }

    public function getSessionValues($sessionId)
    {
        $keyName = 'session_'. $sessionId . '_keys';

        $arrRet = $this->session_values;
        $arrKeysInSession = $this->objMemcache->GetValue($keyName);
        if(isset($arrKeysInSession) && false != $arrKeysInSession)
        {
            foreach($arrKeysInSession as $memKey)
            {
                if(!array_key_exists($memKey, $this->session_values))
                {
                    $value = $this->objMemcache->GetValue($memKey);
                    if(isset($value) && false != $value)
                    {
                        $arrRet[$memKey] = $value;
                    }
                }
            }
        }

        return $arrRet;
    }

    public function cleanSession($sessionId)
    {
        $keyName = 'session_'. $sessionId . '_keys';
        $arrKeysInSession = $this->objMemcache->GetValue($keyName);
        if(isset($arrKeysInSession) && false != $arrKeysInSession)
        {
            foreach($arrKeysInSession as $memKey)
            {
                $this->objMemcache->delete($memKey);
            }
        }

        $this->session_values = array();
    }

    private function updateSessionDataExpire($sessionId)
    {
        foreach($this->cache_values as $memKey => $value)
        {
            if (isset($value['expire'])) {
                $this->objMemcache->setValue($memKey, $value['value'], $value['expire']);
            }
        }

        if (empty($sessionId)) {
            return;
        }
        $arrayValues = $this->getSessionValues($sessionId);
        if (empty($arrayValues)) {
            return;
        }
        foreach($arrayValues as $memKey => $value)
        {
            $this->objMemcache->setValue($memKey, $value);
        }

        $keyName = 'session_'. $sessionId . '_keys';
        $this->objMemcache->setValue($keyName, array_keys($arrayValues));
    }

}
