<?php
    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
class Bitrix
{
    const CONFIG = [
        'domain' => 'https://team.promo-bot.ru/rest/1097/xh3yrqn4yj4pq0ir',
    ];

    public $log;

    function __construct()
    {

    }

    function init()
    {

    }

    function __destruct()
    {

    }

    public function send($method, $params=[], $domain = self::CONFIG['domain'])
    {
        $url = $domain.'/'.$method;

        /*curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 0);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result,true);*/
        //$params = json_encode($params);

        //echo $url;die;
        $sPostFields = http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTREDIR, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, 'My script PHP');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $sPostFields);
        //define('C_REST_CURRENT_ENCODING','windows-1251');
        //define('C_REST_IGNORE_SSL',true);//turn off validate ssl by curl
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