<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Stamp\Model\UserConfig;

final class UserConfigTest extends TestCase
{
    public function testGetterReturnsSetterValue(): void
    {
        $userConfig = new UserConfig();
        $tokenValue = 'LorumIpsemDolorSitAmet1234456';

        $userConfig->setGithubToken($tokenValue);

        $this->assertEquals($userConfig->getGithubToken(), $tokenValue);
    }
}
