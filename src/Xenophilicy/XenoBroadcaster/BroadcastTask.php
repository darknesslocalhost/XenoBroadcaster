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
        $server = $this->plugInstance->getServer();

        $message = str_replace(["&", "{break}", "{online}", "{max}", "{motd}", "{tps}", "{api}"], ["§", "\n", count($server->getOnlinePlayers()), $server->getMaxPlayers(), $server->getMotd(), $server->getTicksPerSecond(), $server->getVersion()], $message);

        $this->plugInstance->broadcastMessage($prefix . $message);
    }
}
