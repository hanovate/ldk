{{--
layouts.base

Layout page for using ReactJS components

@version 0.1.0 2019-09-27 MH
@since 0.1.1
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta content="IE=edge" http-equiv="X-UA-Compatible"/>
    <title>@yield('title',config('app-extra.browser.default-page-title')) | @yield('main-title',config('app-extra.browser.main-title'))</title>
    <meta content="UNM IT Application group is here to provide the application development needs of the university." name="description"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <link href="//webcore.unm.edu/v1/images/unm.ico" rel="shortcut icon"/>
    <link rel="stylesheet" type="text/css" href="https://cloud.typography.com/7254094/6839152/css/fonts.css" />
    <link href="//webcore.unm.edu/v2/css/unm-styles.min.css" rel="stylesheet"/>
    <link href="{{ mix('css/site-styles.css') }}" rel="stylesheet"/>
    <!--script src="//webcore.unm.edu/v2/js/unm-scripts.min.js" type="text/javascript"></script-->

    <link href="{{ mix('css/app.css') }}" type="text/css" rel="stylesheet">

    <!--link href="{{ mix('css/unm.css') }}" type="text/css" rel="stylesheet"-->

    <link rel="manifest" href="{{ asset('mix-manifest.json') }}">
  </head>
  <body>
    <!-- container to hold menus on collapse -->
    <a accesskey="2" class="sr-only sr-only-focusable skip2content" href="#main">Skip to main content</a>
    <div class="nav-wrapper" id="offcanvas">
        <div class="navbar-header">
            <button class="menu-toggle navbar-toggle collapsed" data-target="#mobile-menu .navbar-collapse" data-toggle="collapse" type="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="fa fa-reorder fa-2x"></span>
            </button>
            <div id="mobile-menu">
                <div class="text-center visible-xs-block" id="mobile-toolbar">
                    <ul aria-label="Resource Links" class="list-unstyled btn-group" role="group">
                        <li class="btn btn-sm btn-default">
                            <a href="http://directory.unm.edu/departments/" title="UNM A to Z">UNM A-Z</a>
                        </li>
                        <li class="btn btn-sm btn-default">
                            <a href="https://my.unm.edu" title="myUNM">myUNM</a>
                        </li>
                        <li class="btn btn-sm  btn-default">
                            <a href="http://directory.unm.edu" title="Directory">Directory</a>
                        </li>
                        <li class="btn btn-sm  btn-default">
                            <a href="https://search.unm.edu" title="Search">
                                <span class="fa fa-search"></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div id="page">
      <!-- start nav -->
      <div class="navbar navbar-unm">
          <div class="container">
              <a class="navbar-brand" href="https://www.unm.edu">The University of New Mexico</a>
              <!-- search form -->
              <form action="//search.unm.edu/search" class="pull-right hidden-xs" id="unm_search_form" method="get">
                  <div class="input-append search-query">
                      <input accesskey="4" id="unm_search_form_q" maxlength="255" name="q" placeholder="Search" title="input search query here" type="text"/>
                      <button accesskey="s" class="btn" id="unm_search_for_submit" name="submit" title="submit search" type="submit">
                          <span class="fa fa-search"></span>
                      </button>
                  </div>
              </form>
              <!-- end search form -->
              <ul class="nav navbar-nav navbar-right hidden-xs" id="toolbar-nav">
                  <li>
                      <a href="http://directory.unm.edu/departments/" title="UNM A to Z">UNM A-Z</a>
                  </li>
                  <li>
                      <a href="https://my.unm.edu" title="myUNM">myUNM</a>
                  </li>
                  <li>
                      <a href="http://directory.unm.edu" title="Directory">Directory</a>
                  </li>
                  <li class="dropdown">
                      <a class="dropdown-toggle" data-toggle="dropdown" href="#">Help
                          <span class="caret"></span>
                      </a>
                      <ul class="dropdown-menu">
                          <li>
                              <a href="https://studentinfo.unm.edu" title="StudentInfo">StudentInfo</a>
                          </li>
                          <li>
                              <a href="https://fastinfo.unm.edu" title="FastInfo">FastInfo</a>
                          </li>
                      </ul>
                  </li>
              </ul>
          </div>
      </div>
      <!-- end  nav -->
      <div id="app-header" class="d-flex justify-content-end">
          <div class="container">
              <a href="https://core.unm.edu">
                  <h1>Chart of Accounts Requests</h1>
              </a>
          </div>
      </div>

      <div id="hero">
        <div class="d-line bg-primary text-white">
          <div class="container">
               Dual-Credit Administration
          </div>
        </div>
      </div>

      {{--
      <div id="hero">
        <div class="carousel slide" data-ride="carousel" id="ucamslider">
          <div class="container">
            <ul class="carousel-indicators nav nav-app-menu nav-justified">
                <li class=""><a href="/business/dual-credit/agreements/institutions">High School/District</a></li>
              <li class="">Course Agreements</li>
              <li class="">Requests</li>
            </ul>
          </div>
        </div>
      </div>
      --}}

      <div id="nav">
        <div class="container">
        </div>
      </div>

      <div id="upper">
          <div class="container">
              <!--UPPER-->
          </div>
      </div>

      <div id="breadcrumbs">
          <div class="container">
              <ul class="breadcrumb hidden-xs" id="unm_breadcrumbs">
                  <li class="unm_home">
                      <a href="https://www.unm.edu">UNM</a>
                  </li>
                  @yield('breadcrumbs',View::make('layouts.breadcrumb'))
              </ul>
          </div>
      </div>

      <div id="main">
          <div class="container layout" id="sc">
              <div class="row">
                  <div id="col">
                    @yield('content')
                  </div>
              </div>
          </div>
      </div>
      <div id="lower">
          <div class="container">
              <!--LOWER-->
          </div>
      </div>
      <!-- footer start -->
      <div id="footer">
          <div class="container">
              <hr/>
              <div class="row">
                  <div class="col-md-8">
                      <p>
                          <a href="https://www.unm.edu">
                              <img alt="The University of New Mexico" src="https://webcore.unm.edu/v2/images/unm-transparent-white.png"/>
                          </a>
                      </p>
                      <p>
                          <small>&#169; The University of New Mexico
                              <br/> Albuquerque, NM 87131, (505) 277-0111
                              <br/> New Mexico's Flagship University
                          </small>
                      </p>
                  </div>
                  <div class="col-md-4">
                      <ul class="list-inline">
                          <li>
                              <a href="https://www.facebook.com/universityofnewmexico" title="UNM on Facebook">
                                  <span class="fa fa-facebook-square fa-2x">
                                      <span class="sr-only">UNM on Facebook</span>
                                  </span>
                              </a>
                          </li>
                          <li>
                              <a href="https://instagram.com/uofnm" title="UNM on Instagram">
                                  <span class="fa fa-instagram fa-2x">
                                      <span class="sr-only">UNM on Instagram</span>
                                  </span>
                              </a>
                          </li>
                          <li>
                              <a href="https://twitter.com/unm" title="UNM on Twitter">
                                  <span class="fa fa-twitter-square fa-2x">
                                      <span class="sr-only">UNM on Twitter</span>
                                  </span>
                              </a>
                          </li>
                          <li>
                              <a href="http://uofnm.tumblr.com" title="UNM on Tumblr">
                                  <span class="fa fa-tumblr-square fa-2x">
                                      <span class="sr-only">UNM on Tumblr</span>
                                  </span>
                              </a>
                          </li>
                          <li>
                              <a href="https://www.youtube.com/user/unmlive" title="UNM on YouTube">
                                  <span class="fa fa-youtube-square fa-2x">
                                      <span class="sr-only">UNM on YouTube</span>
                                  </span>
                              </a>
                          </li>
                      </ul>
                      <p>more at
                          <a href="http://social.unm.edu" title="UNM Social Media Directory &amp; Information">social.unm.edu</a>
                      </p>
                      <ul class="list-inline" id="unm_footer_links">
                          <li>
                              <a href="https://www.unm.edu/accessibility.html">Accessibility</a>
                          </li>
                          <li>
                              <a href="https://www.unm.edu/legal.html">Legal</a>
                          </li>
                          <li>
                              <a href="https://www.unm.edu/contactunm.html">Contact UNM</a>
                          </li>
                          <li>
                              <a href="http://nmhedss2.state.nm.us/Dashboard/index.aspx?ID=21">New Mexico Higher Education Dashboard</a>
                          </li>
                      </ul>
                  </div>
              </div>
          </div>
      </div>
      <!-- footer end -->

    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
    @yield('scripts')
    {{-- LiveReload --}}
    @if (config('app.env') == 'local')
      <script src="{{-- Request::root() --}}127.0.0.1:35729/livereload.js" type="text/javascript"></script>
    @endif
  </body>
</html>
<!-- -->
