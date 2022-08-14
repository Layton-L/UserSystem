<?php

declare(strict_types = 1);

namespace UserSystem\Layton\form;

use jojoe77777\FormAPI\CustomForm;
use pocketmine\player\Player;
use UserSystem\Layton\PasswordUtils;
use UserSystem\Layton\UserSystem;

class ChangePasswordForm extends CustomForm {

    public function __construct(string $error = null) {
        $dataManager = UserSystem::getInstance()->getDataManager();
        $queryHelper = UserSystem::getInstance()->getTranslationManager()->getQueryHelper();

        parent::__construct(function (Player $player, array $data = null) use ($dataManager, $queryHelper) {
            if ($data === null) return;

            $password = $data["password"];
            $newPassword = $data["new_password"];

            if ($password === null) {
                $player->sendForm(new ChangePasswordForm("module.changepass.form.input.password.empty"));
                return;
            }

            if ($newPassword === null) {
                $player->sendForm(new ChangePasswordForm("module.changepass.form.input.newpassword.empty"));
                return;
            }

            if (!PasswordUtils::verifyPassword($password, $dataManager->getPassword($player))) {
                $player->sendForm(new ChangePasswordForm("module.changepass.form.input.password.invalid"));
                return;
            }

            if (!preg_match("/^(?=\S+[0-9])(?=\S+[a-z])(?=\S+[A-Z])(?=\S+\W).{8,}$/", $newPassword)) {
                $player->sendForm(new ChangePasswordForm("module.changepass.form.input.newpassword.invalid"));
                return;
            }

            if ($password === $newPassword) {
                $player->sendForm(new ChangePasswordForm("module.changepass.form.input.equals"));
                return;
            }

            if ($dataManager->setPassword($player, $newPassword)) {
                PasswordUtils::addHashPassword(PasswordUtils::getHashPassword($newPassword));
                $player->sendMessage($queryHelper->getTranslatedString("module.changepass.message.success"));
            } else {
                $message = $queryHelper->getTranslatedString("module.changepass.message.error");
                $player->kick($message, $message);
            }
        });
        $this->setTitle($queryHelper->getTranslatedString("module.changepass.form.title"));

        if ($error === null) {
            $this->addLabel($queryHelper->getTranslatedString("module.changepass.form.label"));
        } else {
            $this->addLabel($queryHelper->getTranslatedString($error));
        }
        $this->addInput($queryHelper->getTranslatedString("module.changepass.form.input.password.text"), $queryHelper->getTranslatedString("module.changepass.form.input.password.placeholder"), "", "password");
        $this->addInput($queryHelper->getTranslatedString("module.changepass.form.input.newpassword.text"), $queryHelper->getTranslatedString("module.changepass.form.input.newpassword.placeholder"), "", "new_password");

    }

}