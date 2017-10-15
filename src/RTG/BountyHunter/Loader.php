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

    public function getAPI() {
        return new \API($this);
    }

    public function onCommand(CommandSender $sender, Command $command, string $commandLabel, array $args): bool {

        if ($sender->hasPermission("bounty.command")) {
            switch ($command->getName()) {
                case "bh":

                    if (isset($args[0])) {

                        switch ($args[0]) {

                            case "list":
                                $this->getAPI()->getAll($sender);
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
                                                } else {
                                                    $int = 100;
                                                }
                                            }
                                            $this->getAPI()->setBounty($args[1], $int);
                                        } else {
                                            $sender->sendMessage("$args[1] is not a valid Player!");
                                        }
                                    }
                                } else {
                                    $sender->sendMessage("[Usage] /bh set {name} {bounty}");
                                }
                                return true;
                            break;

                            case "inbounty":
                                if (isset($args[1]) && isset($args[2])) {
                                    if ($args[1] instanceof Player) {
                                        if (empty($args[2])) {
                                            $sender->sendMessage("[Usage] /bh addbounty {name} {bounty:int}");
                                            return true;
                                        } else {
                                            if (is_int($args[2])) {
                                                $this->getAPI()->increaseBounty($args[1], $args[2]);
                                                $sender->sendMessage("Done");
                                            } else {
                                                $sender->sendMessage("Bounty has to be in a form of Integer!");
                                            }
                                        }
                                    } else {
                                        $sender->sendMessage("$args[1] is not a Player!");
                                    }
                                } else {
                                    $sender->sendMessage("[Usage] /bh inbounty {name} {bounty:int}");
                                }
                                return true;
                            break;
                        }

                    } else {
                        $cmd = array(
                            "/bh list",
                            "/bh setbounty",
                            "/bh inbounty"
                        );
                        foreach ($cmd as $c) {
                            $sender->sendMessage($c);
                        }

                    }

                    return true;
                break;
            }

        } else {
            $sender->sendMessage("No Perm!");
        }

    }
}