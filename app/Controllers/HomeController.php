<?php

class HomeController
{
    public function index()
    {
        view('layout/header');
        view('home');
        view('layout/footer');
    }
}
