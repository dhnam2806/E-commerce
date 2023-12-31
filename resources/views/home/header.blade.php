<header class="header_section">
    <div class="container">
        <nav class="navbar navbar-expand-lg custom_nav-container ">
            <a class="navbar-brand" href="/"><img width="240" src="images/logo1.png" alt="#" /></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class=""> </span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav">
                    <li class="nav-item active">
                        <a class="nav-link" href="{{ url('/') }}">Home <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true"> <span class="nav-label">Category <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            @foreach($category as $category)
                            <li><a href="{{ url('get_product_by_category', $category->category_name) }}">{{ $category->category_name }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('get_all_products') }}">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="blog_list.html">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.html">Contact</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('show_cart') }}"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a>
                    </li>

                    <form class="form-inline">
                        <button class="btn  my-2 my-sm-0 nav_search-btn" type="submit">
                            <i class="fa fa-search" aria-hidden="true"></i>
                        </button>
                    </form>
                    @if (Route::has('login'))

                    @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true"> <span class="nav-label">{{ Auth::user()->name }} <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <input class="btn mx-2 py-2" type="submit" value="Logout">
                                </form>
                            </li>
                            <li><a href="{{ url('/get_profile') }}">Profile</a></li>
                        </ul>
                    </li>
                    @else
                    <li class="nav-item mx-2">
                        <a class="btn" href="{{ route('login') }}" style="background-color: #A0E9FF;">Sign in</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn" style="background-color: #F875AA;" href="{{ route('register') }}">Sign up</a>
                    </li>
                    @endauth
                    @endif

                </ul>
            </div>
        </nav>
    </div>
</header>