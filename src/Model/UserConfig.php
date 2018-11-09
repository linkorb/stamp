<?php

namespace Stamp\Model;

class UserConfig
{
    const GITHUB_TOKEN_KEY = 'github_token';

    public function __construct()
    {
        umask(0077); // Don't allow the outside world to read any secrets!

        if (!is_dir($this->directory())) {
            if (!mkdir($this->directory(), 0700, true)) {
                throw new Exception('Config directory could not be created');
            }
        }

        if (!file_exists($this->settingsPath())) {
            file_put_contents($this->settingsPath(), '{}');
        }
    }

    public function getGithubToken() {
        return $this->getProperty(self::GITHUB_TOKEN_KEY);
    }

    public function setGithubToken($value) {
        $this->setProperty(self::GITHUB_TOKEN_KEY, $value);
    }

    private function getSettingsJson(): array {
        return json_decode(file_get_contents($this->settingsPath()), true);
    }

    private function setProperty(string $key, $value): void {
        $json = $this->getSettingsJson();
        $json[$key] = $value;
        file_put_contents(
            $this->settingsPath(),
            json_encode($json)
        );
    }

    private function getProperty(string $key) {
        $json = $this->getSettingsJson();
        
        if (isset($json[$key])) {
            return $json[$key];
        } else {
            return $null;
        }
    }

    private function settingsPath() {
        return $this->directory() . '/settings.json';
    }

    private function directory(): string
    {
        $dirname = 'LinkORB/Stamp';

        if (isset($_SERVER['LOCALAPPDATA'])) {
            return "{$_SERVER['LOCALAPPDATA']}/{$dirname}";
        } else if (isset($_SERVER['HOME'])) {
            return "{$_SERVER['HOME']}/.config/{$dirname}";
        } else if ($HOME = get_env('HOME')) {
            return "$HOME/.config/{$dirname}";
        } else {
            throw new Exception('Config directory could not be found');
        }
    }
}
