<?php

namespace Ships\Controllers;

class DefaultController
{
    public function home(): string
    {
        // implement
        return 'Hello World!';
    }

    public function contact(): string
    {
        return 'DefaultController -> contact';
    }

    public function companies($id = null): string
    {
        return 'DefaultController -> companies -> id: ' . $id;
    }

    public function notFound(): string
    {
        return 'Page not found';
    }
}
