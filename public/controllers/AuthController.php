<?php
namespace controllers;
use \interfaces\IController as IController;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \models\User as User;
use \models\Session as Session;

class AuthController implements IController {
	public function bindController(\Slim\app $app) {
		$app->post('/auth/login', function(Request $req, Response $res) {
			$params = $req->getParsedBody();
			if(empty($params['name']) || empty($params['password']) || empty($params['deviceID'])) {
				throw new \Exception('Name, Device ID and Password required', 400);
			}

			$user = User::find(['name' => $params['name']], ['id', 'name', 'password']);
			if($user) {
				if(User::passwordVerify($params['password'], $user[0]['password'])) {
					return self::sendIDWithTokenRefresh($res, $user[0], $params['deviceID']);
				} else {
					throw new \Exception('Login or password incorrect', 400);
				}
			}
		});

		$app->post('/auth', function(Request $req, Response $res) {
			$params = $req->getParsedBody();
			if(empty($params['name']) || empty($params['password']) || empty($params['deviceID'])) {
				throw new \Exception('Name, Device ID and Password required', 400);
			}

			$name = trim($params['name']);
			$user = User::find(['name' => $name]);
			if($user) {
				throw new \Exception('Name is not available', 400);
			} else {
				$user = User::save($name, $params['password']);
				if($user) {
					return self::sendIDWithTokenRefresh($res, $user, $params['deviceID']);
				} else {
					throw new \Exception('User not saved', 500);
				}
			}
		});
	}

	private static function sendIDWithTokenRefresh(Response $res, $user, $deviceID)
	{
		$salt = User::getSalt();
		$token = md5($salt . $user['name']);
		Session::delete($user['id'], $deviceID);
		Session::save($user['id'], $token, $deviceID);

		$res->getBody()->write(json_encode([
			'id' => $user['id'],
			'token' => md5($salt . $user['name'])
		]));

		return $res;
	}
}