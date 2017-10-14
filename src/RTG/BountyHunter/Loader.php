<?php
/**
 * Created by PhpStorm.
 * User: RTG
 * Date: 14/10/2017
 * Time: 4:25 PM
 */

namespace RTG\BountyHunter;

use pocketmine\plugin\PluginBase;

class Loader extends PluginBase {

    public $db_file = "saves.db";
    public $db;
    const prefix = '[BountyHunter]';

    public function onEnable() {

        // Config Check
        if ($this->checkConfig() === true) {

            if (!is_file($this->getDataFolder() . $this->db_file)) {
                $this->db = new \SQLite3($this->getDataFolder() . $this->db_file);
                $this->db->exec("CREATE TABLE IF NOT EXISTS `list` (`id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, `name` TEXT NOT NULL, `bounty` INTEGER NOT NULL);");
            } else {
                $this->getLogger()->warning(self::prefix . " Database has been loaded under the name of $this->db_file");
            }

            $this->onRegisterCommands();

        } else {
            $this->setEnabled(false);
        }

    }

    public function onRegisterCommands() {

    }

    /**
     * @return bool
     */
    public function checkConfig(): bool {
        $json = json_decode(file_get_contents("config.json"));
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
        $statement = "SELECT * FROM `list` WHERE `name` = '$name'";
        $res = $this->getDatabase()->query($statement);
        if ($row = $res->fetchArray(1)) {
            return false;
        } else {
            return true;
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

}