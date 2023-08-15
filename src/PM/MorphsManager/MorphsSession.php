<?php

namespace PM\MorphsManager;

use pocketmine\entity\Entity;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\Effect;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\network\mcpe\protocol\RemoveEntityPacket;
use pocketmine\Server;
use pocketmine\Player;

class MorphsSession
{
    public static $players = [];

    const BIGGEST_MORPHS_BY_ID = [
        "53",
        "41"
    ];

    public static function getMorphsPlayerName(Player $player)
    {
        if (isset(MorphsSession::$players[$player->getName()])) {
            return MorphsSession::$players[$player->getName()]["id"];
        }
        return false;
    }

    public static function getMorphsPlayer(Player $player)
    {
        if (isset(MorphsSession::$players[$player->getName()])) {
            return MorphsSession::$players[$player->getName()];
        }
        return false;
    }

    public static function setMorphs(Player $player, string $name)
    {
        if (isset(Morphs::$morphs[$name])) {
            self::removeMorphs($player);
            MorphsSession::$players[$player->getName()]["entity"] = Entity::$entityCount++;
            MorphsSession::$players[$player->getName()]["id"] = Morphs::$morphs[$name];

            $pk = new AddEntityPacket();
            $pk->entityRuntimeId = MorphsSession::$players[$player->getName()]["entity"];
            $pk->type = MorphsSession::$players[$player->getName()]["id"];
            $pk->position = $player->asVector3()->add(0, 3, 0);
            $pk->yaw = $player->getYaw();
            $pk->pitch = $player->getPitch();
            if (in_array(MorphsSession::$players[$player->getName()]["id"], self::BIGGEST_MORPHS_BY_ID)) {
                $pk->metadata = [Entity::DATA_SCALE => [Entity::DATA_TYPE_FLOAT, 0.5]];
            }

            Server::getInstance()->broadcastPacket(Server::getInstance()->getOnlinePlayers(), $pk);
        }
    }

    public static function removeMorphs(Player $player)
    {

        if (isset(MorphsSession::$players[$player->getName()])) {

            $pk = new RemoveEntityPacket();
            $pk->entityUniqueId = MorphsSession::$players[$player->getName()]["entity"];
            $player->sendDataPacket($pk);

            Server::getInstance()->broadcastPacket(Server::getInstance()->getOnlinePlayers(), $pk);
            unset(MorphsSession::$players[$player->getName()]);

            return true;
        }
        return false;
    }

}
