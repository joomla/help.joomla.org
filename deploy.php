<?php
/**
 * Build a ZIP package to deploy to the production server
 *
 * This script strips all local environment data and non-production resources from the filesystem before running the
 * `zip` command.  It should be run after `composer install --no-dev -o -a`. This script WILL remove the `conf/config.json`
 * file when it is run.  On the provisioning server the git repo is copied to a staging space before files start to be
 * removed to prevent accidental removal of the in-use parameters file.
 *
 * This script must be run on a Linux platform.  Sorry Windoze.
 */

$baseDir    = __DIR__;
$stagingDir = $baseDir . '/staging';

chdir($baseDir);

echo "Provisioning staging\n";

if (is_dir($stagingDir)) {
    recursive_remove_directory($stagingDir);
}

mkdir($stagingDir, 0755);

// Copy everything into staging
system('cp -r cache/ staging/cache/');
system('cp -r conf/ staging/conf/');
system('cp -r log/ staging/log/');
system('cp -r src/ staging/src/');
system('cp -r templates/ staging/templates/');
system('cp -r vendor/ staging/vendor/');
system('cp -r www/ staging/www/');

chdir($stagingDir);

echo "Removing extra files\n";

// Removes Symfony cache, logs, temp files, the local configuration file and .git sources
system('rm -rf cache/* log/*.log conf/config.json .git*');

// Removes Mac .DS_Store files, .git sources, PHP-CS Fixer configuration files, Scrutinizer configuration files, Travis-CI configuration files, Changelogs, GitHub Contributing Guidelines, Composer manifests, README files, and PHPUnit configurations
system('find . -name .DS_Store | xargs rm -rf -');
system('find . -name .git* | xargs rm -rf -');
system('find . -name .php_cs | xargs rm -rf -');
system('find . -name .scrutinizer.yml | xargs rm -rf -');
system('find . -name .travis.yml | xargs rm -rf -');
system('find . -name CHANGELOG*.md | xargs rm -rf -');
system('find . -name composer.json | xargs rm -rf -');
system('find . -name composer.lock | xargs rm -rf -');
system('find . -name CONTRIBUTING.md | xargs rm -rf -');
system('find . -name Makefile | xargs rm -rf -');
system('find . -name phpunit.xml.dist | xargs rm -rf -');
system('find . -name README.md | xargs rm -rf -');
system('find . -name UPGRADE*.md | xargs rm -rf -');

echo "Cleaning vendors\n";

// joomla/*
system('rm -rf vendor/joomla/*/.travis');
system('rm -rf vendor/joomla/*/Tests');
system('rm -rf vendor/joomla/*/tests');

// league/plates
system('rm -rf vendor/league/plates/docs');
system('rm -rf vendor/league/plates/example');

// monolog/monolog
system('rm -rf vendor/monolog/monolog/doc');

// zendframework/zend-diactoros
system('rm -f vendor/zendframework/zend-diactoros/CONDUCT.md');
system('rm -f vendor/zendframework/zend-diactoros/mkdocs.yml');

// symfony/*
system('rm -rf vendor/symfony/*/Tests');

echo "Packaging the site\n";
system('zip -r ../site.zip . > /dev/null');

/**
 * Tries to recursively delete a directory.
 *
 * This code is based on the recursive_remove_directory function used by Akeeba Restore
 *
 * @param string $directory
 *
 * @return bool
 */
function recursive_remove_directory(string $directory): bool
{
    // if the path has a slash at the end we remove it here
    if (substr($directory, -1) == '/') {
        $directory = substr($directory, 0, -1);
    }

    // if the path is not valid or is not a directory ...
    if (!file_exists($directory)) {
        return true;
    } elseif (!is_dir($directory)) {
        return false;
        // ... if the path is not readable
    } elseif (!is_readable($directory)) {
        // ... we return false and exit the function
        return false;
        // ... else if the path is readable
    } else {
        // we open the directory
        $handle = opendir($directory);

        // and scan through the items inside
        while (false !== ($item = readdir($handle))) {
            // if the filepointer is not the current directory
            // or the parent directory
            if ($item != '.' && $item != '..') {
                // we build the new path to delete
                $path = $directory . '/' . $item;
                // if the new path is a directory
                if (is_dir($path)) {
                    // we call this function with the new path
                    recursive_remove_directory($path);
                    // if the new path is a file
                } else {
                    // we remove the file
                    @unlink($path);
                }
            }
        }

        // close the directory
        closedir($handle);

        // try to delete the now empty directory
        if (!@rmdir($directory)) {
            // return false if not possible
            return false;
        }

        // return success
        return true;
    }
}
