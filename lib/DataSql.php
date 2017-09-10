<?php

namespace fw;


class DataSql
{
	public static function fetchOneField($sql){
		$sta = Config::getDefaultDB()->query($sql);
		return $sta->fetchColumn(0);
	}
    public static function fetch($sql){
        $sta = Config::getDefaultDB()->query($sql);
        return $sta->fetchAll(\PDO::FETCH_ASSOC);
    }
    public static function query($sql){
        return Config::getDefaultDB()->query($sql);
    }
}