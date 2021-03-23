<?php

class LoginController
{
	public function index()
	{
		$test = "";
		$user_data = [
			"name" => "test123123",
			"email" => "j@mail.com"
		];

		view('auth/login');
	}
}
