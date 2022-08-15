<?php

declare(strict_types = 1);

namespace UserSystem\Layton\event;

use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerTransferEvent;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use UserSystem\Layton\form\LoginForm;
use UserSystem\Layton\form\RegistrationForm;
use UserSystem\Layton\PasswordUtils;
use UserSystem\Layton\UserSystem;

class EventHandler implements Listener {

    public function onEntityTeleport(EntityTeleportEvent $event): void {
        if (($player = $event->getEntity()) instanceof Player) {
            if (!UserSystem::isLogined($player)) {
                $event->cancel();
            }
        }
    }

    public function onPlayerChat(PlayerChatEvent $event): void {
        if (!UserSystem::isLogined($event->getPlayer())) {
            $event->cancel();
        }

        $event->setMessage(PasswordUtils::checkString($event->getMessage()));
    }

    public function onPlayerDropItem(PlayerDropItemEvent $event): void {
        if (!UserSystem::isLogined($event->getPlayer())) {
            $event->cancel();
        }
    }

    public function onPlayerInteract(PlayerInteractEvent $event): void {
        if (!UserSystem::isLogined($event->getPlayer())) {
            $event->cancel();
        }
    }

    public function onPlayerItemUse(PlayerItemUseEvent $event): void {
        if (!UserSystem::isLogined($event->getPlayer())) {
            $event->cancel();
        }
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $queryHelper = UserSystem::getInstance()->getTranslationManager()->getQueryHelper();

        $player->setImmobile(true);
        $player->setGamemode(GameMode::SPECTATOR());
        $player->teleport($player->getWorld()->getSafeSpawn($player->getPosition()));

        UserSystem::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player, $queryHelper) {
            if ($player->isOnline() && !UserSystem::isLogined($player)) {
                $message = $queryHelper->getTranslatedString("module.login.timeout");
                $player->kick($message, $message);
            }

            LoginForm::setTries($player, 0);
        }), 20 * (int) UserSystem::getInstance()->getConfig()->get("entry_time"));

        if (!UserSystem::getInstance()->getDataManager()->isRegistered($player)) {
            $player->sendForm(new RegistrationForm());
            return;
        }

        if (!UserSystem::isLogined($player)) {
            $player->sendForm(new LoginForm());
            return;
        }

        $event = new UserLoginEvent($player);
        $event->call();

        if ($event->isCancelled()) {
            $player->kick();
        }

        UserSystem::login($player);
        $player->sendMessage($queryHelper->getTranslatedString("module.login.message"));
    }

    public function onPlayerTransfer(PlayerTransferEvent $event): void {
        if (!UserSystem::isLogined($event->getPlayer())) {
            $event->cancel();
        }
    }

}