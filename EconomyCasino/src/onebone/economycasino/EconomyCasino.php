<?php

namespace onebone\economycasino;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;

use onebone\economyapi\EconomyAPI;

class EconomyCasino extends PluginBase implements Listener{
	private $casino;

	/**
	 * @var EconomyAPI
	 */
	private $api;

	/**
	 * @var Config
	 */
	private $config;

	public function onEnable(){
		@mkdir($this->getDataFolder());
		$this->api = EconomyAPI::getInstance();
		$this->config = new Config($this->getDataFolder()."config.yml", Config::YAML, array(
			"jackpot-winning" => 1000,
			"jackpot-money" => 5,
			"max-game" => 10
		));

		$this->casino = array();

		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onDisable(){
		$this->api = null;
		$this->casino = array();
	}

	public function onQuitEvent(PlayerQuitEvent $event){
		$player = $event->getPlayer();
		
		foreach($this->casino as $pl => $casino){
			if(isset($casino["players"][$pl])){
				unset($this->casino[$pl]["players"][$pl]);
				$players = $this->casino[$pl]["players"];

				$name = $player->getName();
				foreach($players as $p => $v){
					$this->getServer()->getPlayerExact($p)->sendMessage("[EconomyCasino] ".$name." left the casino game.");
				}
				break;
			}
		}
	}

	public function onCommand(CommandSender $sender, Command $command, $label, array $params){
		switch($command->getName()){
			case "casino":
				$sub = array_shift($params);
				switch($sub){
					case "start":
						if(!$sender instanceof Player){
							$sender->sendMessage("Please run this command in-game.");
							break;
						}
						if(!$sender->hasPermission("economycasino.command.casino.start")){
							return true;
						}
						if($this->config->get("max-game") <= count($this->casino)){
							$sender->sendMessage("There are too many games in progress. Please join the other games.");
							break;
						}
						if(isset($this->casino[$sender->getName()])){
							$sender->sendMessage("You already have joined casino.");
						}else{
							foreach($this->casino as $player => $casino){
								if(isset($casino["players"][$sender->getName()])){
									$sender->sendMessage("You already have joined casino.");
									return true;
								}
							}
						}
						$this->casino[$sender->getName()] = array(
							"players" => array(
								$sender->getName() => true
							)
						);
						$this->getServer()->broadcastMessage("[EconomyCasino] Casino of ".$sender->getName()." has just started.");
						break;
					case "stop":
						if(!$sender instanceof Player){
							$sender->sendMessage("Please run this command in-game.");
							break;
						}
						if(!$sender->hasPermission("economycasino.command.casino.stop")){
							return true;
						}
						if($sender instanceof Player and !$sender->isOp()){
							if(isset($this->casino[$sender->getName()])){
								foreach($this->casino[$sender->getName()]["players"] as $player => $v){
									$this->getServer()->getPlayerExact($player)->sendMessage("[EconomyCasino] You have left the casino due to stop.");
								}
								unset($this->casino[$sender->getName()]);
								$sender->sendMessage("You have stopped your casino.");
							}else{
								$sender->sendMessage("You don't have any casino game to quit.");
							}
						}else{
							$player = array_shift($params);
							if(trim($player) === ""){
								$sender->sendMessage("Usage: /casino stop <player>");
								break;
							}
							if(isset($this->casino[$player])){
								foreach($this->casino[$player]["players"] as $player => $v){
									$this->getServer()->getPlayerExact($player)->sendMessage("[EconomyCasino] You have left the casino game due to stop.");
								}
								$sender->sendMessage("[EconomyCasino] The game by \"$player\" has successfully stopped.");
								unset($this->casino[$player]);
							}
						}
						break;
					case "join":
						if(!$sender instanceof Player){
							$sender->sendMessage("Please run this command in-game.");
							break;
						}
						if(!$sender->hasPermission("economycasino.command.casino.join")){
							return true;
						}
						$player = array_shift($params);
						if(trim($player) === ""){
							$sender->sendMessage("Usage: /casino join <player>");
							break;
						}
						if(isset($this->casino[$player])){
							foreach($this->casino[$player]["players"] as $player => $v){
								if(($p = $this->getServer()->getPlayerExact($player)) instanceof Player){
									$p->sendMessage("[EconomyCasino] ".$sender->getName()." has joined the game.");
								}
							}
							$this->casino[$player]["players"][$sender->getName()] = true;
							$sender->sendMessage("You've joined the casino.");
						}else{
							$sender->sendMessage("There's no casino where are looking for.");
						}
						break;
					case "leave":
						if(!$sender instanceof Player){
							$sender->sendMessage("Please run this command in-game.");
							break;
						}
						if(!$sender->hasPermission("economycasino.command.casino.leave")){
							return true;
						}
						foreach($this->casino as $player => $casino){
							if(isset($casino["players"][$sender->getName()])){
								unset($this->casino[$player]["players"][$sender->getName()]);
								foreach($casino["players"] as $p => $v){
									$this->getServer()->getPlayerExact($p)->sendMessage("[EconomyCasino] ".$sender->getName()." left the game.");
								}
								break;
							}
						}
						$sender->sendMessage("[EconomyCasino] You have no casino game to leave.");
						break;
					case "list":
						if(!$sender->hasPermission("economycasino.command.casino.list")){
							return true;
						}
						$player = array_shift($params);
						if(trim($player) === ""){
							list_general:
							$output = "[EconomyCasino] Game list : \n";
							foreach($this->casino as $player => $casino){
								$output .= "$player : ".(count($this->casino[$player]["players"]))." \n";
							}
							$output = substr($output, 0, -2);
							$sender->sendMessage($output);
						}else{
							if(isset($this->casino[$player])){
								$output = "[EconomyCasino] Player list of casino game by : $player \n";
								foreach($this->casino[$player]["players"] as $p){
									$output .= "$p, ";
								}
								$output = substr($output, 0, -2);
							}else{
								goto list_general;
							}
						}
						break;
					case "gamble":
						if(!$sender instanceof Player){
							$sender->sendMessage("Please run this command in-game.");
							break;
						}
						if(!$sender->hasPermission("economycasino.command.casino.gamble")){
							return true;
						}
						$money = array_shift($params);
						if(!is_numeric($money)){
							$sender->sendMessage("Usage: /casino gamble <money>");
							break;
						}
						$money = (int)$money;
						if($this->api->myMoney($sender) < $money){
							$sender->sendMessage("You don't have money to gamble ".$this->api->getMonetaryUnit()."$money");
							break;
						}
						if(isset($this->casino[$sender->getName()])){
							$all = 0;
							foreach($this->casino[$sender->getName()]["players"] as $player => $v){
								$tmp = min($money, $this->api->myMoney($player));
								$this->api->reduceMoney($player, $tmp);
								$all += $tmp;
							}
							$got = array_rand($this->casino[$sender->getName()]["players"]);
							
							$this->api->addMoney($got, $all, true, "EconomyCasino");
							
							foreach($this->casino[$sender->getName()]["players"] as $p => $v){
								if($got === $p){
									$this->getServer()->getPlayerExact($p)->sendMessage("You've win ".$this->api->getMonetaryUnit()."$all!");
								}else{
									$this->getServer()->getPlayerExact($p)->sendMessage("You've lost ".$this->api->getMonetaryUnit()."$money");
								}
							}
						}else{
							foreach($this->casino as $player => $casino){
								if(isset($casino["players"][$sender->getName()])){
									$all = 0;
									foreach($this->casino[$player]["players"] as $p => $true){
										$tmp = min($this->api->myMoney($p), $money);
										$this->api->reduceMoney($p, $tmp);
										$all += $tmp;
									}
									$got = array_rand($this->casino[$player]["players"]);
									$this->api->addMoney($got, $all, true, "EconomyCasino");
									foreach($this->casino[$player]["players"] as $p => $v){
										if($got === $p){
											$this->getServer()->getPlayerExact($p)->sendMessage("You've win ".$this->api->getMonetaryUnit()."$all!");
										}else{
											$this->getServer()->getPlayerExact($p)->sendMessage("You've lost ".$this->api->getMonetaryUnit()."$money");
										}
									}
								}
							}
						}
						break;
					default:
						$sender->sendMessage("Usage: ".$command->getUsage());
				}
				break;
			case "jackpot":
				if(!$sender instanceof Player){
					$sender->sendMessage("Please run this command in-game.");
					break;
				}
				$money = array_shift($params);
				if(!is_numeric($money)){
					$sender->sendMessage("Usage: ".$command->getUsage());
					break;
				}
				$money = (int)$money;
				if($this->api->myMoney($sender) < $money){
					$sender->sendMessage("You don't have money to jackpot ".$this->api->getMonetaryUnit()."$money");
					break;
				}
				
				$rand = rand(0, $this->config->get("jackpot-winning"));
				if($rand === 0){
					$this->api->addMoney($sender, $money);
					$sender->sendMessage("You've wined jackpot! You've got ".$this->api->getMonetaryUnit()."$money");
				}else{
					$this->api->reduceMoney($sender, $money);
					$sender->sendMessage("You've failed your jackpot! You've lost ".$this->api->getMonetaryUnit()."$money");
				}
				break;
		}
		return true;
	}
}