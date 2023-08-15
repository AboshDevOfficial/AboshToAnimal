<?php

namespace PM\Task;

use pocketmine\entity\EffectInstance;
use pocketmine\entity\Effect;
use pocketmine\network\mcpe\protocol\MoveEntityAbsolutePacket;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use PM\Loader;
use PM\MorphsManager\MorphsSession;

class MorphsTask extends Task
{

    public $plugin;

    public function __construct(Loader $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onRun(int $refreshTick)
    {

        foreach (Server::getInstance()->getOnlinePlayers() as $p) {

            if (MorphsSession::getMorphsPlayer($p)) {
            	
                $pk = new MoveEntityAbsolutePacket();
                $pk->entityRuntimeId = MorphsSession::$players[$p->getName()]["entity"];
                $pk->position = $p->asVector3()->add(0, 0, 0);
                $pk->xRot = $p->pitch;
                $pk->yRot = $p->yaw;
                $pk->zRot = $p->yaw;
                $pk->flags = MoveEntityAbsolutePacket::FLAG_TELEPORT;
                $p->sendDataPacket($pk);
                Server::getInstance()->broadcastPacket(Server::getInstance()->getOnlinePlayers(), $pk);

            }
        }
    }
}
