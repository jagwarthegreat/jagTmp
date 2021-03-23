<?php

function view($viewsPath, $datas = [])
{
	$$datas[0] = $datas[1];
	return include_once "../app/Views/" . $viewsPath . ".php";
}

function page_404()
{
	echo "ERROR 404";
	die();
}
