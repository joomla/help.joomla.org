<?php /** @var $this \League\Plates\Template\Template */ ?>
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

                        <?php echo $this->cdn_menu(); ?>
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
                                <a href="https://downloads.joomla.org" class="btn btn-large btn-warning">Download</a>
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

                <?php echo $this->cdn_footer(); ?>
            </div>
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                var navTop;
                var isFixed = false;

                $('.hasTooltip').tooltip();
                processScrollInit();
                processScroll();

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
