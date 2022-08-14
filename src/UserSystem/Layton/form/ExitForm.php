<?php

declare(strict_types = 1);

namespace UserSystem\Layton\form;

use jojoe77777\FormAPI\ModalForm;
use pocketmine\player\Player;
use UserSystem\Layton\UserSystem;

class ExitForm extends ModalForm {

    public function __construct() {
        $dataManager = UserSystem::getInstance()->getDataManager();
        $queryHelper = UserSystem::getInstance()->getTranslationManager()->getQueryHelper();

        parent::__construct(function (Player $player, bool $exit = true) use ($dataManager, $queryHelper) {
            if ($exit) {
                $message = $queryHelper->getTranslatedString("module.exit.message");
                $player->kick($message, $message);
            } else {
                if (!$dataManager->isRegistered($player)) {
                    $player->sendForm(new RegistrationForm());
                    return;
                }

                if (!UserSystem::isLogined($player)) {
                    $player->sendForm(new LoginForm());
                }
            }
        });

        $this->setTitle($queryHelper->getTranslatedString("module.exit.form.title"));
        $this->setContent($queryHelper->getTranslatedString("module.exit.form.content"));
        $this->setButton1($queryHelper->getTranslatedString("module.exit.form.button1"));
        $this->setButton2($queryHelper->getTranslatedString("module.exit.form.button2"));
    }

}