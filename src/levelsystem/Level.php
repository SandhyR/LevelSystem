<?php

namespace levelsystem;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\player\Player;

class Level extends PluginBase{

    private $config;
    private static $instance;
    private $level;

    public function onLoad(): void
    {
        self::$instance = $this;
    }

    public static function getInstance(){
        return self::$instance;
    }

    public function onEnable(): void
    {
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, array());
        $this->getServer()->getPluginManager()->registerEvents(New EventListener($this), $this);
        $this->getDatabase()->query("CREATE TABLE level ( id INT PRIMARY KEY AUTO_INCREMENT , username VARCHAR(255) NOT NULL , exp INT(11) NOT NULL, level INT(11) NOT NULL);");
    }

    public function getDatabase(){
        return new \mysqli($this->config->get("host"), $this->config->get("user"), $this->config->get("password"), $this->config->get("db-name"));
    }

    public function getExp(Player $player){
        $playername = $player->getName();
        $exp = $this->getDatabase()->query("SELECT exp FROM level WHERE username='$playername'");
        $exp = mysqli_fetch_row($exp);
        return $exp[0];
    }
    public function getLevel(Player $player)
    {
        $playername = $player->getName();
        $level = $this->getDatabase()->query("SELECT level FROM level WHERE username='$playername'");
        $level = mysqli_fetch_row($level);
        if ($level[0] < 20) {
            $this->level[$playername] = TextFormat::GRAY . "[$level[0]]";
            $levela = TextFormat::GRAY . $level[0];
        } elseif ($level[0] < 40) {
            $this->level[$playername] = TextFormat::BLUE . "[$level[0]]";
            $levela = TextFormat::DARK_BLUE . $level[0];
        } elseif ($level[0] < 80) {
            $this->level[$playername] = TextFormat::GREEN . "[$level[0]]";
            $levela = TextFormat::GREEN . $level[0];
        } elseif ($level[0] < 100) {
            $this->level[$playername] = TextFormat::YELLOW . "[$level[0]]";
        } else {
            $this->level[$playername] = TextFormat::RED . "[$level[0]]";
        }
        return $this->level[$playername];
    }

    public function setLevel(Player $player, $value){
        $playername = $player->getName();
        $level = $this->getDatabase()->query("UPDATE level set level='$value' WHERE username='$playername'");
        $level = mysqli_fetch_row($level);
    }


    public function limitExp(Player $player){
        $playername = $player->getName();
        $level = $this->getDatabase()->query("SELECT level FROM level WHERE username='$playername'");
        $level = mysqli_fetch_row($level);
        $limit = $level[0] * 1000;
        return $limit;
    }

    public function levelUp(Player $player)
    {
        $playername = $player->getName();
        $level = $this->getDatabase()->query("SELECT level from level WHERE username='$playername'");
        $level = mysqli_fetch_row($level);
        $this->getDatabase()->query("UPDATE level SET level=$level[0] + 1 WHERE username='$playername'");
    }

    public function addExp(Player $player, $value){
        $playername = $player->getName();
        $exp = $this->getDatabase()->query("SELECT exp FROM level WHERE username='$playername'");
        $exp = mysqli_fetch_row($exp);
        $this->getDatabase()->query("UPDATE level SET exp=$exp[0] + $value WHERE username='$playername'");
    }

    public function setExp(Player $player, $value){
        $playername = $player->getName();
        $this->getDatabase()->query("UPDATE level SET exp=$value WHERE username='$playername'");
    }
}
