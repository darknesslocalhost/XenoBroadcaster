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
        $server = $this->plugInstance->getServer(); // Pobieranie obiektu serwera
        $message = str_replace("&", "§", $message);
        $message = str_replace("{break}", "\n", $message);
        $message = str_replace("{online}", count($server->getOnlinePlayers()), $message);
        $message = str_replace("{max}", $server->getMaxPlayers(), $message);
        $message = str_replace("{motd}", $server->getMotd(), $message);
        $message = str_replace("{tps}", $server->getTicksPerSecond(), $message);
        $message = str_replace("{api}", $server->getVersion());
        $server->broadcastMessage($prefix . $message);
    }
}
