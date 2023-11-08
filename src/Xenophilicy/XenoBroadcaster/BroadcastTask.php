<?php
namespace Xenophilicy\XenoBroadcaster;

use pocketmine\scheduler\Task;

class BroadcastTask extends Task {
    private $plugInstance;

    public function onRun(int $currentTick): void {
        $this->plugInstance = XenoBroadcaster::getInstance();
        $prefix = str_replace("&", "§", $this->plugInstance->getConfig()->get("Message-Prefix"));
        $messages = $this->plugInstance->getConfig()->get("Messages");
        $message = $messages[array_rand($messages)];
        $this->plugInstance = XenoBroadcaster::getInstance()->getServer();
        $message = str_replace("&", "§", $message);
        $message = str_replace("{break}", "\n", $message);
        $message = str_replace("{online}", count($this->plugInstance->getOnlinePlayers()), $message);
        $message = str_replace("{max}", $this->plugInstance->getMaxPlayers(), $message);
        $message = str_replace("{motd}", $this->plugInstance->getMotd(), $message);
        $message = str_replace("{tps}", $this->plugInstance->getTicksPerSecond(), $message);
        $message = str_replace("{api}", $this->getServer()->getVersion());
        $this->plugInstance->broadcastMessage($prefix . $message);
    }
}
