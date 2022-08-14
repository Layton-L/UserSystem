<?php

declare(strict_types = 1);

namespace UserSystem\Layton\form;

use jojoe77777\FormAPI\CustomForm;
use pocketmine\player\Player;
use UserSystem\Layton\event\UserLoginEvent;
use UserSystem\Layton\PasswordUtils;
use UserSystem\Layton\UserSystem;

class RegistrationForm extends CustomForm {

    public function __construct(string $error = null) {
        $dataManager = UserSystem::getInstance()->getDataManager();
        $queryHelper = UserSystem::getInstance()->getTranslationManager()->getQueryHelper();

        parent::__construct(function (Player $player, array $data = null) use ($dataManager, $queryHelper) {
            if ($data === null) {
                $player->sendForm(new ExitForm());
                return;
            }

            $password = $data["password"];
            if ($password === null) {
                $player->sendForm(new RegistrationForm("module.registration.form.input.empty"));
                return;
            }

            if (!preg_match("/^(?=\S+[0-9])(?=\S+[a-z])(?=\S+[A-Z])(?=\S+\W).{8,}$/", $password)) {
                $player->sendForm(new RegistrationForm("module.registration.form.input.invalid"));
                return;
            }

            $event = new UserLoginEvent($player);
            $event->call();

            if ($event->isCancelled()) {
                $player->kick();
            }

            if ($dataManager->register($player, $password)) {
                PasswordUtils::addHashPassword(PasswordUtils::getHashPassword($password));
                UserSystem::login($player);
                $player->sendMessage($queryHelper->getTranslatedString("module.registration.message.success"));
            } else {
                $message = $queryHelper->getTranslatedString("module.registration.message.error");
                $player->kick($message, $message);
            }
        });
        $this->setTitle($queryHelper->getTranslatedString("module.registration.form.title"));

        if ($error === null) {
            $this->addLabel($queryHelper->getTranslatedString("module.registration.form.label"));
        } else {
            $this->addLabel($queryHelper->getTranslatedString($error));
        }
        $this->addInput($queryHelper->getTranslatedString("module.registration.form.input.text"), $queryHelper->getTranslatedString("module.registration.form.input.placeholder"), "", "password");
    }

}