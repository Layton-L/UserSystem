<?php

declare(strict_types=1);

namespace UserSystem\Layton\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use UserSystem\Layton\form\ChangePasswordForm;
use UserSystem\Layton\UserSystem;

class ChangePasswordCommand extends Command {

    public const PERMISSION = "usersystem.changepass";

    public function __construct(string $name, string $description) {
        parent::__construct($name, $description);
        $this->setPermission(ChangePasswordCommand::PERMISSION);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!$sender instanceof Player || !$sender->hasPermission(ChangePasswordCommand::PERMISSION)) {
            return;
        }

        if (count($args) > 0) {
            $sender->sendMessage("/changepass");
            return;
        }

        $sender->sendForm(new ChangePasswordForm());
    }

}