<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
    <head>
        <?php $this->insert('partials/head') ?>
    </head>
    <body class="site">
        <?php $this->insert('partials/gtm') ?>
        <nav class="navigation" role="navigation">
            <div id="mega-menu" class="navbar navbar-inverse navbar-fixed-top">
                <div class="navbar-inner">
                    <div class="container">
                        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </a>

                        <div class="nav-collapse collapse">
                            <ul id="nav-joomla" class="nav">
                                <li class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                        <span aria-hidden="true" class="icon-joomla"></span> Joomla!<sup>&reg;</sup> <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="https://www.joomla.org">
                                                <span aria-hidden="true" class="icon-joomla"></span> Joomla! Home
                                            </a>
                                        </li>
                                        <li class="divider"><span></span></li>
                                        <li class="nav-header"><span>Recent News</span></li>
                                        <li><a href="https://www.joomla.org/announcements.html">Announcements</a></li>
                                        <li><a href="https://community.joomla.org/blogs.html">Blogs</a></li>
                                        <li><a href="http://magazine.joomla.org">Magazine</a></li>
                                        <li class="divider"><span></span></li>
                                        <li class="nav-header"><span>Support Joomla!</span></li>
                                        <li><a href="https://volunteers.joomla.org">Contribute</a></li>
                                        <li><a href="https://shop.joomla.org">Shop Joomla Gear</a></li>
                                        <li><a href="https://www.joomla.org/sponsorship">Sponsorship</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">About <span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="https://www.joomla.org/about-joomla.html">About Joomla!</a></li>
                                        <li><a href="https://www.joomla.org/core-features.html">Core Features</a></li>
                                        <li><a href="https://www.joomla.org/about-joomla/the-project.html">The Project</a></li>
                                        <li><a href="https://www.joomla.org/about-joomla/the-project/leadership-team.html">Leadership</a></li>
                                        <li><a href="http://opensourcematters.org">Open Source Matters</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Community <span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="https://community.joomla.org">Joomla! Community Portal</a></li>
                                        <li><a href="https://events.joomla.org">Joomla! Events</a></li>
                                        <li><a href="https://tm.joomla.org">Joomla! Trademark &amp; Licensing</a></li>
                                        <li><a href="https://community.joomla.org/user-groups.html">Joomla! User Groups</a></li>
                                        <li><a href="https://volunteers.joomla.org">Joomla! Volunteers Portal</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Support <span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="http://forum.joomla.org">Joomla! Forum</a></li>
                                        <li><a href="https://docs.joomla.org">Joomla! Documentation</a></li>
                                        <li><a href="https://issues.joomla.org">Joomla! Issue Tracker</a></li>
                                        <li><a href="http://resources.joomla.org">Joomla! Resources Directory</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Read <span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="http://magazine.joomla.org">Joomla! Magazine</a></li>
                                        <li><a href="https://community.joomla.org/connect.html">Joomla! Connect</a></li>
                                        <li><a href="https://www.joomla.org/mailing-lists.html">Joomla! Mailing Lists</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Extend <span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="http://extensions.joomla.org">Extension Directory</a></li>
                                        <li><a href="http://showcase.joomla.org">Showcase Directory</a></li>
                                        <li><a href="https://community.joomla.org/translations.html">Language Packages</a></li>
                                        <li><a href="https://certification.joomla.org">Certification Program</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Developers <span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="https://developer.joomla.org">Developer Network</a></li>
                                        <li><a href="https://docs.joomla.org">Documentation</a></li>
                                        <li><a href="https://docs.joomla.org/Bug_Squad">Joomla! Bug Squad</a></li>
                                        <li><a href="https://api.joomla.org">Joomla! API</a></li>
                                        <li><a href="http://joomlacode.org">JoomlaCode</a></li>
                                        <li><a href="https://framework.joomla.org">Joomla! Framework</a></li>
                                    </ul>
                                </li>
                            </ul>
                            <ul id="nav-international" class="nav pull-right">
                                <li class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                        <span aria-hidden="true" class="icon-earth"></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a href="https://community.joomla.org/translations.html">Language Packages</a></li>
                                        <li><a href="https://demo.joomla.org/multilingual">Multilingual Demo</a></li>
                                        <li><a href="https://docs.joomla.org/Translations_Working_Group">Translation Working Group</a></li>
                                        <li><a href="http://forum.joomla.org/viewforum.php?f=11">Translations Forum</a></li>
                                    </ul>
                                </li>
                            </ul>
                            <div id="nav-search" class="navbar-search pull-right">
                                <form action="https://api.joomla.org/results.html" id="searchbox_007628682600509520926:myhvbnm964o" class="form-search">
                                    <input type="text" name="q" id="api-google-search" class="search-query input-medium" size="20" placeholder="Search..." autocomplete="off">
                                    <input type="hidden" name="cx" value="007628682600509520926:myhvbnm964o">
                                    <input name="siteurl" type="hidden" value="api.joomla.org/">
                                    <input name="ref" type="hidden" value="api.joomla.org/">
                                    <input name="ss" type="hidden" value="">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <header class="header">
            <div class="container">
                <div class="row-fluid">
                    <div class="span7">
                        <h1 class="page-title">Joomla! Help Site</h1>
                    </div>
                    <div class="span5">
                        <div class="btn-toolbar pull-right">
                            <div class="btn-group">
                                <a href="https://www.joomla.org/download.html" class="btn btn-large btn-warning">Download</a>
                            </div>
                            <div class="btn-group">
                                <a href="https://demo.joomla.org" class="btn btn-large btn-primary">Demo</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="subnav-wrapper">
            <div class="subnav">
                <div class="container">
                    <ul class="nav menu nav-pills">
                        <li>
                            <a href="https://help.joomla.org">Home</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="body">
            <div class="container">
                <div class="row-fluid">
                    <main class="span12">
                        <?php echo $this->section('content'); ?>
                    </main>
                </div>
            </div>
        </div>

        <div class="footer center">
            <div class="container">
                <hr />
                <div class="social">
                    <ul class="soc">
                        <li><a href="https://twitter.com/joomla" target="_blank" class="soc-twitter2" title="Follow Us on Twitter"><span class="element-invisible">Follow Us on Twitter</span></a></li>
                        <li><a href="https://www.facebook.com/joomla" target="_blank" class="soc-facebook" title="Joomla! on Facebook"><span class="element-invisible">Joomla! on Facebook</span></a></li>
                        <li><a href="https://plus.google.com/+joomla/posts" target="_blank" class="soc-google" title="Joomla! on Google+"><span class="element-invisible">Joomla! on Google+</span></a></li>
                        <li><a href="https://www.youtube.com/user/joomla" target="_blank" class="soc-youtube3" title="Joomla's YouTube Channel"><span class="element-invisible">Joomla's YouTube Channel</span></a></li>
                        <li><a href="https://www.linkedin.com/company/joomla" target="_blank" class="soc-linkedin" title="Joomla! on Linkedin"><span class="element-invisible">Joomla! on Linkedin</span></a></li>
                        <li><a href="https://www.pinterest.com/joomla" target="_blank" class="soc-pinterest" title="Joomla's Pinterest Board"><span class="element-invisible">Joomla's Pinterest Board</span></a></li>
                        <li><a href="https://github.com/joomla" target="_blank" class="soc-github3 soc-icon-last" title="Joomla's GitHub"><span class="element-invisible">Joomla's GitHub</span></a></li>
                    </ul>
                </div>

                <div class="footer-menu">
                    <ul class="nav-inline">
                        <li><a href="https://www.joomla.org"><span>Home</span></a></li>
                        <li><a href="https://www.joomla.org/about-joomla.html"><span>About</span></a></li>
                        <li><a href="https://community.joomla.org"><span>Community</span></a></li>
                        <li><a href="http://forum.joomla.org"><span>Forum</span></a></li>
                        <li><a href="http://extensions.joomla.org"><span>Extensions</span></a></li>
                        <li><a href="http://resources.joomla.org"><span>Resources</span></a></li>
                        <li><a href="https://docs.joomla.org"><span>Docs</span></a></li>
                        <li><a href="https://developer.joomla.org"><span>Developer</span></a></li>
                        <li><a href="https://shop.joomla.org"><span>Shop</span></a></li>
                    </ul>

                    <ul class="nav-inline">
                        <li><a href="https://www.joomla.org/accessibility-statement.html">Accessibility Statement</a></li>
                        <li><a href="https://www.joomla.org/privacy-policy.html">Privacy Policy</a></li>
                        <li><a href="https://github.com/joomla/joomla-websites/issues/new?title=[jhelp]%20&amp;body=Please%20describe%20the%20problem%20or%20your%20issue">Report an Issue</a></li>
                    </ul>

                    <p class="copyright">&copy; 2005 - <?php echo date('Y');?> <a href="http://opensourcematters.org">Open Source Matters, Inc.</a> All rights reserved.</p>

                    <div class="hosting">
                        <div class="hosting-image"><a href="https://www.rochen.com/joomla-hosting" target="_blank"><img class="rochen" src="https://cdn.joomla.org/rochen/rochen_footer_logo_white.png" alt="Rochen" /></a></div>
                        <div class="hosting-text"><a href="https://www.rochen.com/joomla-hosting" target="_blank">Joomla! Hosting by Rochen</a></div>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                var navTop;
                var isFixed = false;
            
                $('.hasTooltip').tooltip();
                processScrollInit();
                processScroll();
            
                if (typeof blockAdBlock === 'undefined') {
                    adBlockDetected();
                } else {
                    blockAdBlock.onDetected(adBlockDetected);
                    blockAdBlock.on(true, adBlockDetected);
                }
            
                function adBlockDetected() {
                    $('#adblock-msg').removeClass('hide');
                }
            
                function processScrollInit() {
                    if ($('.subnav-wrapper').length) {
                        navTop = $('.subnav-wrapper').length && $('.subnav-wrapper').offset().top - 30;
            
                        // Fix the container top
                        $('.body .container-main').css('top', $('.subnav-wrapper').height() + $('#mega-menu').height());
            
                        // Only apply the scrollspy when the toolbar is not collapsed
                        if (document.body.clientWidth > 480) {
                            $('.subnav-wrapper').height($('.subnav').outerHeight());
                            $('.subnav').affix({
                                offset: {top: $('.subnav').offset().top - $('#mega-menu').height()}
                            });
                        }
                    }
                }
            
                function processScroll() {
                    if ($('.subnav-wrapper').length) {
                        var scrollTop = $(window).scrollTop();
                        if (scrollTop >= navTop && !isFixed) {
                            isFixed = true;
                            $('.subnav-wrapper').addClass('subhead-fixed');
            
                            // Fix the container top
                            $('.body .container-main').css('top', $('.subnav-wrapper').height() + $('#mega-menu').height());
                        } else if (scrollTop <= navTop && isFixed) {
                            isFixed = false;
                            $('.subnav-wrapper').removeClass('subhead-fixed');
                        }
                    }
                }
            });
       	</script>
    </body>
</html>
