<?php

declare(strict_types = 1);

namespace UserSystem\Layton\provider;

use pocketmine\player\Player;

interface Provider {

    public function isRegistered(Player $player): bool;

    public function register(Player $player, string $password): bool;

    public function getPassword(Player $player): string;

    public function setPassword(Player $player, string $password): bool;

}