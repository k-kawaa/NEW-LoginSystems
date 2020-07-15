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
  private $config3;

    public function onEnable()
    {

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->config = new Config($this->getDataFolder() . "name.yml", Config::YAML);
        $this->config2 = new Config($this->getDataFolder() . "ip.yml", Config::YAML);
        $this->config3 = new Config($this->getDataFolder() . "uuid.yml", Config::YAML);

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
    	if ($this->config->exists($name)) 
    	{
    		$myip = $this->config2->get($name);
    		$myuuid = $this->config3->get($name);

    		if ($myip === $ip&&$myuuid === $uuid) 
    		{
                
            $player->sendMessage("§a[NEW!LoginSystem]認証に成功しました。おかえりなさい!");
                
    		}else{
    			$player->sendMessage("§4[NEWLoginSystem]認証に失敗しました。IPアドレスや端末変更された可能性がございます。\n/login password でもう一度ログインをお願いいたします。");
      		    $player->setImmobile();  			
    		}
    	
    	}

    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool
    {

        if(!$sender instanceof Player)
        {
          $sender->sendMessage("§cゲーム内で実行してください。コンソールからは実行ができません。");
          return true;
        }

        $name = $sender->getName();
        $ip = $sender->getAddress();
    	$uuid = $sender->getUniqueId();
        switch($label){

          case 'register':
         if (!$this->config->exists($name)) 
         {
         	if (!isset($args[0])) 
         	{
         		$sender->sendMessage("アカウント登録方法：/register [任意のパスワード]");
         	}else{
            $password = ($args[0]);
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $this->config->set($name,$hash);
            $this->config2->set($name,$ip);
            $this->config3->set($name,$uuid);
            $sender->setImmobile(false);
            $sender->sendMessage("§a[LoginSystem]password・その他端末情報を保存し、正常にアカウント登録が完了しました。");
            $this->config->save();
            $this->config2->save();
            $this->config3->save();
            return true;
            }
          }
          case "login":
          if ($this->config->exists($name)) 
          {
          	if (!isset($args[0])) 
          	{
          		$sender->sendMessage("§4ログイン方法:/login password ");
          	}
            $hash = $this->config->get($name);
            if (password_verify($args[0], $hash)) 
            {
            	$sender->sendMessage("§a[LoginSystem]password認証に成功しました。情報の変更を行います。");
            	$this->config2->remove($name);
            	$this->config3->remove($name);
            	$this->config2->set($name,$ip);
            	$this->config3->set($name,$uuid);
                $this->config2->save();
                $this->config3->save();
      		    $sender->setImmobile(false);  

            }else
            {
            	$sender->sendMessage("§4[エラー]ログインができませんでした。正しく入力できているかご確認をお願い致します。");
            }
          
         }
          break;        
        }
        return true;
    }

    public function onDisable()
    {
      $this->config->save();
      $this->config2->save();
      $this->config3->save();

    }

}
