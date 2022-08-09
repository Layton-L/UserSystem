<?php

declare(strict_types=1);

namespace UserSystem\Layton\translation;

use UserSystem\Layton\UserSystem;

class TranslationManager {

    private string $languageName = "en";

    private array $translations = [];

    private QueryHelper $queryHelper;

    public function __construct(UserSystem $plugin) {
        foreach ($plugin->getResources() as $resource) {
            if ($resource->isFile()) {
                $filename = $resource->getFilename();
                if (str_starts_with($filename, "translation_") and str_ends_with($filename, ".json")) {
                    $decodeArray = json_decode(file_get_contents($resource->getPathname()), true);
                    $this->translations[$decodeArray["name"]] = $decodeArray;
                }
            }
        }

        $language = $plugin->getConfig()->get("language");
        if (array_key_exists($language, $this->translations)) {
            $this->languageName = $language;
        }

        $this->queryHelper = new QueryHelper($this->languageName, $this->translations);
    }

    public function getLanguageName(): string {
        return $this->languageName;
    }

    public function getQueryHelper(): QueryHelper {
        return $this->queryHelper;
    }

}