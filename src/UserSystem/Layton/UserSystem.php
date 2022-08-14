<?php

declare(strict_types = 1);

namespace UserSystem\Layton;

use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use UserSystem\Layton\command\ChangePasswordCommand;
use UserSystem\Layton\event\EventHandler;
use UserSystem\Layton\provider\SQLite3Provider;
use UserSystem\Layton\translation\TranslationManager;

class UserSystem extends PluginBase {

    private static UserSystem $instance;

    private static array $users = [];

    private TranslationManager $translationManager;

    private DataManager $dataManager;

    public static function getInstance(): UserSystem {
        return self::$instance;
    }

    public static function isLogined(Player $player): bool {
        $name = strtolower($player->getName());
        if (isset(self::$users[$name])) {
            return self::$users[$name];
        }
        return false;
    }

    public static function login(Player $player): void {
        $name = strtolower($player->getName());
        self::$users[$name] = true;

        $player->setImmobile(false);
        $player->setGamemode(GameMode::SURVIVAL());
    }

    public function onLoad(): void {
        self::$instance = $this;
        $this->saveDefaultConfig();

        $provider = match ($this->getConfig()->get("provider")) {
          default => new SQLite3Provider($this),
        };

        $this->translationManager = new TranslationManager($this);
        $this->dataManager = new DataManager($provider);
        $this->registerCommands();
    }

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents(new EventHandler, $this);
    }

    private function registerCommands(): void {
        $queryHelper = $this->getTranslationManager()->getQueryHelper();
        $map = $this->getServer()->getCommandMap();

        $map->registerAll("UserSystem", [
            new ChangePasswordCommand("changepass", $queryHelper->getTranslatedString("command.changepass.description"))
        ]);
    }

    public function getTranslationManager(): TranslationManager {
        return $this->translationManager;
    }

    public function getDataManager(): DataManager {
        return $this->dataManager;
    }

}
