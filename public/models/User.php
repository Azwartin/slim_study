<?php
namespace models;
use \interfaces\IActiveRecord as IActiveRecord;
use \lib\MySQLConnector as MySQLConnector;

class User {
	private static $tableName = 'users';
	public static function find($params = [], $fields = ['id', 'name'],  $limit = 100, $offset = 0) {
		$fields = join(',', $fields);
		$query = "SELECT $fields FROM " . self::$tableName;
		if($params) {
			$query .= ' WHERE ';
			foreach ($params as $key => $value) {
				$query .= '`' . htmlspecialchars($key) . '`' . '=' . '\'' . htmlspecialchars($value) . '\' and ';		
			}

			$query = substr($query, 0, -4);
		}

		$query .= " LIMIT $offset, $limit";
		$data = MySQLConnector::executeSql($query);
		return $data;
	}

	public static function save($name, $password) {
		$query = 'INSERT INTO ' . self::$tableName . "(name, password) VALUES ('$name', '" . self::passwordHash($password) . '\')';
		MySQLConnector::executeSql($query);
		$query = 'SELECT id, name FROM ' . self::$tableName . " WHERE name = '$name'";
		$data = MySQLConnector::executeSql($query);
		return $data ? $data[0] : false;
	}

	public static function update($id, $fieldsForUpdate) 
	{
		$strUpdate = '';
		foreach ($fieldsForUpdate as $key => $value) {
			$strUpdate = '`' . $key . '`' . '=' . (is_string($value) ? "'$value'" : $value) . ',' ;
		}

		$strUpdate = substr($strUpdate, 0, -1);
		$query = 'UPDATE ' . self::$tableName . ' SET ' . $strUpdate. ' WHERE id = ' . $id;
		$data = MySQLConnector::executeSql($query);
		return $data;
	}

	public static function getSalt() {
		$salt = '';
		$i = 20;
		while($i-- >= 0) {
			$salt .= rand(0, 9);
		}

		return md5($salt);
	}

	public static function passwordHash($password) {
		return password_hash($password, PASSWORD_DEFAULT);
	}

	public static function passwordVerify($password, $hash) {
		return password_verify($password, $hash);
	}
}