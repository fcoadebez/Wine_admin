<nav class="navbar navbar-full navbar-inverse navbar-fixed-top mai-top-header">
	<div class="container">
		<!-- <a href="{{ URL::to('/admin') }}" class="navbar-brand"></a> -->
		<img src="{{ asset('web-admin/assets/img/logo-2x.png') }}" alt="logo" width="239"
                    class="logo-img mb-4" style="width: 70px; height: 70px; margin-top: 1.5rem;">
		<!--Left Menu-->
		<ul class="nav navbar-nav mai-top-nav">
			
		</ul>

	<!--User Menu-->
		<ul class="nav navbar-nav float-lg-right mai-user-nav">
			<li class="dropdown nav-item">
				<a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="dropdown-toggle nav-link">
					<!-- <img src="{{ asset('web-admin/assets/img/avatar.png')}}"> -->
					<span class="user-name">{{ strtoupper(app('request')->session()->get('user')->name) }} {{app('request')->session()->get('user')->firstname}}</span>
					<span class="angle-down s7-angle-down" style="color: #A83057"></span>
				</a>
				<div role="menu" class="dropdown-menu">
					<a href="{{ URL::to('/') }}" class="dropdown-item">
						<span class="icon s7-home"></span>&nbsp; Accèder au site
					</a>
					<a href="{{ URL::to('/admin/user/password') }}" class="dropdown-item"> &nbsp;
						<span class="icon s7-key"></span>&nbsp;&nbsp; Modifier mon mot de passe
					</a>
					<a href="{{ URL::to('/admin/user/logout') }}" class="dropdown-item">
						<span class="icon s7-power"></span>&nbsp; Déconnexion
					</a>
				</div>
			</li>
		</ul>
	</div>
</nav>