<?php

declare(strict_types = 1);

namespace UserSystem\Layton\event;

use pocketmine\player\Player;

class UserRegistrationEvent extends UserEvent {

    public function __construct(Player $player, private string $password) {
        parent::__construct($player);
    }

    public function getPassword(): string {
        return $this->password;
    }

}