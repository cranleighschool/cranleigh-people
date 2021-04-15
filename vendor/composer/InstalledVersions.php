<?php

namespace Composer;

use Composer\Autoload\ClassLoader;
use Composer\Semver\VersionParser;

class InstalledVersions
{
    private static $installed = [
        'root' => [
            'pretty_version' => 'dev-master',
            'version' => 'dev-master',
            'aliases' => [
            ],
            'reference' => '4d5feadf1c5361f888933d5be6dabb47646113c6',
            'name' => 'fredbradley/cranleigh-people',
            'dev' => true,
        ],
        'versions' => [
            'composer/xdebug-handler' => [
                'pretty_version' => '1.4.6',
                'version' => '1.4.6.0',
                'aliases' => [
                ],
                'reference' => 'f27e06cd9675801df441b3656569b328e04aa37c',
                'dev-requirement' => true,
            ],
            'fredbradley/cranleigh-people' => [
                'pretty_version' => 'dev-master',
                'version' => 'dev-master',
                'aliases' => [
                ],
                'reference' => '4d5feadf1c5361f888933d5be6dabb47646113c6',
                'dev-requirement' => false,
            ],
            'fredbradley/cranleigh-slacker' => [
                'pretty_version' => '1.0.1',
                'version' => '1.0.1.0',
                'aliases' => [
                ],
                'reference' => '3f01ad0c14dd7a1a423f25ce6abb90e61dc4731a',
                'dev-requirement' => false,
            ],
            'giacocorsiglia/wordpress-stubs' => [
                'dev-requirement' => true,
                'replaced' => [
                    0 => '*',
                ],
            ],
            'nesbot/carbon' => [
                'pretty_version' => '2.46.0',
                'version' => '2.46.0.0',
                'aliases' => [
                ],
                'reference' => '2fd2c4a77d58a4e95234c8a61c5df1f157a91bf4',
                'dev-requirement' => false,
            ],
            'pdepend/pdepend' => [
                'pretty_version' => '2.9.0',
                'version' => '2.9.0.0',
                'aliases' => [
                ],
                'reference' => 'b6452ce4b570f540be3a4f46276dd8d8f4fa5ead',
                'dev-requirement' => true,
            ],
            'php-stubs/wordpress-stubs' => [
                'pretty_version' => 'v5.7.0',
                'version' => '5.7.0.0',
                'aliases' => [
                ],
                'reference' => '69baf30e7c92f149526da950a68222af05f7bc67',
                'dev-requirement' => true,
            ],
            'phpmd/phpmd' => [
                'pretty_version' => '2.9.1',
                'version' => '2.9.1.0',
                'aliases' => [
                ],
                'reference' => 'ce10831d4ddc2686c1348a98069771dd314534a8',
                'dev-requirement' => true,
            ],
            'phpstan/phpstan' => [
                'pretty_version' => '0.12.83',
                'version' => '0.12.83.0',
                'aliases' => [
                ],
                'reference' => '4a967cec6efb46b500dd6d768657336a3ffe699f',
                'dev-requirement' => true,
            ],
            'psr/container' => [
                'pretty_version' => '1.1.1',
                'version' => '1.1.1.0',
                'aliases' => [
                ],
                'reference' => '8622567409010282b7aeebe4bb841fe98b58dcaf',
                'dev-requirement' => true,
            ],
            'psr/container-implementation' => [
                'dev-requirement' => true,
                'provided' => [
                    0 => '1.0',
                ],
            ],
            'psr/log' => [
                'pretty_version' => '1.1.3',
                'version' => '1.1.3.0',
                'aliases' => [
                ],
                'reference' => '0f73288fd15629204f9d42b7055f72dacbe811fc',
                'dev-requirement' => true,
            ],
            'rilwis/meta-box' => [
                'pretty_version' => '4.18.4',
                'version' => '4.18.4.0',
                'aliases' => [
                ],
                'reference' => 'd299ea6d2dcfb55b05201fd1ac10e4cc1a6e839a',
                'dev-requirement' => false,
            ],
            'squizlabs/php_codesniffer' => [
                'pretty_version' => '3.6.0',
                'version' => '3.6.0.0',
                'aliases' => [
                ],
                'reference' => 'ffced0d2c8fa8e6cdc4d695a743271fab6c38625',
                'dev-requirement' => true,
            ],
            'symfony/config' => [
                'pretty_version' => 'v5.2.4',
                'version' => '5.2.4.0',
                'aliases' => [
                ],
                'reference' => '212d54675bf203ff8aef7d8cee8eecfb72f4a263',
                'dev-requirement' => true,
            ],
            'symfony/dependency-injection' => [
                'pretty_version' => 'v5.2.6',
                'version' => '5.2.6.0',
                'aliases' => [
                ],
                'reference' => '1e66194bed2a69fa395d26bf1067e5e34483afac',
                'dev-requirement' => true,
            ],
            'symfony/deprecation-contracts' => [
                'pretty_version' => 'v2.2.0',
                'version' => '2.2.0.0',
                'aliases' => [
                ],
                'reference' => '5fa56b4074d1ae755beb55617ddafe6f5d78f665',
                'dev-requirement' => true,
            ],
            'symfony/filesystem' => [
                'pretty_version' => 'v5.2.6',
                'version' => '5.2.6.0',
                'aliases' => [
                ],
                'reference' => '8c86a82f51658188119e62cff0a050a12d09836f',
                'dev-requirement' => true,
            ],
            'symfony/polyfill-ctype' => [
                'pretty_version' => 'v1.22.1',
                'version' => '1.22.1.0',
                'aliases' => [
                ],
                'reference' => 'c6c942b1ac76c82448322025e084cadc56048b4e',
                'dev-requirement' => true,
            ],
            'symfony/polyfill-mbstring' => [
                'pretty_version' => 'v1.22.1',
                'version' => '1.22.1.0',
                'aliases' => [
                ],
                'reference' => '5232de97ee3b75b0360528dae24e73db49566ab1',
                'dev-requirement' => false,
            ],
            'symfony/polyfill-php73' => [
                'pretty_version' => 'v1.22.1',
                'version' => '1.22.1.0',
                'aliases' => [
                ],
                'reference' => 'a678b42e92f86eca04b7fa4c0f6f19d097fb69e2',
                'dev-requirement' => true,
            ],
            'symfony/polyfill-php80' => [
                'pretty_version' => 'v1.22.1',
                'version' => '1.22.1.0',
                'aliases' => [
                ],
                'reference' => 'dc3063ba22c2a1fd2f45ed856374d79114998f91',
                'dev-requirement' => false,
            ],
            'symfony/service-contracts' => [
                'pretty_version' => 'v2.2.0',
                'version' => '2.2.0.0',
                'aliases' => [
                ],
                'reference' => 'd15da7ba4957ffb8f1747218be9e1a121fd298a1',
                'dev-requirement' => true,
            ],
            'symfony/service-implementation' => [
                'dev-requirement' => true,
                'provided' => [
                    0 => '1.0|2.0',
                ],
            ],
            'symfony/translation' => [
                'pretty_version' => 'v5.2.6',
                'version' => '5.2.6.0',
                'aliases' => [
                ],
                'reference' => '2cc7f45d96db9adfcf89adf4401d9dfed509f4e1',
                'dev-requirement' => false,
            ],
            'symfony/translation-contracts' => [
                'pretty_version' => 'v2.3.0',
                'version' => '2.3.0.0',
                'aliases' => [
                ],
                'reference' => 'e2eaa60b558f26a4b0354e1bbb25636efaaad105',
                'dev-requirement' => false,
            ],
            'symfony/translation-implementation' => [
                'dev-requirement' => false,
                'provided' => [
                    0 => '2.3',
                ],
            ],
            'szepeviktor/phpstan-wordpress' => [
                'pretty_version' => 'v0.7.5',
                'version' => '0.7.5.0',
                'aliases' => [
                ],
                'reference' => '90cf3c6a225a633889b1b3a556816911f42de4f7',
                'dev-requirement' => true,
            ],
            'wp-coding-standards/wpcs' => [
                'pretty_version' => '2.3.0',
                'version' => '2.3.0.0',
                'aliases' => [
                ],
                'reference' => '7da1894633f168fe244afc6de00d141f27517b62',
                'dev-requirement' => true,
            ],
            'yahnis-elsts/plugin-update-checker' => [
                'pretty_version' => 'v4.11',
                'version' => '4.11.0.0',
                'aliases' => [
                ],
                'reference' => '3155f2d3f1ca5e7ed3f25b256f020e370515af43',
                'dev-requirement' => false,
            ],
        ],
    ];
    private static $canGetVendors;
    private static $installedByVendor = [];

    public static function getInstalledPackages()
    {
        $packages = [];
        foreach (self::getInstalled() as $installed) {
            $packages[] = array_keys($installed['versions']);
        }

        if (1 === \count($packages)) {
            return $packages[0];
        }

        return array_keys(array_flip(\call_user_func_array('array_merge', $packages)));
    }

    public static function isInstalled($packageName, $includeDevRequirements = true)
    {
        foreach (self::getInstalled() as $installed) {
            if (isset($installed['versions'][$packageName])) {
                return $includeDevRequirements || empty($installed['versions'][$packageName]['dev-requirement']);
            }
        }

        return false;
    }

    public static function satisfies(VersionParser $parser, $packageName, $constraint)
    {
        $constraint = $parser->parseConstraints($constraint);
        $provided = $parser->parseConstraints(self::getVersionRanges($packageName));

        return $provided->matches($constraint);
    }

    public static function getVersionRanges($packageName)
    {
        foreach (self::getInstalled() as $installed) {
            if (! isset($installed['versions'][$packageName])) {
                continue;
            }

            $ranges = [];
            if (isset($installed['versions'][$packageName]['pretty_version'])) {
                $ranges[] = $installed['versions'][$packageName]['pretty_version'];
            }
            if (array_key_exists('aliases', $installed['versions'][$packageName])) {
                $ranges = array_merge($ranges, $installed['versions'][$packageName]['aliases']);
            }
            if (array_key_exists('replaced', $installed['versions'][$packageName])) {
                $ranges = array_merge($ranges, $installed['versions'][$packageName]['replaced']);
            }
            if (array_key_exists('provided', $installed['versions'][$packageName])) {
                $ranges = array_merge($ranges, $installed['versions'][$packageName]['provided']);
            }

            return implode(' || ', $ranges);
        }

        throw new \OutOfBoundsException('Package "'.$packageName.'" is not installed');
    }

    public static function getVersion($packageName)
    {
        foreach (self::getInstalled() as $installed) {
            if (! isset($installed['versions'][$packageName])) {
                continue;
            }

            if (! isset($installed['versions'][$packageName]['version'])) {
                return null;
            }

            return $installed['versions'][$packageName]['version'];
        }

        throw new \OutOfBoundsException('Package "'.$packageName.'" is not installed');
    }

    public static function getPrettyVersion($packageName)
    {
        foreach (self::getInstalled() as $installed) {
            if (! isset($installed['versions'][$packageName])) {
                continue;
            }

            if (! isset($installed['versions'][$packageName]['pretty_version'])) {
                return null;
            }

            return $installed['versions'][$packageName]['pretty_version'];
        }

        throw new \OutOfBoundsException('Package "'.$packageName.'" is not installed');
    }

    public static function getReference($packageName)
    {
        foreach (self::getInstalled() as $installed) {
            if (! isset($installed['versions'][$packageName])) {
                continue;
            }

            if (! isset($installed['versions'][$packageName]['reference'])) {
                return null;
            }

            return $installed['versions'][$packageName]['reference'];
        }

        throw new \OutOfBoundsException('Package "'.$packageName.'" is not installed');
    }

    public static function getRootPackage()
    {
        $installed = self::getInstalled();

        return $installed[0]['root'];
    }

    public static function getRawData()
    {
        return self::$installed;
    }

    public static function reload($data)
    {
        self::$installed = $data;
        self::$installedByVendor = [];
    }

    private static function getInstalled()
    {
        if (null === self::$canGetVendors) {
            self::$canGetVendors = method_exists('Composer\Autoload\ClassLoader', 'getRegisteredLoaders');
        }

        $installed = [];

        if (self::$canGetVendors) {
            foreach (ClassLoader::getRegisteredLoaders() as $vendorDir => $loader) {
                if (isset(self::$installedByVendor[$vendorDir])) {
                    $installed[] = self::$installedByVendor[$vendorDir];
                } elseif (is_file($vendorDir.'/composer/installed.php')) {
                    $installed[] = self::$installedByVendor[$vendorDir] = require $vendorDir.'/composer/installed.php';
                }
            }
        }

        $installed[] = self::$installed;

        return $installed;
    }
}
