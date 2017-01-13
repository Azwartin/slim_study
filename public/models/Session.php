<?php
namespace models;
use \lib\MySQLConnector as MySQLConnector;

class Session {
	private static $tableName = 'sessions';
	public static function find($token) {
		$query = 'SELECT * FROM ' . self::$tableName . " WHERE token = '$token'"; 
		$data = MySQLConnector::executeSql($query);
		return $data ? $data[0]['user_id'] : false;
	}

	public static function delete($userId, $deviceId = false) {
		$query = 'DELETE FROM ' . self::$tableName . ' WHERE user_id = ' . $userId;
		if($deviceId) {
			$query .= " AND device_id = '$deviceId'"; 
		}

		return MySQLConnector::executeSql($query);
	}

	public static function save($userId, $token, $deviceID) {
		$query = 'INSERT INTO ' . self::$tableName . "(user_id, token, device_id) VALUES ($userId, '$token' , '$deviceID')"; 
		return MySQLConnector::executeSql($query);
	}
}