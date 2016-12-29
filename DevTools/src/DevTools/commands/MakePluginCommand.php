<?php

namespace DevTools\commands;

use DevTools\DevTools;
use FolderPluginLoader\FolderPluginLoader;
use pocketmine\command\CommandSender;
use pocketmine\event\TranslationContainer;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class MakePluginCommand extends DevToolsCommand{

	public function __construct(DevTools $plugin, $name){
		parent::__construct(
			$plugin,
			$name,
			"Creates a Phar plugin from source code",
			"/makeplugin <pluginName> [no-gz] [no-echo]",
			["mp"]
		);
		$this->setPermission("devtools.command.makeplugin");
	}

	public function execute(CommandSender $sender, $currentAlias, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}

		if(count($args) === 0){
			$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));
			return false;
		}

		$pluginName = trim(str_replace(["no-gz", "no-echo"], "", implode(" ", $args)));
		
		if($pluginName !== "FolderPluginLoader"){
			if($pluginName === "" or !(($plugin = Server::getInstance()->getPluginManager()->getPlugin($pluginName)) instanceof Plugin)){
				$sender->sendMessage(TextFormat::RED . "Invalid plugin name, check the name case.");
				return true;
			}
			$description = $plugin->getDescription();

			if(!($plugin->getPluginLoader() instanceof FolderPluginLoader)){
				$sender->sendMessage(TextFormat::RED . "Plugin " . $description->getName() . " is not in folder structure.");
				return true;
			}
			$metadata = [
				"name" => $description->getName(),
				"version" => $description->getVersion(),
				"main" => $description->getMain(),
				"api" => $description->getCompatibleApis(),
				"depend" => $description->getDepend(),
				"description" => $description->getDescription(),
				"authors" => $description->getAuthors(),
				"website" => $description->getWebsite(),
				"creationDate" => time()
			];
		}else{
			$metadata = [
				"name" => "FolderPluginLoader",
				"version" => "1.1.0",
				"main" => "FolderPluginLoader\\Main",
				"api" => ["2.0.0"],
				"depend" => [],
				"description" => "Loader of folder plugins",
				"authors" => ["PocketMine Team"],
				"website" => "https://github.com/PocketMine/DevTools",
				"creationDate" => time()
			];
		}
		
		
		@mkdir($this->getPlugin()->getWorkingDirectory());
		$pharPath = $this->getPlugin()->getWorkingDirectory() . $pluginName . "_v" . $metadata["version"] ."_" . date("Y-m-d_h-i-s"). ".phar";
		if(file_exists($pharPath)){
			$sender->sendMessage("Phar plugin already exists, overwriting...");
			@\Phar::unlinkArchive($pharPath);
		}
		$phar = new \Phar($pharPath);
		$phar->setMetadata($metadata);
		if($pluginName === "DevTools"){
			$phar->setStub('<?php require("phar://". __FILE__ ."/src/DevTools/ConsoleScript.php"); __HALT_COMPILER();');
		}elseif($pluginName === "FolderPluginLoader"){
			$phar->setStub('<?php __HALT_COMPILER();');
		}else{
			$phar->setStub('<?php echo "PocketMine-MP/Genisys plugin ' . $description->getName() . ' v' . $description->getVersion() . '\nThis file has been generated using DevTools at ' . date("r") . '\n----------------\n";if(extension_loaded("phar")){$phar = new \Phar(__FILE__);foreach($phar->getMetadata() as $key => $value){echo ucfirst($key).": ".(is_array($value) ? implode(", ", $value):$value)."\n";}} __HALT_COMPILER();');
		}
		$phar->setSignatureAlgorithm(\Phar::SHA1);
		$phar->startBuffering();
		if($pluginName === "FolderPluginLoader"){
			$phar->addFromString("plugin.yml", "name: FolderPluginLoader\nversion: 1.1.0\nmain: FolderPluginLoader\\Main\napi: [2.0.0]\nload: STARTUP\n");
			$phar->addFile($this->getPlugin()->getFile() . "src/FolderPluginLoader/FolderPluginLoader.php", "src/FolderPluginLoader/FolderPluginLoader.php");
			$phar->addFile($this->getPlugin()->getFile() . "src/FolderPluginLoader/Main.php", "src/FolderPluginLoader/Main.php");
		}else{
			$reflection = new \ReflectionClass("pocketmine\\plugin\\PluginBase");
			$file = $reflection->getProperty("file");
			$file->setAccessible(true);
			$filePath = rtrim(str_replace("\\", "/", $file->getValue($plugin)), "/") . "/";
		
			foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($filePath)) as $file){
				$path = ltrim(str_replace(["\\", $filePath], ["/", ""], $file), "/");
				if($path{0} === "." or strpos($path, "/.") !== false){
					continue;
				}
				$phar->addFile($file, $path);
				if(!in_array("no-echo", $args)){
					$sender->sendMessage("[DevTools] Adding $path");
				}
			}
		}

		foreach($phar as $file => $finfo){
			/** @var \PharFileInfo $finfo */
			if($finfo->getSize() > (1024 * 512)){
				$finfo->compress(\Phar::GZ);
			}
		}
		if(!in_array("no-gz", $args)){
			$phar->compressFiles(\Phar::GZ);
		}
		$phar->stopBuffering();
		
		$sender->sendMessage("Phar plugin " . $metadata["name"] . " v" . $metadata["version"] . " has been created in " . $pharPath);
		return true;
	}
}
