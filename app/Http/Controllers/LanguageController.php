<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;

class LanguageController
{
    public function switch($lang)
    {
        Session::put('locale', $lang);

        return redirect()->back();
    }
}
