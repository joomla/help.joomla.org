<?php /** @var $this \League\Plates\Template\Template */ ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <?php $this->insert('partials/head') ?>
    </head>
    <body>
        <?php $this->insert('partials/gtm') ?>
   		<div class="container">
            <div class="well text-center">
                <img src="<?php echo $this->media('images/logo.png'); ?>" alt="Joomla! Logo">
                <h2>Joomla! End of Support Notice<br />All security patches have ceased.</h2>
                <p class="lead">Joomla 1.0 End of Support - July 22, 2009<br />Joomla 1.5 End of Support - December 31, 2012</p>
                <p>If you are still using Joomla! 1.0 or 1.5, please upgrade your Joomla! version ASAP. Your website may be at risk and your installed extensions may be a security risk.</p>
                <p><a class="btn btn-primary" href="https://docs.joomla.org/What_version_of_Joomla!_should_you_use%3F" target="_blank">What version of Joomla! should you use?</a></p>
                <p><a class="btn btn-success" href="https://docs.joomla.org/Help15:Help_screens" target="_blank">Legacy Joomla 1.5 Help Screens</a></p>
                <p>Joomla 1.0 Help Screens are no longer available</p>
            </div>
   		</div>
   	</body>
</html>
