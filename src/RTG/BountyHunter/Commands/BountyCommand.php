<?php
/**
 * Created by PhpStorm.
 * User: RTG
 * Date: 14/10/2017
 * Time: 5:14 PM
 */

namespace RTG\BountyHunter\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use RTG\BountyHunter\Loader;

class BountyCommand extends PluginCommand {

    public $plugin;

    public function __construct(Loader $plugin) {
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if ($sender->hasPermission("bounty.command")) {
            switch ($args[0]) {
                case "list":
                    $this->plugin->getAll($sender);
                    return true;
                break;
            }
        }
    }

}