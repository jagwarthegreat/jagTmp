<?php

class Testcontroller
{
	public function index()
	{
		$user_data = [
			"name" => "test123123",
			"email" => "j@mail.com"
		];
		return view('test', compact('user_data'));
	}
}
