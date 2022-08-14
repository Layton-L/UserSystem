<?php

declare(strict_types = 1);

namespace UserSystem\Layton\provider;

use pocketmine\player\Player;
use SQLite3;
use UserSystem\Layton\UserSystem;

class SQLite3Provider implements Provider {

    private SQLite3 $database;

    public function __construct(UserSystem $plugin) {
        $this->database = new SQLite3($plugin->getDataFolder() . "users.db");

        $this->database->exec(stream_get_contents($plugin->getResource("schemas/users.sql")));
        $this->database->exec("PRAGMA journal_mode=WAL;");
        $this->database->exec("PRAGMA synchronous=OFF;");
    }

    public function isRegistered(Player $player): bool {
        $name = strtolower($player->getName());

        $result = $this->database->query("SELECT * FROM `users` WHERE `name` = '" . $name ."'");
        return !empty($result->fetchArray(SQLITE3_ASSOC));
    }

    public function register(Player $player, string $password): bool {
        $name = strtolower($player->getName());

        $statement = $this->database->prepare("INSERT INTO `users` (`name`, `password`) VALUES (:name, :password)");
        $statement->bindValue(":name", $name);
        $statement->bindValue(":password", $password);
        $statement->execute();

        return $this->database->changes() == 1;
    }

    public function getPassword(Player $player): string {
        $name = strtolower($player->getName());

        $result = $this->database->query("SELECT `password` FROM `users` WHERE `name` = '" . $name ."'");
        return $result->fetchArray(SQLITE3_ASSOC)["password"];
    }

    public function setPassword(Player $player, string $password): bool {
        $name = strtolower($player->getName());

        $statement = $this->database->prepare("UPDATE `users` SET `password` = :password WHERE `name` = :name");
        $statement->bindValue(":name", $name);
        $statement->bindValue(":password", $password);
        $statement->execute();

        return $this->database->changes() == 1;
    }

}
