<?php

declare(strict_types = 1);

namespace UserSystem\Layton\event;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use pocketmine\player\Player;

abstract class UserEvent extends Event implements Cancellable {

    use CancellableTrait;

    public function __construct(private Player $player) {

    }

    public function getUser(): Player {
        return $this->player;
    }

}