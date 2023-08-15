<?php

namespace PM;


use pocketmine\plugin\PluginBase as PL;

use pocketmine\event\Listener;
use PM\commands\MorphsCommand;
use PM\Task\MorphsTask;

class Loader extends PL implements Listener
{

    public function onEnable()
    {
        $this->registerTask();
        $this->registerCommands();
    }


    public function registerCommands()
    {
        $MorphsCmd = new MorphsCommand($this);
        $this->getServer()->getCommandMap()->register("morph", $MorphsCmd);
    }

    public function registerTask()
    {
        $this->getScheduler()->scheduleRepeatingTask(new MorphsTask($this), 0);
    }
}
