<?php

declare(strict_types=1);

namespace UserSystem\Layton\form;

use jojoe77777\FormAPI\CustomForm;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use UserSystem\Layton\UserSystem;

class LoginForm extends CustomForm {

    private static array $tries = [];

    public function __construct(string $error = null) {
        $dataManager = UserSystem::getInstance()->getDataManager();
        $queryHelper = UserSystem::getInstance()->getTranslationManager()->getQueryHelper();

        parent::__construct(function (Player $player, array $data = null) use ($dataManager, $queryHelper) {
            $name = strtolower($player->getName());
            if (!isset(self::$tries[$name])) {
                self::$tries[$name] = 0;
            } else {
                if (self::$tries[$name] == 2) {
                    self::$tries[$name] = 0;
                    $message = $queryHelper->getTranslatedString("module.login.timeout");
                    $player->kick($message, $message);
                }
            }

            if ($data == null) {
                $player->sendForm(new ExitForm());
                return;
            }

            $password = $data["password"];
            if ($password == null) {
                $player->sendForm(new LoginForm("module.login.form.input.empty"));
                self::$tries[$name]++;
                return;
            }

            if (!UserSystem::isPasswordVerify($password, $dataManager->getPassword($player))) {
                $player->sendForm(new LoginForm("module.login.form.input.invalid"));
                self::$tries[$name]++;
                return;
            }

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