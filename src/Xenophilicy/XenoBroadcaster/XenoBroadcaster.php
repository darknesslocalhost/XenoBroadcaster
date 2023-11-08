<?php
# MADE BY:
#  __    __                                          __        __  __  __                     
# /  |  /  |                                        /  |      /  |/  |/  |                    
# $$ |  $$ |  ______   _______    ______    ______  $$ |____  $$/ $$ |$$/   _______  __    __ 
# $$  \/$$/  /      \ /       \  /      \  /      \ $$      \ /  |$$ |/  | /       |/  |  /  |
#  $$  $$<  /$$$$$$  |$$$$$$$  |/$$$$$$  |/$$$$$$  |$$$$$$$  |$$ |$$ |$$ |/$$$$$$$/ $$ |  $$ |
#   $$$$  \ $$    $$ |$$ |  $$ |$$ |  $$ |$$ |  $$ |$$ |  $$ |$$ |$$ |$$ |$$ |      $$ |  $$ |
#  $$ /$$  |$$$$$$$$/ $$ |  $$ |$$ \__$$ |$$ |__$$ |$$ |  $$ |$$ |$$ |$$ |$$ \_____ $$ \__$$ |
# $$ |  $$ |$$       |$$ |  $$ |$$    $$/ $$    $$/ $$ |  $$ |$$ |$$ |$$ |$$       |$$    $$ |
# $$/   $$/  $$$$$$$/ $$/   $$/  $$$$$$/  $$$$$$$/  $$/   $$/ $$/ $$/ $$/  $$$$$$$/  $$$$$$$ |
#                                         $$ |                                      /  \__$$ |
#                                         $$ |                                      $$    $$/ 
#                                         $$/                                        $$$$$$/

namespace Xenophilicy\XenoBroadcaster;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\server\Server;

class XenoBroadcaster extends PluginBase implements Listener {

    private $config;

    public static $serverInstance;

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        self::$serverInstance = $this;
        $this->hasValidInterval();
    }

    public function onDisable(): void {
        $this->getLogger()->info("§6XenoBroadcaster§c has been disabled!");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if ($sender->hasPermission("xenobcast.use")) {
            if (isset($args[0])) {
                switch (strtolower($command->getName())) {
                    case 'bcast':
                    case 'broadcast':
                        $prefix = str_replace("&", "§", $this->config->get("Message-Prefix"));
                        $message = str_replace("&", "§", implode(" ", $args));
                        $this->getServer()->broadcastMessage($prefix . $message);
                        break;
                }
            } else {
                $sender->sendMessage("§eEnter a message to broadcast!");
            }
            return true;
        } else {
            $sender->sendMessage("§cYou don't have permission to broadcast!");
        }
        return true;
    }

    private function hasValidInterval(): bool {
        $this->saveDefaultConfig();
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        if (!is_int($this->config->get("Interval-Delay"))) {
            $this->getLogger()->critical("Invalid interval in the config! Plugin Disabling...");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return false;
        } elseif (is_int($this->config->get("Interval-Delay"))) {
            $this->getLogger()->info("§6XenoBroadcaster§a has been enabled!");
            $this->getScheduler()->scheduleRepeatingTask(new BroadcastTask(), $this->config->get("Interval-Delay") * 20);
            return true;
        }
        return true;
    }

    public static function getInstance() {
        return self::$serverInstance;
    }
}
