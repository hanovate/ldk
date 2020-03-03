<!DOCTYPE html SYSTEM "about:legacy-compat">
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <title>@yield('title',config('app-extra.browser.default-page-title')) | @yield('main-title',config('app-extra.browser.main-title')) }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="//webcore.unm.edu/v1/images/unm.ico" rel="shortcut icon"/>

    <link href="{{ mix('css/app.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ mix('css/unm.css') }}" type="text/css" rel="stylesheet">

    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
    <!--
     All css that are site wide are linked in the partial
     -->
    {{-- @include('unmit::layouts.partials.css') --}}
    <!--
     All java scripts that are site wide are linked in the partial
     -->
    {{-- @include('unmit::layouts.partials.top-load-js') --}}
  </head>
  <body>
      <!-- container to hold menus on collapse --><a accesskey="2" class="sr-only sr-only-focusable skip2content" href="#main">Skip to main content</a>
      <div class="nav-wrapper" id="offcanvas">
         <div class="navbar-header">
            <button class="menu-toggle navbar-toggle collapsed" data-target="#mobile-menu .navbar-collapse" data-toggle="collapse" type="button"><span class="sr-only">Toggle navigation</span><span class="fa fa-reorder fa-2x"></span></button>
            <div id="mobile-menu">
               <div class="text-center visible-xs-block" id="mobile-toolbar">
                  <ul aria-label="Resource Links" class="list-unstyled btn-group" role="group">
                     <li class="btn btn-sm btn-default"><a href="http://directory.unm.edu/departments/" title="UNM A to Z">UNM A-Z</a></li>
                     <li class="btn btn-sm btn-default"><a href="https://my.unm.edu" title="myUNM">myUNM</a></li>
                     <li class="btn btn-sm  btn-default"><a href="http://directory.unm.edu" title="Directory">Directory</a></li>
                     <li class="btn btn-sm  btn-default"><a href="http://search.unm.edu" title="Search"><span class="fa fa-search"></span></a></li>
                  </ul>
               </div>
            </div>
         </div>
      </div>
      <div id="page">
         <!-- start nav -->
         <div class="navbar navbar-unm">
            <div class="container">
               <a class="navbar-brand" href="http://www.unm.edu">The University of New Mexico</a><!-- search form -->
               <form action="//search.unm.edu/search" class="pull-right hidden-xs" id="unm_search_form" method="get">
                  <div class="input-append search-query"><input accesskey="4" id="unm_search_form_q" maxlength="255" name="q" placeholder="Search" title="input search query here" type="text"/><button accesskey="s" class="btn" id="unm_search_for_submit" name="submit" title="submit search" type="submit"><span class="fa fa-search"></span></button></div>
               </form>
               <!-- end search form -->
               <ul class="nav navbar-nav navbar-right hidden-xs" id="toolbar-nav">
                  <li><a href="http://directory.unm.edu/departments/" title="UNM A to Z">UNM A-Z</a></li>
                  <li><a href="https://my.unm.edu" title="myUNM">myUNM</a></li>
                  <li><a href="http://directory.unm.edu" title="Directory">Directory</a></li>
                  <li class="dropdown">
                     <a class="dropdown-toggle" data-toggle="dropdown" href="#">Help <span class="caret"></span></a>
                     <ul class="dropdown-menu">
                        <li><a href="http://studentinfo.unm.edu" title="StudentInfo">StudentInfo</a></li>
                        <li><a href="http://fastinfo.unm.edu" title="FastInfo">FastInfo</a></li>
                     </ul>
                  </li>
               </ul>
            </div>
         </div>
         <!-- end  nav -->
         <div id="header">
            <div class="container">
               <a href="http://ucam.unm.edu">
                  <h1>University Communication &amp; Marketing</h1>
               </a>
            </div>
         </div>
         <div id="hero">
            <div class="carousel slide" data-ride="carousel" id="ucamslider">
               <div class="carousel-inner">
                  <div class="item active unm-cherry">
                     <div class="container">
                        <div class="bannerInfo">
                           <br/>
                           <h2 class="textshadow">Marketing</h2>
                           <p>With the increasing number of marketing vehicles available, delivering a message today is more complicated than ever. Our planning and creative approach examines the &#8220;big picture,&#8221; identifying ways to effectively engage with our constituents.</p>
                           <p><a class="btn btn-default" href="marketing/index.html">More About Marketing</a></p>
                        </div>
                     </div>
                  </div>
                  <div class="item unm-green">
                     <div class="container">
                        <div class="bannerInfo">
                           <br/>
                           <h2 class="textshadow">Media Relations</h2>
                           <p>Our staff pitch stories to members of the news media on behalf of UNM faculty and departments and assist in media requests. UCAM staff can help faculty and staff experts feel more comfortable speaking in front of news reporters and camera crews.</p>
                           <p><a class="btn btn-default" href="media-relations/index.html">More About Media Relations</a></p>
                        </div>
                     </div>
                  </div>
                  <div class="item unm-blue">
                     <div class="container">
                        <div class="bannerInfo">
                           <br/>
                           <h2 class="textshadow">News &amp; Media</h2>
                           <p><strong>News Services -&#160;</strong>Staff produce print, audio and video stories and expert interviews for departments across campus. These stories are featured on UNM Today (print and online), the UNM homepage and other UNM venues.</p>
                           <p><a class="btn btn-default" href="news-services/index.html">More About News Services</a></p>
                        </div>
                     </div>
                  </div>
                  <div class="item unm-purple">
                     <div class="container">
                        <div class="bannerInfo">
                           <br/>
                           <h2 class="textshadow">Social Media</h2>
                           <p>UCAM&#8217;s social media experts create a cohesive social media presence through the university's Facebook, Twitter, YouTube and other social media and social networking tools. UCAM also provides assistance for departments looking to start or improve their own social media initiatives.</p>
                           <p><a class="btn btn-default" href="social-media/index.html">More About Social Media</a></p>
                        </div>
                     </div>
                  </div>
                  <div class="item unm-teal">
                     <div class="container">
                        <div class="bannerInfo">
                           <br/>
                           <h2 class="textshadow">Web Services</h2>
                           <p>Our web team manages the main UNM website as well as provides services to departments.&#160;Web services staff can help your department with web related needs including &#160;web site maintenance, feature enhancements, or migrating your website into the university's Web content management system.</p>
                           <p><a class="btn btn-default" href="web-communications/index.html">More About Web</a></p>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="ucamslidercontrols">
                  <div class="container">
                     <div>
                        <ul class="carousel-indicators nav nav-justified">
                           <li class="active" data-slide-to="0" data-target="#ucamslider">Marketing</li>
                           <li data-slide-to="1" data-target="#ucamslider">Media Relations</li>
                           <li data-slide-to="2" data-target="#ucamslider">News Services</li>
                           <li data-slide-to="3" data-target="#ucamslider">Social Media</li>
                           <li data-slide-to="4" data-target="#ucamslider">Web Services</li>
                        </ul>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div id="nav">
            <div class="container"></div>
         </div>
         <div id="upper">
            <div class="container">
               <!--UPPER-->
            </div>
         </div>
         <div id="breadcrumbs">
            <div class="container">
               <ul class="breadcrumb hidden-xs" id="unm_breadcrumbs">
                  <li class="unm_home"><a href="http://www.unm.edu">UNM</a></li>
                  <li>UCAM Home</li>
               </ul>
            </div>
         </div>
         <div id="main">
            <div class="container layout" id="scs">
               <div class="row">
                  <div id="primary">
                     <h1>UNM is a great story that will never be completely told.</h1>
                     <p>Welcome to the University Communications and Marketing (UCAM) website. We invite you to explore and learn more about our services and projects, which promote the University of New Mexico and its programs.</p>
                     <p>UCAM is the primary source for accurate and current information about the University of New Mexico. The office advances UNM&#8217;s position as the state&#8217;s flagship university and a leader in higher education by promoting the University's people, programs, and educational philosophy, and by publicizing its achievements and resources to a wide array of internal and external audiences. Our professional staff works with external news organizations, produces a range of marketing materials, and creates and maintains a strategic web presence and multimedia content to ensure clear, concise communication of ideas and initiatives.</p>
                     <p>At UCAM, we work hard to showcase UNM&#8217;s extraordinary accomplishments, as well as to build national and international awareness of the university. Our team of talented communication, marketing, and web professionals welcomes the opportunity to collaborate with you. Together, we will work to take your program and UNM to new heights of success. Thank you for visiting. We hope to work with you soon!</p>
                     <div class="well">
                        <div class="row">
                           <img alt="brand" height="230" src="common/images/brand.png" width="350"/><br/>
                           <div class="col-sm-8">
                              <h2><span>UNM Brand Style Guide</span></h2>
                              <p><span>Our brand is more than a logo or&#160;</span><span>a color palette. It's the collection of experiences that people</span><br/><span>associate with UNM. It's the voice and tone we use to tell our</span><br/><span>story. It's the images and colors we use to communicate to our</span><br/><span>audiences. It's the campus, our community, and our passion. Used</span><br/><span>properly, the UNM Brand Style Guide will help maintain graphic and</span><br/><span>message continuity, protect our logos, and help us build our brand</span><br/><span>across a wide spectrum of media</span>.</p>
                              <p><a class="btn btn-primary" href="http://brand.unm.edu/ "><span class="caption">UNM Brand Style Guide</span></a></p>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div id="secondary">
                     <div class="sidebar-nav-wrapper">
                        <nav class="navbar-collapse collapse sidebar-nav" id="sidebar-nav">
                           <ul class="nav" id="contextual_nav">
                              <li><a class="active" href="index.html" title="UCAM Home">UCAM Home</a></li>
                              <li class="submenu">
                                 <a class="collapsed" data-toggle="collapse" href="#about-submenu" title="About Submenu">About</a>
                                 <ul class="nav collapse" id="about-submenu">
                                    <li><a href="about/index.html" title="About - About UCAM">About UCAM</a></li>
                                    <li><a href="about/staff/index.html" title="UCAM Staff Submenu">UCAM Staff</a></li>
                                    <li><a href="about/awards.html" title="About - Awards">Awards</a></li>
                                 </ul>
                              </li>
                              <li class="submenu">
                                 <a class="collapsed" data-toggle="collapse" href="#marketing-submenu" title="Marketing Submenu">Marketing</a>
                                 <ul class="nav collapse" id="marketing-submenu">
                                    <li><a href="marketing/the-unm-brand.html" title="Marketing - Licensing &amp; Trademarks">Licensing &amp; Trademarks</a></li>
                                    <li><a href="marketing/identity-standards.html" title="Marketing - UNM Identity Standards">UNM Identity Standards</a></li>
                                    <li><a href="marketing/film-guidelines/index.html" title="Film Guidelines Submenu">Film Guidelines</a></li>
                                    <li><a href="marketing/design-templates/index.html" title="Design Templates Submenu">Design Templates</a></li>
                                    <li><a href="marketing/green-logos/index.html" title="Green Logos Submenu">Green Logos</a></li>
                                    <li><a href="marketing/logos/index.html" title="Logos Submenu">Logos</a></li>
                                 </ul>
                              </li>
                              <li><a href="media-relations/index.html" title="Media Relations Submenu">Media Relations</a></li>
                              <li class="submenu">
                                 <a class="collapsed" data-toggle="collapse" href="#news-services-submenu" title="News Services Submenu">News Services</a>
                                 <ul class="nav collapse" id="news-services-submenu">
                                    <li><a href="news-services/index.html" title="News Services - About News Services">About News Services</a></li>
                                    <li><a href="news-services/submit-story.html" title="News Services - Submit A Story">Submit A Story</a></li>
                                 </ul>
                              </li>
                              <li><a href="social-media/index.html" title="Social Media Submenu">Social Media</a></li>
                              <li><a href="web-communications/index.html" title="Web Communications Submenu">Web Communications</a></li>
                              <li><a href="events/index.html" title="UNM Events Calendar Submenu">UNM Events Calendar</a></li>
                              <li><a href="jobs/index.html" title="Jobs Submenu">Jobs</a></li>
                           </ul>
                        </nav>
                     </div>
                     <a href="http://news.unm.edu"><strong>UNM Newsroom</strong></a><!-- content block -->
                     <hr/>
                     <div class="vcard leftnav_contact">
                        <a class="fn organization-unit url" href="http://ucam.unm.edu">University Communication &amp; Marketing</a>
                        <p class="adr"><span class="extended-address">MSC06 3745, Box 26</span><br/><span class="street-address">1 University of New Mexico</span><br/><span class="locality">Albuquerque</span>, <abbr class="region" title="New Mexico">NM</abbr><span class="postal-code">87131</span></p>
                        <p><strong>Physical Location:</strong><br/><span class="extended-address">UAEC Building 85 <br/>Suite 160</span></p>
                        <p><span class="tel"><span class="type hidden">Work</span> Phone: <span class="value">(505) 277-5813</span><br/></span><span><a class="email" href="mailto:ucam@unm.edu">ucam@unm.edu</a></span></p>
                     </div>
                  </div>
                  <div id="tertiary">
                     <div class="well">
                        <h2 class="cherry">Media Emergencies</h2>
                        <p>(505) 750-0866</p>
                     </div>
                     <meta charset="utf-8"/>
                     <div class="quickfindarea">
                        <h2>Publications</h2>
                        <ul>
                           <!--
                              <li><a title="News Releases" href="http://news.unm.edu/news-releases/">News Releases</a></li>
                              -->
                           <li><a href="http://news.unm.edu">UNM Newsroom</a></li>
                        </ul>
                     </div>
                     <div class="quickfindarea">
                        <h2>Other Resources</h2>
                        <ul>
                           <li><a href="http://hscnews.unm.edu/">HSC News</a></li>
                           <li><a href="http://golobos.cstv.com/">Athletics</a></li>
                           <li><a href="http://publicrecords.unm.edu/">Inspection of Public Records</a></li>
                           <li><a href="http://marcomm.unm.edu">Marketing &amp; Communication</a></li>
                        </ul>
                     </div>
                     <div class="quickfindarea">
                        <h2>Web Resources</h2>
                        <ul>
                           <li><a href="http://webmaster.unm.edu/wcms">Content Management</a></li>
                           <li><a href="http://webmaster.unm.edu">UNM Webmaster</a></li>
                           <li><a href="http://webmaster.unm.edu/web-advisory">Web Advisory Committee</a></li>
                           <li><a href="http://demo.unm.edu/">Web Templates</a></li>
                        </ul>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div id="lower">
            <div class="container">
               <!--LOWER-->
            </div>
         </div>
         <div id="footer">
            <div class="container">
               <hr/>
               <div class="row">
                  <div class="col-md-8">
                     <p><a href="http://www.unm.edu"><img alt="The University of New Mexico" src="https://webcore.unm.edu/v2/images/unm-transparent-white.png"/></a></p>
                     <p><small>&#169; The University of New Mexico<br/> Albuquerque, NM 87131, (505) 277-0111<br/> New Mexico's Flagship University</small></p>
                  </div>
                  <div class="col-md-4">
                     <ul class="list-inline">
                        <li><a href="https://www.facebook.com/universityofnewmexico" title="UNM on Facebook"><span class="fa fa-facebook-square fa-2x"><span class="sr-only">UNM on Facebook</span></span></a></li>
                        <li><a href="http://instagram.com/uofnm" title="UNM on Instagram"><span class="fa fa-instagram fa-2x"><span class="sr-only">UNM on Instagram</span></span></a></li>
                        <li><a href="https://twitter.com/unm" title="UNM on Twitter"><span class="fa fa-twitter-square fa-2x"><span class="sr-only">UNM on Twitter</span></span></a></li>
                        <li><a href="http://uofnm.tumblr.com" title="UNM on Tumblr"><span class="fa fa-tumblr-square fa-2x"><span class="sr-only">UNM on Tumblr</span></span></a></li>
                        <li><a href="http://www.youtube.com/user/unmlive" title="UNM on YouTube"><span class="fa fa-youtube-square fa-2x"><span class="sr-only">UNM on YouTube</span></span></a></li>
                     </ul>
                     <p>more at <a href="http://social.unm.edu" title="UNM Social Media Directory &amp; Information">social.unm.edu</a></p>
                     <ul class="list-inline" id="unm_footer_links">
                        <li><a href="http://www.unm.edu/accessibility.html">Accessibility</a></li>
                        <li><a href="http://www.unm.edu/legal.html">Legal</a></li>
                        <li><a href="http://www.unm.edu/contactunm.html">Contact UNM</a></li>
                        <li><a href="http://nmhedss2.state.nm.us/Dashboard/index.aspx?ID=21">New Mexico Higher Education Dashboard</a></li>
                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div id="totop"><span class="fa fa-arrow-circle-up"></span></div>
   </body>
</html>
