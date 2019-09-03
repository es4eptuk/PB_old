<?php


/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Longman\TelegramBot\Commands\SystemCommands;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;
/**
 * Start command
 *
 * Gets executed when a user first starts using the bot.
 */
class GetTicketsCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'get_tickets';
    /**
     * @var string
     */
    protected $description = 'Tickets with Robot ID';
    /**
     * @var string
     */
    protected $usage = '/get_tickets <text>';
    /**
     * @var string
     */
    protected $version = '1.1.0';
    /**
     * @var bool
     */
    protected $private_only = true;
    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
       
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $text    = trim($message->getText(true));
        if ($text === '') {
            $text = 'Command usage: ' . $this->getUsage();
        }
        $data = [
            'chat_id' => $chat_id,
            'text'    => $text."!!!!",
        ];
        
        $fd = fopen("hello.txt", 'w') or die("не удалось создать файл");
        $str = $chat_id;
        fwrite($fd, $str);
        fclose($fd);
        
        return Request::sendMessage($data);
        
    }
}