<?php

declare(strict_types=1);

namespace UserSystem\Layton\event;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\inventory\InventoryOpenEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBlockPickEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExperienceChangeEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerTransferEvent;
use pocketmine\event\server\CommandEvent;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use UserSystem\Layton\form\LoginForm;
use UserSystem\Layton\form\RegistrationForm;
use UserSystem\Layton\UserSystem;

class EventHandler implements Listener {

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $queryHelper = UserSystem::getInstance()->getTranslationManager()->getQueryHelper();

        $player->setImmobile(true);
        $player->setGamemode(GameMode::SPECTATOR());
        $player->teleport($player->getWorld()->getSafeSpawn($player->getPosition()));

        UserSystem::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player, $queryHelper) {
            if (!UserSystem::isLogined($player)) {
                $message = $queryHelper->getTranslatedString("module.login.timeout");
                $player->kick($message, $message);
            }
        }), 20 * 60);

        if (!UserSystem::getInstance()->getDataManager()->isRegistered($player)) {
            $player->sendForm(new RegistrationForm());
            return;
        }

        if (!UserSystem::isLogined($player)) {
            $player->sendForm(new LoginForm());
            return;
        }

        UserSystem::login($player);
        $player->sendMessage($queryHelper->getTranslatedString("module.login.message"));
    }

    public function onBlockBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();
        if (!UserSystem::isLogined($player)) {
            $event->cancel();
        }
    }

    public function onBlockPlace(BlockPlaceEvent $event): void {
        $player = $event->getPlayer();
        if (!UserSystem::isLogined($player)) {
            $event->cancel();
        }
    }

    public function onInventoryOpen(InventoryOpenEvent $event): void {
        $player = $event->getPlayer();
        if (!UserSystem::isLogined($player)) {
            $event->cancel();
        }
    }

    public function onPlayerBlockPick(PlayerBlockPickEvent $event): void {
        $player = $event->getPlayer();
        if (!UserSystem::isLogined($player)) {
            $event->cancel();
        }
    }

    public function onPlayerChat(PlayerChatEvent $event): void {
        $player = $event->getPlayer();
        if (!UserSystem::isLogined($player)) {
            $event->cancel();
        }
    }

    public function onPlayerDropItem(PlayerDropItemEvent $event): void {
        $player = $event->getPlayer();
        if (!UserSystem::isLogined($player)) {
            $event->cancel();
        }
    }

    public function onPlayerExperienceChange(PlayerExperienceChangeEvent $event): void {
        $player = $event->getEntity();
        if ($player instanceof Player && !UserSystem::isLogined($player)) {
            $event->cancel();
        }
    }

    public function onPlayerInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        if (!UserSystem::isLogined($player)) {
            $event->cancel();
        }
    }

    public function onPlayerItemUse(PlayerItemUseEvent $event): void {
        $player = $event->getPlayer();
        if (!UserSystem::isLogined($player)) {
            $event->cancel();
        }
    }

    public function onPlayerMove(PlayerMoveEvent $event): void {
        $player = $event->getPlayer();
        if (!UserSystem::isLogined($player)) {
            $event->cancel();
        }
    }

    public function onPlayerTransfer(PlayerTransferEvent $event): void {
        $player = $event->getPlayer();
        if (!UserSystem::isLogined($player)) {
            $event->cancel();
        }
    }

    public function onCommand(CommandEvent $event): void {
        $player = $event->getSender();
        if ($player instanceof Player && !UserSystem::isLogined($player)) {
            $event->cancel();
        }
    }

}