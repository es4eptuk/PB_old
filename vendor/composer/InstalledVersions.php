<?php

namespace Composer;

use Composer\Semver\VersionParser;






class InstalledVersions
{
private static $installed = array (
  'root' => 
  array (
    'pretty_version' => 'dev-test',
    'version' => 'dev-test',
    'aliases' => 
    array (
    ),
    'reference' => '2c028becaada12be9c873cabf10b350acae49408',
    'name' => '__root__',
  ),
  'versions' => 
  array (
    '__root__' => 
    array (
      'pretty_version' => 'dev-test',
      'version' => 'dev-test',
      'aliases' => 
      array (
      ),
      'reference' => '2c028becaada12be9c873cabf10b350acae49408',
    ),
    'doctrine/inflector' => 
    array (
      'pretty_version' => 'v1.1.0',
      'version' => '1.1.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '90b2128806bfde671b6952ab8bea493942c1fdae',
    ),
    'guzzlehttp/guzzle' => 
    array (
      'pretty_version' => '6.3.3',
      'version' => '6.3.3.0',
      'aliases' => 
      array (
      ),
      'reference' => '407b0cb880ace85c9b63c5f9551db498cb2d50ba',
    ),
    'guzzlehttp/promises' => 
    array (
      'pretty_version' => 'v1.3.1',
      'version' => '1.3.1.0',
      'aliases' => 
      array (
      ),
      'reference' => 'a59da6cf61d80060647ff4d3eb2c03a2bc694646',
    ),
    'guzzlehttp/psr7' => 
    array (
      'pretty_version' => '1.4.2',
      'version' => '1.4.2.0',
      'aliases' => 
      array (
      ),
      'reference' => 'f5b8a8512e2b58b0071a7280e39f14f72e05d87c',
    ),
    'illuminate/contracts' => 
    array (
      'pretty_version' => 'v5.4.36',
      'version' => '5.4.36.0',
      'aliases' => 
      array (
      ),
      'reference' => '67f642e018f3e95fb0b2ebffc206c3200391b1ab',
    ),
    'illuminate/support' => 
    array (
      'pretty_version' => 'v5.4.36',
      'version' => '5.4.36.0',
      'aliases' => 
      array (
      ),
      'reference' => 'feab1d1495fd6d38970bd6c83586ba2ace8f299a',
    ),
    'irazasyed/telegram-bot-sdk' => 
    array (
      'pretty_version' => 'v2.2.0',
      'version' => '2.2.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '2824278370b231dfc9172104db8acc23fc5ade94',
    ),
    'kilylabs/odata-1c' => 
    array (
      'pretty_version' => '0.2',
      'version' => '0.2.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '3f3fd7ef93f77536f31a1769cabbae9e8057989d',
    ),
    'longman/telegram-bot' => 
    array (
      'pretty_version' => '0.54.0',
      'version' => '0.54.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '64bcd925e65dbdca94f21638b69b24dfea99d046',
    ),
    'mesilov/bitrix24-php-sdk' => 
    array (
      'pretty_version' => '0.6.1',
      'version' => '0.6.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '4d975336fd0af8937a9c09b01b617925748bf47b',
    ),
    'monolog/monolog' => 
    array (
      'pretty_version' => '1.23.0',
      'version' => '1.23.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'fd8c787753b3a2ad11bc60c063cff1358a32a3b4',
    ),
    'paragonie/random_compat' => 
    array (
      'pretty_version' => 'v2.0.18',
      'version' => '2.0.18.0',
      'aliases' => 
      array (
      ),
      'reference' => '0a58ef6e3146256cc3dc7cc393927bcc7d1b72db',
    ),
    'psr/http-message' => 
    array (
      'pretty_version' => '1.0.1',
      'version' => '1.0.1.0',
      'aliases' => 
      array (
      ),
      'reference' => 'f6561bf28d520154e4b0ec72be95418abe6d9363',
    ),
    'psr/http-message-implementation' => 
    array (
      'provided' => 
      array (
        0 => '1.0',
      ),
    ),
    'psr/log' => 
    array (
      'pretty_version' => '1.0.2',
      'version' => '1.0.2.0',
      'aliases' => 
      array (
      ),
      'reference' => '4ebe3a8bf773a19edfe0a84b6585ba3d401b724d',
    ),
    'psr/log-implementation' => 
    array (
      'provided' => 
      array (
        0 => '1.0.0',
      ),
    ),
    'telegram-bot/api' => 
    array (
      'pretty_version' => 'v2.3.13',
      'version' => '2.3.13.0',
      'aliases' => 
      array (
      ),
      'reference' => '8d34f3ccd3230a9fd7c7fd9ba8ec0110a059f781',
    ),
    'tightenco/collect' => 
    array (
      'replaced' => 
      array (
        0 => 'v5.4.36',
      ),
    ),
  ),
);







public static function getInstalledPackages()
{
return array_keys(self::$installed['versions']);
}









public static function isInstalled($packageName)
{
return isset(self::$installed['versions'][$packageName]);
}














public static function satisfies(VersionParser $parser, $packageName, $constraint)
{
$constraint = $parser->parseConstraints($constraint);
$provided = $parser->parseConstraints(self::getVersionRanges($packageName));

return $provided->matches($constraint);
}










public static function getVersionRanges($packageName)
{
if (!isset(self::$installed['versions'][$packageName])) {
throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}

$ranges = array();
if (isset(self::$installed['versions'][$packageName]['pretty_version'])) {
$ranges[] = self::$installed['versions'][$packageName]['pretty_version'];
}
if (array_key_exists('aliases', self::$installed['versions'][$packageName])) {
$ranges = array_merge($ranges, self::$installed['versions'][$packageName]['aliases']);
}
if (array_key_exists('replaced', self::$installed['versions'][$packageName])) {
$ranges = array_merge($ranges, self::$installed['versions'][$packageName]['replaced']);
}
if (array_key_exists('provided', self::$installed['versions'][$packageName])) {
$ranges = array_merge($ranges, self::$installed['versions'][$packageName]['provided']);
}

return implode(' || ', $ranges);
}





public static function getVersion($packageName)
{
if (!isset(self::$installed['versions'][$packageName])) {
throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}

if (!isset(self::$installed['versions'][$packageName]['version'])) {
return null;
}

return self::$installed['versions'][$packageName]['version'];
}





public static function getPrettyVersion($packageName)
{
if (!isset(self::$installed['versions'][$packageName])) {
throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}

if (!isset(self::$installed['versions'][$packageName]['pretty_version'])) {
return null;
}

return self::$installed['versions'][$packageName]['pretty_version'];
}





public static function getReference($packageName)
{
if (!isset(self::$installed['versions'][$packageName])) {
throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}

if (!isset(self::$installed['versions'][$packageName]['reference'])) {
return null;
}

return self::$installed['versions'][$packageName]['reference'];
}





public static function getRootPackage()
{
return self::$installed['root'];
}







public static function getRawData()
{
return self::$installed;
}



















public static function reload($data)
{
self::$installed = $data;
}
}
