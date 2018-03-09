<nav class="navbar mai-sub-header">
	<div class="container">
		<!-- Mega Menu structure-->
		<nav class="navbar navbar-toggleable-sm">
			<button type="button" data-toggle="collapse" data-target="#mai-navbar-collapse" aria-controls="#mai-navbar-collapse" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler hidden-md-up collapsed">
				<div class="icon-bar">
					<span></span>
					<span></span>
					<span></span>
				</div>
			</button>
			<div id="mai-navbar-collapse" class="navbar-collapse collapse mai-nav-tabs">
				<ul class="nav navbar-nav">
					<li class="nav-item parent {{ (str_contains(Request::path(), 'admin/vins') ? 'open' : '') }}">
						<a href="{{ URL::to('/admin/wine/list') }}" role="button" aria-expanded="false" class="nav-link">
							<span class="icon s7-wine"></span>
							<span>Vins</span>
						</a>
					</li>
					<li class="nav-item parent {{ (str_contains(Request::path(), 'admin/questions') ? 'open' : '') }}">
						<a href="{{ URL::to('/admin/questions/list') }}" role="button" aria-expanded="false" class="nav-link">
							<span class="icon s7-note2"></span>
							<span>Questions</span>
						</a>
					</li>
					<li class="nav-item parent {{ (str_contains(Request::path(), 'admin/settings') ? 'open' : '') }}">
						<a href="{{ URL::to('/admin/settings') }}" role="button" aria-expanded="false" class="nav-link">
							<span class="icon s7-tools"></span>
							<span>Paramètres</span>
						</a>
					</li>
				</ul>
				<div class="clear"></div>
			</div>
		</nav>
		{{--
			<!--Search input-->
			<div class="search">
				<input type="text" placeholder="Search..." name="q"><span class="s7-search"></span>
			</div>
			--}}
	</div>
</nav>