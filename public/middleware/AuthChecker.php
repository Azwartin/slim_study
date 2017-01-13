<?php
namespace middleware;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \models\Session as Session;

class AuthChecker {
	public function __invoke(Request $req,Response $res, $next) {
		$token = $req->getHeader('Auth-Token')[0];
		if($token && Session::find($token)) {
			return $next($req, $res);
		} else {
			throw new \Exception('Token incorrect', 400);
		}
	}
}