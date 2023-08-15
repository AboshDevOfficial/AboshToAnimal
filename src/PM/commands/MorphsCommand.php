<?php

namespace PM\commands;

use PM\Loader;
use pocketmine\entity\Attribute;
use PM\MorphsManager\Morphs;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use PM\MorphsManager\MorphsSession;
use pocketmine\tile\Tile;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\Effect;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;


class MorphsCommand extends Command implements PluginIdentifiableCommand
{

    public $plugin;

    const ALIASE_SUBCOMMAND_ADDD = [
        "set",
        "add",
        "new"
    ];
    const ALIASE_SUBCOMMAND_REMOVEE = [
        "remove",
        "del",
        "delete",
        "rm"
    ];

    public function __construct(Loader $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct('morph', 'Morphs you');
        $this->setUsage('/morph <add:remove:list>');
    }

    public function getPlugin(): Plugin
    {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $label, array $args): bool
    {
        if ($this->plugin->isEnabled()) {
            if ($sender instanceof ConsoleCommandSender) {
                $sender->sendMessage('§cPlease run this command in game');
                return true;
            }

            if ($sender->hasPermission('morph.use.command')) {

                if (!isset($args[0])) {
                	$sender->sendMessage("§e<===============================>");
                    $sender->sendMessage("§e<===============================>");
                	$sender->sendMessage("§b Version-1");
                    $sender->sendMessage("§l§c[Morphs]");
                    $sender->sendMessage("§a- §b/morph <list> §5Morphs for using!");
                    $sender->sendMessage("§a- §b/morph <set> <name of the morph> §5Select morph.");
                    $sender->sendMessage("§a- §b/morph <remove> <name of the morph> §5remove your current morph.");
                    $sender->sendMessage("§a- §bCredits to MortalClient357");
                    $sender->sendMessage("§e<===============================>");
                    $sender->sendMessage("§e<===============================>");
                    return false;
                }

                if (isset($args[0])) {
                    if ($sender instanceof Player) {


                        if (in_array($args[0], self::ALIASE_SUBCOMMAND_ADDD)) {
                            if (isset($args[1])) {

                                if (empty($args[1])) {
                                    $sender->sendMessage('§c§l[PlayerMorphs] §r§cplease enter the specific name of morph.');
                                    return true;
                                }


                                if (!is_numeric($args[1])) {

                                    if (!in_array($args[1], Morphs::$morphsAsStr)) {
                                        $sender->sendMessage("§c§l[PlayerMorphs] §r§cthis morph doesn't exist on our list, please use /morph list for see our list of morphs");
                                        return false;
                                    }


                                    $morphs_name = Morphs::$morphs[$args[1]];


                                    if (isset($morphs_name)) {
                                        MorphsSession::setMorphs($sender, $args{1});
                                        $sender->addEffect(new EffectInstance(Effect::getEffect(Effect::INVISIBILITY), 1060*20, 1, false));
                                        $sender->addTitle("§6". $args[1]);
                                        $sender->sendMessage("§c§l[PlayerMorphs] §r§ayou have successfully added a morph " . $args[1]);

                                    } else {
                                        $sender->sendMessage("§c§l[PlayerMorphs] §r§cthis morphs doesn't exist please use the command /morph list for see the list.");
                                    }
                                } else {

                                    $sender->sendMessage("§c§l[PlayerMorphs] §r§cplease enter the specific name of morph");
                                }
                            }
                        }

                        if ($args[0] == 'list') {

                            $i = 0;
                            $sender->sendMessage('§e>= All Morphs for using =<');
                            foreach (Morphs::$morphsAsStr as $name) {
                                $i++;
                                $sender->sendMessage('§c- ' . $i . '§a '.$name);
                            }
                        }

                        if (in_array($args[0], self::ALIASE_SUBCOMMAND_REMOVEE)) {
                            if (isset($args[1])) {

                                if (empty($args[1])) {
                                    $sender->sendMessage('§c§l[PlayerMorphs] §r§cplease enter the specific name of morph.');
                                    return true;
                                }


                                if (!is_numeric($args[1])) {


                                    if (!in_array($args[1], Morphs::$morphsAsStr)) {
                                        $sender->sendMessage("§c§l[PlayerMorphs] §r§cthis morph doesn't exist on our list.");
                                        return false;
                                    }

                                    $name = Morphs::$morphs[$args[1]];

                                    if (MorphsSession::getMorphsPlayer($sender)) {

                                        if (MorphsSession::getMorphsPlayerName($sender) === $name) {
                                            MorphsSession::removeMorphs($sender);
                                            $sender->sendMessage("§c§l[PlayerMorphs] §r§ayou have removed the morph: " . $args[1]);
                                            $sender->removeAllEffects();

                                        } else {
                                            $sender->sendMessage("§c§l[PlayerMorphs] §r§cthe morph you put does not match the morph you have.");
                                        }

                                    } else {
                                        $sender->sendMessage("§c§l[PlayerMorphs] §r§cyou can't remove morph because you don't have any morphs.");
                                    }
                                } else {
                                    $sender->sendMessage("§c§l[PlayerMorphs] §r§cplease enter the specific name of the morph");
                                }

                            }
                        }
                    } else {
                        $sender->sendMessage("§c§l[PlayerMorphs] §r§cYou don't have the permission to use this command");
                    }
                }
            }
        }
        return true;
    }
}
