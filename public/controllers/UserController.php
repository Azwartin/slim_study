<?php
namespace controllers;
use \interfaces\IController as IController;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \models\User as User;
use \middleware\AuthChecker as Auth;

class UserController implements IController {
	public function bindController(\Slim\app $app) {
		$app->get('/users', function(Request $req, Response $res) {
			$params = $req->getParsedBody();
			$limit = empty($params['limit']) ? 25 : (int) $params['limit'];
			$offset = empty($params['offset']) ? 0 : (int) $params['offset'];
			$users = User::find([], ['id', 'name'], $limit, $offset);
			$res->getBody()->write(json_encode($users));
			return $res;
		})->add(Auth::class);

		$app->patch('/users/{id}', function(Request $req, Response $res) {
			$user = User::find(['id' => $req->getAttribute('id')]);
			if($user) {
				$params = $req->getParsedBody();
				$fieldForUpdate = [];
				if(!empty($params['name'])) {
					$name = trim($params['name']);
					if(!User::find(['name' => $name])) {
						$fieldForUpdate['name'] = $name;
					} else {
						throw new \Exception('Name is not available', 400);
					}
				}

				if(!empty($params['password'])) {
					$fieldForUpdate['password'] = User::passwordHash($params['password']);
				}

				if($fieldForUpdate) {
					$user = $user[0];
					if(User::update($user['id'], $fieldForUpdate)) {
						if(!empty($fieldForUpdate['password'])) {
							Session::delete($user['id']);
						}

						$res->getBody()->write(json_encode(['status' => 'ok']));
						return $res;
					} else {
						throw new \Exception('Not updated', 500);
					}
				}
			}			
		})->add(Auth::class);;

		$app->get('/users/{id}', function(Request $req, Response $res) {
			$user = User::find(['id' => $req->getAttribute('id')]);
			if($user) {
				$res->getBody()->write(json_encode($user[0]));
				return $res;
			}
			
			throw new \Exception('User not found', 400);
		})->add(Auth::class);
	}
}