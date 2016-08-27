<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Joomla! Help Screens</title>
        <link rel="stylesheet" href="<?php echo $this->media('css/reset.css'); ?>" />
        <link rel="stylesheet" href="<?php echo $this->media('css/help.css'); ?>" />
	</head>
	<body>
        <?php $this->insert('partials/gtm') ?>
		<a name="Top" id="Top"></a>
        <h1><?php if ($exception->getCode()) : echo $exception->getCode() . ' - '; endif; ?><?php echo $this->e(get_class($exception)); ?></h1>
        <p><?php echo $this->e($exception->getMessage()); ?></p>
        <div id="footer-wrapper">
        	<div id="license">License: <a href="https://docs.joomla.org/JEDL">Joomla! Electronic Documentation License</a></div>
        	<div id="copyright">Copyright &copy; <?php echo date('Y'); ?> <a href="http://opensourcematters.org">Open Source Matters, Inc.</a> All rights reserved.</div>
	        <div id="report-an-issue"><a href="https://github.com/joomla/joomla-websites/issues/new?title=[jhelp]%20&amp;body=Please%20describe%20the%20problem%20or%20your%20issue">Report an Issue</a></div>
        </div>
		<hr/>
		<a href="<?php echo $this->current_url(); ?>#Top">Top</a>
	</body>
</html>
