<?php

namespace apartkktrain\login;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\player\PlayerChatEvent;

class Main extends PluginBase implements Listener
{

  private $config;
  private $config2;

    public function onEnable()
    {

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->config = new Config($this->getDataFolder() . "name.yml", Config::YAML);
        $this->config2 = new Config($this->getDataFolder() . "ip.yml", Config::YAML);

    }


    public function onJoin(PlayerJoinEvent $event)
    {
    	$player = $event->getPlayer();
    	$name = $event->getPlayer()->getName();

    	$ip = $player->getAddress();
    	$uuid = $player->getUniqueId();


    	if (!$this->config->exists($name)) {
    		$player->setImmobile();
    		$player->sendMessage("§a[NEW!LoginSystem]サーバーへようこそ。\nこのサーバーではログインシステムを導入しております。\n/register [password] でアカウントを登録しましょう。");
    	}
    	if ($this->config->exists($name)) {
    		$myip = $this->config2->get($name);

    		if ($myip === $ip) {
                $player->sendMessage("§a[NEWLoginSystem]認証が成功しましたおかえりなさい。");
    		}else{
    			$player->sendMessage("§4[NEWLoginSystem]認証に失敗しました。IPアドレスが変更された可能性がございます。\n/login password でもう一度ログインをお願いいたします。");
      		    $player->setImmobile();  			
    		}
    	
    	}

    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool
    {

        if(!$sender instanceof Player){
          $sender->sendMessage("§cゲーム内で実行してください。コンソールからは実行ができません。");
          return true;
        }

        $name = $sender->getName();
        $ip = $sender->getAddress();

        switch($label){

          case 'register':
         if (!$this->config->exists($name)) {
         	if (!isset($args[0])) {
         		$sender->sendMessage("アカウント登録方法：/register [任意のパスワード]");
         	}else{
         		
            $this->config->set($name,$args[0]);
            $this->config2->set($name,$ip);
            $sender->setImmobile(false);
            $sender->sendMessage("§a[LoginSystem]password・その他端末情報を保存し、正常にアカウント登録が完了しました。");
            $this->config->save();
            $this->config2->save();
            return true;
            }

          case 'login':
          
         }
          break;        
        }
        return true;
    }

    public function onDisable()
    {
      $this->config->save();
      $this->config2->save();


    }

}
