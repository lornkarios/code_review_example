<?php

class Example
{
    function InitCache($TTL, $uniq_str, $initdir = false, $basedir = "cache")
    {
        /** @global CMain $APPLICATION */
        global $APPLICATION, $USER;
        if($initdir === false)
            $initdir = $APPLICATION->GetCurDir();

        $this->basedir = BX_PERSONAL_ROOT."/".$basedir."/";
        $this->initdir = $initdir;
        $this->filename = "/".CPageCache::GetPath($uniq_str);
        $this->TTL = $TTL;
        $this->uniq_str = $uniq_str;

        if($TTL<=0)
            return false;

        if(is_object($USER) && $USER->CanDoOperation('cache_control'))
        {
            if(isset($_GET["clear_cache_session"]))
            {
                if(strtoupper($_GET["clear_cache_session"])=="Y")
                    $_SESSION["SESS_CLEAR_CACHE"] = "Y";
                elseif(strlen($_GET["clear_cache_session"]) > 0)
                    unset($_SESSION["SESS_CLEAR_CACHE"]);
            }

            if(isset($_GET["clear_cache"]) && strtoupper($_GET["clear_cache"])=="Y")
                return false;
        }

        if(isset($_SESSION["SESS_CLEAR_CACHE"]) && $_SESSION["SESS_CLEAR_CACHE"] == "Y")
            return false;

        if(!$this->_cache->read($this->content, $this->basedir, $this->initdir, $this->filename, $this->TTL))
            return false;

//		$GLOBALS["CACHE_STAT_BYTES"] += $this->_cache->read;
        if (\Bitrix\Main\Data\Cache::getShowCacheStat())
        {
            $read = 0;
            $path = '';
            if ($this->_cache instanceof \Bitrix\Main\Data\ICacheEngineStat)
            {
                $read = $this->_cache->getReadBytes();
                $path = $this->_cache->getCachePath();
            }
            elseif ($this->_cache instanceof \ICacheBackend)
            {
                /** @noinspection PhpUndefinedFieldInspection */
                $read = $this->_cache->read;

                /** @noinspection PhpUndefinedFieldInspection */
                $path = $this->_cache->path;
            }

            \Bitrix\Main\Diag\CacheTracker::addCacheStatBytes($read);
            \Bitrix\Main\Diag\CacheTracker::add($read, $path, $this->basedir, $this->initdir, $this->filename, "R");
        }
        return true;
    }

}