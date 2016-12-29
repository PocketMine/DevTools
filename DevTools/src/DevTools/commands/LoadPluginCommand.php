<?php

namespace DevTools\commands;

use DevTools\DevTools;

use pocketmine\command\CommandSender;
use pocketmine\event\TranslationContainer;
use pocketmine\utils\TextFormat;

class LoadPluginCommand extends DevToolsCommand{

	public function __construct(DevTools $plugin, $name){
		parent::__construct(
			$plugin,
			$name,
			"Load a plugin",
			"/loadplugin <file name or folder name>",
			["lp"]
		);
		$this->setPermission("devtools.command.loadplugin");
	}

	public function execute(CommandSender $sender, $currentAlias, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}

		if(count($args) === 0){
			$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));
			return false;
		}

		$plugin = $sender->getServer()->getPluginManager()->loadPlugin($sender->getServer()->getPluginPath() . $args[0]);
		if($plugin != null){
			$sender->getServer()->getPluginManager()->enablePlugin($plugin);
			return true;
		}
		return false;
	}
}