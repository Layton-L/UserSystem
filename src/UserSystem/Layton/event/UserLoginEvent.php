<?php

declare(strict_types = 1);

namespace UserSystem\Layton\event;

use pocketmine\player\Player;

class UserLoginEvent extends UserEvent {

    public function __construct(Player $player) {
        parent::__construct($player);
    }

}