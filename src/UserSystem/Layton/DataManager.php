<?php

declare(strict_types=1);

namespace UserSystem\Layton;

use pocketmine\player\Player;
use UserSystem\Layton\event\UserChangePasswordEvent;
use UserSystem\Layton\event\UserRegistrationEvent;
use UserSystem\Layton\provider\Provider;

class DataManager {

    public function __construct(private Provider $provider) {

    }

    public function isRegistered(Player $player): bool {
        return $this->provider->isRegistered($player);
    }

    public function register(Player $player, string $password): bool {
        $password = password_hash(strtolower($password), PASSWORD_DEFAULT);
        $event = new UserRegistrationEvent($player, $password);

        $event->call();
        if (!$event->isCancelled()) {
            return $this->provider->register($player, $password);
        }
        return false;
    }

    public function getPassword(Player $player): string {
        return $this->provider->getPassword($player);
    }

    public function setPassword(Player $player, string $password): bool {
        $password = password_hash(strtolower($password), PASSWORD_DEFAULT);
        $event = new UserChangePasswordEvent($player, $password);

        $event->call();
        if (!$event->isCancelled()) {
            return $this->provider->setPassword($player, $password);
        }
        return false;
    }

}
