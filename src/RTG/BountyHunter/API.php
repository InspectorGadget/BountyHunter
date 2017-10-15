<?php
/**
 * Created by PhpStorm.
 * User: RTG
 * Date: 15/10/2017
 * Time: 3:20 PM
 */

class API {

    public $plugin;
    public $db;

    public function __construct(\RTG\BountyHunter\Loader $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @return bool
     */
    public function checkConfig(): bool {
        $json = json_decode(file_get_contents($this->plugin->getDataFolder() . "config.json"));
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
        $this->db = new \SQLite3($this->plugin->getDataFolder() . $this->plugin->db_file);
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
            $statement = "INSERT INTO `list` (name, bounty) VALUES ('$name', '$integer')";
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

}