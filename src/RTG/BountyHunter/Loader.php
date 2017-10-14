<?php
/**
 * Created by PhpStorm.
 * User: RTG
 * Date: 14/10/2017
 * Time: 4:25 PM
 */

namespace RTG\BountyHunter;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;

class Loader extends PluginBase {

    public $db_file = "saves.db";
    public $db;
    const prefix = '[BountyHunter]';

    public function onEnable() {

        if (!is_dir($this->getDataFolder())) {
            mkdir($this->getDataFolder());
        }

        if (!is_file($this->getDataFolder() . $this->db_file)) {
            $this->db = new \SQLite3($this->getDataFolder() . $this->db_file);
            $this->db->exec("CREATE TABLE IF NOT EXISTS `list` (`id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, `name` TEXT NOT NULL, `bounty` INTEGER NOT NULL);");
        } else {
            $this->getLogger()->warning(self::prefix . " Database has been loaded under the name of $this->db_file");
        }

    }

    /**
     * @return bool
     */
    public function checkConfig(): bool {
        $json = json_decode(file_get_contents($this->getDataFolder() . "config.json"));
        if ($json['enabled'] === true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return \SQLite3
     */
    public function getDatabase() {
        $this->db = new \SQLite3($this->getDataFolder() . $this->db_file);
        return $this->db;
    }

    // Bounty API

    /**
     * @param $name
     * @return int
     */
    public function getBounty($name): int {
        $statement = "SELECT * FROM `list` WHERE `name` = '$name'";
        $res = $this->getDatabase()->query($statement);
        if ($row = $res->fetchArray(1)) {
            return $row['bounty'];
        } else {
            return 0;
        }
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasBounty($name): bool {
        $statement = "SELECT * FROM `list` WHERE `name` = '$name'";
        $res = $this->getDatabase()->query($statement);
        if ($row = $res->fetchArray(1)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $name
     * @param int $integer
     * @return bool
     */
    public function setBounty($name, int $integer): bool {
        if (!$this->hasBounty($name)) {
            $statement = "SELECT * FROM `list` WHERE `name` = '$name'";
            $res = $this->getDatabase()->query($statement);
            if ($row = $res->fetchArray(1)) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * @param $name
     * @param int $integer
     * @return bool
     */
    public function increaseBounty($name, int $integer): bool {
        if ($this->hasBounty($name) === true) {
            $old = $this->getBounty($name);
            $inc = $old + $integer;
            $statement = "UPDATE `list` SET `bounty` = '$inc' WHERE `name` = '$name'";
            $res = $this->getDatabase()->query($statement);
                if ($row = $res->fetchArray(1)) {
                    return true;
                } else {
                    return false;
                }
        }
    }

    /**
     * @param $name
     * @param int $integer
     * @return bool
     */
    public function reduceBounty($name, int $integer): bool {
        if ($this->hasBounty($name) === true) {
            $old = $this->getBounty($name);
            $inc = $old - $integer;
            $statement = "UPDATE `list` SET `bounty` = '$inc' WHERE `name` = '$name'";
            $res = $this->getDatabase()->query($statement);
            if ($row = $res->fetchArray(1)) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function getAll(CommandSender $sender) {
        $statement = "SELECT * FROM `list`";
        $res = $this->getDatabase()->query($statement);
        $sender->sendMessage("Total affected Players: ");
        while ($row = $res->fetchArray(1)) {
            $sender->sendMessage($row['name']);
        }
    }

    // ----------------- COMMANDS -------------------

    public function onCommand(CommandSender $sender, Command $command, string $commandLabel, array $args): bool {

        if ($sender->hasPermission("bounty.command")) {
            switch ($command->getName()) {
                case "bh":

                    if (isset($args[0])) {
                        switch ($args[0]) {
                            case "list":
                                $this->getAll($sender);
                                return true;
                            break;
                            case "set":
                                if (isset($args[1])) {
                                    if (isset($args[2])) {
                                        if ($args[1] instanceof Player) {
                                            if (empty($args[2])) {
                                                $int = 100;
                                            } else {
                                                if (is_int($args[2])) {
                                                    $int = $args[2];
                                                }
                                            }
                                            $this->setBounty($args[1], $int);
                                        } else {
                                            $sender->sendMessage("$args[1] is not a valid Player!");
                                        }
                                    }
                                }
                                return true;
                            break;
                        }
                    } else {
                        $sender->sendMessage("[Usage] /bh list");
                    }
                    return true;
                break;
            }
        } else {
            $sender->sendMessage("No Perm!");
        }
    }
}