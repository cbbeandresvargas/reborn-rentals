<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about()
    {
        return view('about-us');
    }

    public function faq()
    {
        return view('faq');
    }

    public function fees()
    {
        return view('fees-surcharges');
    }

    public function privacy()
    {
        return view('privacy-policy');
    }

    public function sitemap()
    {
        return view('site-map');
    }

    public function terms()
    {
        return view('terms-conditions');
    }

    public function directions()
    {
        return view('directions');
    }

    public function blog()
    {
        return view('blog');
    }
}
