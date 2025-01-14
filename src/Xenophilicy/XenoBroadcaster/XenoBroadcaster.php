<?php
namespace Xenophilicy\XenoBroadcaster;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;

class XenoBroadcaster extends PluginBase implements Listener {

    private $config;

    public static $instance;

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        self::$instance = $this;
        $this->hasValidInterval();
    }

    public function onDisable(): void {
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if ($sender->hasPermission("xenobroadcaster.use")) {
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
        $interval = $this->config->get("Interval-Delay");

        if (!is_int($interval)) {
            $this->getLogger()->critical("Invalid interval in the config! Plugin Disabling...");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return false;
        } else {
            $this->getScheduler()->scheduleRepeatingTask(new BroadcastTask(), $interval * 20);
            return true;
        }
    }

    public static function getInstance() {
        return self::$instance;
    }
}
