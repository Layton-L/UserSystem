<?php

declare(strict_types = 1);

namespace UserSystem\Layton\provider;

use Exception;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use UserSystem\Layton\UserSystem;

class ConfigProvider implements Provider {

    private Config $config;

    public function __construct(UserSystem $plugin, int $type) {
        $format = array_flip(Config::$formats)[$type];
        $this->config = new Config($plugin->getDataFolder() . "database." . $format, $type);
    }

    public function isRegistered(Player $player): bool {
        $name = strtolower($player->getName());

        return $this->config->exists($name);
    }

    public function register(Player $player, string $password): bool {
        $name = strtolower($player->getName());

        try {
            $this->config->set($name, $password);
            $this->config->save();
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }

    public function getPassword(Player $player): string {
        $name = strtolower($player->getName());

        return $this->config->get($name);
    }

    public function setPassword(Player $player, string $password): bool {
        $name = strtolower($player->getName());

        try {
            $this->config->set($name, $password);
            $this->config->save();
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }

}