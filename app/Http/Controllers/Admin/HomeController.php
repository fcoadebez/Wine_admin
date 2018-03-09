<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
	public function home(Request $request){
		if (!$request->session()->exists('user')) {
			return redirect("/admin/user/login");
		}

		return redirect('/admin/wine/list');
	}
}
