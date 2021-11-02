<?php

namespace App\Http\Controllers;

abstract class AbstractController extends Controller
{
    protected $user;
    protected $compacts;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = $request->user($this->getGuard());

            return $next($request);
        });
    }

    protected function getGuard()
    {
        return property_exists($this, 'guard') ? $this->guard : null;
    }
}
