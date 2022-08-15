<?php

declare(strict_types = 1);

namespace UserSystem\Layton\form;

use jojoe77777\FormAPI\CustomForm;
use pocketmine\player\Player;
use UserSystem\Layton\event\UserLoginEvent;
use UserSystem\Layton\PasswordUtils;
use UserSystem\Layton\UserSystem;

class LoginForm extends CustomForm {

    private static array $tries = [];

    public static function setTries(Player $player, int $tries): void {
        self::$tries[strtolower($player->getName())] = $tries;
    }

    public static function getTries(Player $player): int {
        return self::$tries[strtolower($player->getName())];
    }

    public static function hasTries(Player $player): bool {
        return isset(self::$tries[strtolower($player->getName())]);
    }

    public function __construct(string $error = null) {
        $dataManager = UserSystem::getInstance()->getDataManager();
        $queryHelper = UserSystem::getInstance()->getTranslationManager()->getQueryHelper();

        parent::__construct(function (Player $player, array $data = null) use ($dataManager, $queryHelper) {
            if (!self::hasTries($player)) {
                self::setTries($player, 0);
            } else {
                if (self::getTries($player) === (int) UserSystem::getInstance()->getConfig()->get("max_tries")) {
                    self::setTries($player, 0);
                    $message = $queryHelper->getTranslatedString("module.login.timeout");
                    $player->kick($message, $message);
                }
            }

            if ($data === null) {
                $player->sendForm(new ExitForm());
                return;
            }

            $password = $data["password"];
            if ($password === null) {
                $player->sendForm(new LoginForm("module.login.form.input.empty"));
                self::setTries($player, self::getTries($player) + 1);
                return;
            }

            if (!PasswordUtils::verifyPassword($password, $dataManager->getPassword($player))) {
                $player->sendForm(new LoginForm("module.login.form.input.invalid"));
                self::setTries($player, self::getTries($player) + 1);
                return;
            }

            $event = new UserLoginEvent($player);
            $event->call();

            if ($event->isCancelled()) {
                $player->kick();
            }

            PasswordUtils::addHashPassword(PasswordUtils::getHashPassword($password));
            UserSystem::login($player);
            $player->sendMessage($queryHelper->getTranslatedString("module.login.message"));
        });
        $this->setTitle($queryHelper->getTranslatedString("module.login.form.title"));

        if ($error === null) {
            $this->addLabel($queryHelper->getTranslatedString("module.login.form.label"));
        } else {
            $this->addLabel($queryHelper->getTranslatedString($error));
        }
        $this->addInput($queryHelper->getTranslatedString("module.login.form.input.text"), $queryHelper->getTranslatedString("module.login.form.input.placeholder"), "", "password");
    }

}