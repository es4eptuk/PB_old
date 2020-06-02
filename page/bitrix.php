<?php
    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
class Bitrix
{
    const CONFIG = [
        'domain' => 'https://team.promo-bot.ru/rest/192/z5y7rvalq9ff5lqg/',
    ];

    public $test;
    public $log;

    function __construct()
    {

    }

    function init()
    {
        $this->test = 'Работает!';

    }

    function __destruct()
    {

    }

    public function send($method, $params=[])
    {
        $url = self::CONFIG['domain'].''.$method.'/';

        /*curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 0);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result,true);*/

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result,true);
    }

    public function todo() {
        // create a log channel
        $log = new Logger('bitrix24');
        $log->pushHandler(new StreamHandler('path/to/your.log', Logger::DEBUG));

        // init lib
        $obB24App = new \Bitrix24\Bitrix24(false, $log);
        $obB24App->setApplicationScope(self::CONFIG['B24_APPLICATION_SCOPE']);
        $obB24App->setApplicationId(self::CONFIG['B24_APPLICATION_ID']);
        $obB24App->setApplicationSecret(self::CONFIG['B24_APPLICATION_SECRET']);

        // set user-specific settings
        $obB24App->setDomain(self::CONFIG['DOMAIN']);
        $obB24App->setMemberId(self::CONFIG['MEMBER_ID']);
        $obB24App->setAccessToken(self::CONFIG['AUTH_ID']);
        $obB24App->setRefreshToken(self::CONFIG['REFRESH_ID']);

        // get information about current user from bitrix24
        $obB24User = new \Bitrix24\User\User($obB24App);
        $arCurrentB24User = $obB24User->current();

        return $arCurrentB24User;
    }
}