<?php

namespace levelsystem;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class EventListener implements Listener{

    private $main;

    public function __construct(Level $main)
    {
        $this->main = $main;
    }

    public function onBreak(BlockBreakEvent $event){
        $player = $event->getPlayer();
        $blockid = $event->getBlock()->getId();
        if ($blockid !== 56 or $blockid !== 129) {
            $this->main->addExp($player, rand(10, 20));
            if ($this->main->getExp($player) >= $this->main->limitExp($player)) {
                $exp = $this->main->getExp($player);
                $limit = $this->main->limitExp($player);
                    $result = $exp - $limit;
                    $this->main->setExp($player, $result);
                    $this->main->levelUp($player);
                    $player->sendMessage("Your level now is " . $this->main->getLevel($player));
            }
        } else {
            $this->main->addExp($player, rand(30, 50));
            if ($this->main->getExp($player) >= $this->main->limitExp($player)) {
                $exp = $this->main->getExp($player);
                $limit = $this->main->limitExp($player);
                $result = $exp - $limit;
                $this->main->setExp($player, $result);
                $this->main->levelUp($player);
                $player->sendMessage("Your level now is" . $this->main->getLevel($player));
            }
        }
    }

    public function onJoin(PlayerJoinEvent $event){
            $player = $event->getPlayer();
            $name = $player->getName();
            $level = $this->main->getDatabase()->query("SELECT level FROM level WHERE username='$name'");
            $level = mysqli_fetch_row($level);
            if(!isset($level[0])) {
                $this->main->getDatabase()->query("INSERT INTO level VALUES('', '$name', 0, 1)");
            }
    }
}
