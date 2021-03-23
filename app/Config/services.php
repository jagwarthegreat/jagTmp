<?php

function view($viewsPath, $datas = [])
{
	// $$params[0] = $params[1];
	include_once "app/Views/" . $viewsPath . ".php";
}
